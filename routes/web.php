<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintModerationController;
use App\Http\Controllers\Admin\PollController as AdminPollController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintMessageController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

Route::get("/", function () {
    return redirect("/login");
});

Route::get("/migrate", function() {
    try {
        $output = "";
        
        // 1. Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $output .= "Migrate output: " . Artisan::output() . "\n\n";
        
        // 2. Manual Fallback: Check if column exists, if not add it
        if (!Schema::hasColumn('users', 'profile_image')) {
            $output .= "Column 'profile_image' missing. Attempting manual addition...\n";
            Schema::table('users', function ($table) {
                $table->string('profile_image')->nullable()->after('course');
            });
            $output .= "Manual addition successful!\n";
        } else {
            $output .= "Column 'profile_image' already exists.\n";
        }

        // 3. Manual Fallback: Create complaint_messages table if it doesn't exist
        if (!Schema::hasTable('complaint_messages')) {
            $output .= "Table 'complaint_messages' missing. Attempting manual creation...\n";
            Schema::create('complaint_messages', function ($table) {
                $table->id();
                $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('message');
                $table->boolean('is_admin')->default(false);
                $table->timestamps();
            });
            $output .= "Table 'complaint_messages' created successfully!\n";
        } else {
            $output .= "Table 'complaint_messages' already exists.\n";
        }

        // 4. Manual Fallback: Add images column to complaint_messages
        if (Schema::hasTable('complaint_messages') && !Schema::hasColumn('complaint_messages', 'images')) {
            $output .= "Column 'images' missing in 'complaint_messages'. Attempting manual addition...\n";
            Schema::table('complaint_messages', function ($table) {
                $table->json('images')->nullable()->after('message');
            });
            $output .= "Column 'images' added successfully!\n";
        }

        // 5. Manual Fallback: Add banned_until to users
        if (!Schema::hasColumn('users', 'banned_until')) {
            $output .= "Column 'banned_until' missing in 'users'. Attempting manual addition...\n";
            Schema::table('users', function ($table) {
                $table->timestamp('banned_until')->nullable()->after('is_blocked');
            });
            $output .= "Column 'banned_until' added successfully!\n";
        }
        
        return nl2br($output);
    } catch (\Exception $e) {
        return "Migration/Fix failed: " . $e->getMessage();
    }
});

Route::get("/import-users", function() {
    try {
        $sharedStringsPath = base_path('sharedStrings.xml');
        $sheetPath = base_path('sheet1.xml');

        if (!file_exists($sharedStringsPath) || !file_exists($sheetPath)) {
            return "XML files missing in root.";
        }

        // 1. Load shared strings
        $sharedStrings = [];
        $xml = simplexml_load_file($sharedStringsPath);
        foreach ($xml->si as $si) {
            $sharedStrings[] = (string) $si->t;
        }

        // 2. Parse sheet
        $sheetXml = simplexml_load_file($sheetPath);
        $usersImported = 0;
        $errors = [];

        foreach ($sheetXml->sheetData->row as $row) {
            $rowIndex = (int) $row['r'];
            if ($rowIndex < 9) continue; // Skip headers

            $rowData = [];
            foreach ($row->c as $c) {
                $r = (string) $c['r'];
                $col = preg_replace('/[0-9]/', '', $r);
                $type = (string) $c['t'];
                $val = (string) $c->v;

                if ($type == 's') {
                    $rowData[$col] = $sharedStrings[(int)$val] ?? '';
                } else {
                    $rowData[$col] = $val;
                }
            }

            $idNumber = $rowData['B'] ?? null;
            $lastName = $rowData['C'] ?? '';
            $firstName = $rowData['D'] ?? '';
            $course = $rowData['G'] ?? '';
            $section = $rowData['J'] ?? '';

            if ($idNumber && $firstName) {
                \App\Models\User::updateOrCreate(
                    ['id_number' => $idNumber],
                    [
                        'name' => trim("$firstName $lastName"),
                        'email' => strtolower(str_replace('-', '', $idNumber)) . "@student.com",
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'role' => 'student',
                        'course' => $course . ($section ? " ($section)" : ""),
                        'is_blocked' => false,
                    ]
                );
                $usersImported++;
            }
        }

        return "Successfully imported $usersImported users. Default password is 'password'.";
    } catch (\Exception $e) {
        return "Import failed: " . $e->getMessage();
    }
});

// Auth Routes
Route::get("/login", [LoginController::class, "showLoginForm"])->name("login");
Route::post("/login", [LoginController::class, "login"]);
Route::post("/logout", [LoginController::class, "logout"])->name("logout");

// Profile Routes
Route::post("/profile/update", [ProfileController::class, "update"])->name("profile.update")->middleware('auth');

// Message Routes
Route::middleware(["auth"])->group(function () {
    Route::post("/complaints/{complaint}/messages", [ComplaintMessageController::class, "store"])->name("complaints.messages.store");
    Route::get("/complaints/{complaint}/messages", [ComplaintMessageController::class, "getMessages"])->name("complaints.messages.get");
});

// User Routes
Route::middleware(["auth"])->prefix("user")->name("user.")->group(function () {
    Route::get("/dashboard", [ComplaintController::class, "dashboard"])->name("dashboard");
    Route::get("/polls", [ComplaintController::class, "polls"])->name("polls");
    Route::get("/polls/{poll}/report", [ComplaintController::class, "pollReport"])->name("polls.report");
    Route::post("/polls/{poll}/vote", [VoteController::class, "vote"])->name("polls.vote");
    Route::resource("complaints", ComplaintController::class);
});

// Admin Routes
Route::middleware(["auth", \App\Http\Middleware\AdminMiddleware::class])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/dashboard", [AdminDashboardController::class, "index"])->name("dashboard");
    
    // Complaint Moderation
    Route::get("/complaints", [ComplaintModerationController::class, "index"])->name("complaints");
    Route::get("/complaints/{complaint}", [ComplaintModerationController::class, "show"])->name("complaints.show");
    Route::post("/complaints/{complaint}/update", [ComplaintModerationController::class, "update"])->name("complaints.update");
    Route::post("/complaints/{complaint}/resolve", [ComplaintModerationController::class, "resolve"])->name("complaints.resolve");
    Route::post("/users/{user}/block", [ComplaintModerationController::class, "blockUser"])->name("users.block");
    Route::post("/users/{user}/unblock", [ComplaintModerationController::class, "unblockUser"])->name("users.unblock");
    Route::post("/users/{user}/quick-ban", [ComplaintModerationController::class, "quickBan"])->name("users.quick_ban");

    // User Management
    Route::get("/users", [\App\Http\Controllers\Admin\UserController::class, "index"])->name("users.index");
    Route::post("/users/import", [\App\Http\Controllers\Admin\UserController::class, "import"])->name("users.import");

    // Admin Poll Routes
    Route::get("/polls", [AdminPollController::class, "index"])->name("polls.index");
    Route::get("/polls/create", [AdminPollController::class, "create"])->name("polls.create");
    Route::post("/polls", [AdminPollController::class, "store"])->name("polls.store");
    Route::post("/polls/{poll}/close", [AdminPollController::class, "close"])->name("polls.close");
    Route::delete("/polls/{poll}", [AdminPollController::class, "destroy"])->name("polls.destroy");

    // Admin Report Routes
    Route::get("/reports", [\App\Http\Controllers\Admin\ReportController::class, "index"])->name("reports.index");
    Route::get("/reports/export/csv", [\App\Http\Controllers\Admin\ReportController::class, "exportCsv"])->name("reports.export.csv");
    Route::get("/reports/export/pdf", [\App\Http\Controllers\Admin\ReportController::class, "exportPdf"])->name("reports.export.pdf");
    Route::get("/reports/polls/export/csv", [\App\Http\Controllers\Admin\ReportController::class, "exportPollsCsv"])->name("reports.polls.csv");
    Route::get("/reports/polls/export/pdf", [\App\Http\Controllers\Admin\ReportController::class, "exportPollsPdf"])->name("reports.polls.pdf");
});
