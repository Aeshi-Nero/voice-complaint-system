<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->json('audio_paths')->nullable()->after('description');
        });

        // Migrate existing audio_path to audio_paths
        $complaints = DB::table('complaints')->whereNotNull('audio_path')->get();
        foreach ($complaints as $complaint) {
            DB::table('complaints')->where('id', $complaint->id)->update([
                'audio_paths' => json_encode([$complaint->audio_path])
            ]);
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('audio_path');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('description');
        });

        // Migrate back
        $complaints = DB::table('complaints')->whereNotNull('audio_paths')->get();
        foreach ($complaints as $complaint) {
            $paths = json_decode($complaint->audio_paths, true);
            if (!empty($paths)) {
                DB::table('complaints')->where('id', $complaint->id)->update([
                    'audio_path' => $paths[0]
                ]);
            }
        }

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('audio_paths');
        });
    }
};
