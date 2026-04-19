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

    public function containsProfanity(string $text, ?string $language = null): bool
    {
        if (empty($this->profanityWords)) {
            return false;
        }

        // Standardize text: Lowercase and remove excessive whitespace
        $text = mb_strtolower($text, 'UTF-8');
        
        foreach ($this->profanityWords as $word) {
            $word = strtolower($word);
            
            // 1. Exact/Word-Boundary match (e.g., "word")
            $pattern = "~\\b" . preg_quote($word, '~') . "\\b~iu";
            if (preg_match($pattern, $text)) {
                return true;
            }

            // 2. Obfuscated match (e.g., "w.0.r.d")
            $obfuscatedPattern = $this->generateObfuscatedPattern($word);
            if (preg_match($obfuscatedPattern, $text)) {
                return true;
            }
            
            // 3. Substring match for dangerous slurs (no word boundaries)
            // This catches "n1gg@" even if surrounded by other characters or symbols
            if (strlen($word) > 3) {
                // Generate a "loose" pattern that doesn't care about boundaries
                $loosePattern = $this->generateObfuscatedPattern($word, false);
                if (preg_match($loosePattern, $text)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    protected function generateObfuscatedPattern(string $word, bool $useBoundaries = true): string
    {
        $word = strtolower($word);
        $substitutions = [
            'a' => '[a4@\xc3\xa0\xc3\xa1\xc3\xa2\xc3\xa3\xc3\xa4\xc3\xa5]',
            'b' => '(?:b|8|\|3|i3|b\)|\|2)',
            'c' => '[c\(\<\{]',
            'd' => '(?:d|cl|\| \)|\| \]|d\)|o\|)',
            'e' => '[e3\xc3\xa8\xc3\xa9\xc3\xaa\xc3\xab]',
            'f' => '(?:f|ph|ff|v)',
            'g' => '[g96]',
            'h' => '(?:h|\|-\||#|}{)',
            'i' => '[i1!\|l]',
            'j' => '[j\]]',
            'k' => '(?:k|\|<|i<|\|\{)',
            'l' => '[l1\|i]',
            'm' => '(?:m|nn|rn|\^\|\||\|\^\|)',
            'n' => '(?:n|\|\||\\\\|\\\\|\\\\|\\\\|\^/)',
            'o' => '[o0\xc3\xb2\xc3\xb3\xc3\xb4\xc3\xb5\xc3\xb6\xc3\xb8\(\)]',
            'p' => '(?:p|\|2|\|\^|\|o)',
            'q' => '[q9k]',
            'r' => '(?:r|\|2|\|z|i2)',
            's' => '[s5\$z]',
            't' => '[t7\+]',
            'u' => '[u\^v\|_\|]',
            'v' => '(?:v|\\\\\\\/)',
            'w' => '(?:w|vv|vv)',
            'x' => '[x\>\<][\)\(]',
            'y' => '[y4\xc3\xbf]',
            'z' => '[z2s]',
        ];

        $patternParts = [];
        for ($i = 0; $i < mb_strlen($word); $i++) {
            $char = mb_substr($word, $i, 1);
            $patternParts[] = $substitutions[$char] ?? preg_quote($char, '~');
        }

        // Allow for optional characters between letters (e.g., f.u.c.k)
        $boundary = $useBoundaries ? '\\b' : '';
        $pattern = '~' . $boundary . implode('[\s\._-]*', $patternParts) . $boundary . '~iu';
        
        return $pattern;
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
