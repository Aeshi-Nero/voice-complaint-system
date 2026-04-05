<?php

namespace App\Services;

use App\Models\ProfanityWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ProfanityService
{
    protected $profanityWords;

    public function __construct()
    {
        // We use a try-catch and check for the cache table to avoid crashes if the database cache is used but not migrated
        try {
            $this->profanityWords = Cache::remember('profanity_words', 3600, function () {
                return $this->getWordsFromDatabase();
            });
        } catch (\Exception $e) {
            // Fallback to direct database query if cache fails (e.g., missing table)
            $this->profanityWords = $this->getWordsFromDatabase();
        }
    }

    protected function getWordsFromDatabase(): array
    {
        try {
            return ProfanityWord::pluck('word')->toArray();
        } catch (\Exception $e) {
            return []; // Fallback to empty if even DB fails
        }
    }

    public function containsProfanity(string $text): bool
    {
        if (empty($this->profanityWords)) {
            return false;
        }

        $lowerText = strtolower($text);
        
        foreach ($this->profanityWords as $word) {
            if (str_contains($lowerText, strtolower($word))) {
                return true;
            }
        }
        
        return false;
    }

    public function getProfanityWords(): array
    {
        return $this->profanityWords;
    }

    public function refreshCache(): void
    {
        try {
            Cache::forget('profanity_words');
        } catch (\Exception $e) {
            // Ignore cache failures
        }
        $this->profanityWords = $this->getWordsFromDatabase();
    }
}
