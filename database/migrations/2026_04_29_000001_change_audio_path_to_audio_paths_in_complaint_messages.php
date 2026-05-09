<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->json('audio_paths')->nullable()->after('images');
        });

        // Migrate existing audio_path to audio_paths
        $messages = DB::table('complaint_messages')->whereNotNull('audio_path')->get();
        foreach ($messages as $message) {
            DB::table('complaint_messages')->where('id', $message->id)->update([
                'audio_paths' => json_encode([$message->audio_path])
            ]);
        }

        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->dropColumn('audio_path');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('images');
        });

        // Migrate back
        $messages = DB::table('complaint_messages')->whereNotNull('audio_paths')->get();
        foreach ($messages as $message) {
            $paths = json_decode($message->audio_paths, true);
            if (!empty($paths)) {
                DB::table('complaint_messages')->where('id', $message->id)->update([
                    'audio_path' => $paths[0]
                ]);
            }
        }

        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->dropColumn('audio_paths');
        });
    }
};
