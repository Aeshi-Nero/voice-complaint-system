<?php

namespace App\Services;

use App\Models\ProfanityWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfanityService
{
    protected $profanityWords;
    protected $perspectiveApiKey;
    protected $perspectiveThreshold;
    protected $openaiApiKey;
    protected $openaiThreshold;

    public function __construct()
    {
        $this->perspectiveApiKey = config('services.perspective.key') ?? env('PERSPECTIVE_API_KEY');
        $this->perspectiveThreshold = config('services.perspective.threshold') ?? env('PERSPECTIVE_THRESHOLD', 0.7);
        $this->openaiApiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $this->openaiThreshold = env('OPENAI_MODERATION_THRESHOLD', 0.1);

        // Keep local words as fallback
        try {
            $this->profanityWords = Cache::remember('profanity_words', 3600, function () {
                return $this->getWordsFromDatabase();
            });
        } catch (\Exception $e) {
            $this->profanityWords = $this->getWordsFromDatabase();
        }
    }

    protected function getWordsFromDatabase(): array
    {
        try {
            return ProfanityWord::pluck('word')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if text contains profanity using multiple AI APIs with local fallback.
     */
    public function containsProfanity(string $text): bool
    {
        if (empty(trim($text))) {
            return false;
        }

        // 1. Try OpenAI Moderation (Best for Dialects & 2026 Ready)
        if ($this->openaiApiKey) {
            try {
                $response = Http::withToken($this->openaiApiKey)
                    ->post("https://api.openai.com/v1/moderations", [
                        'input' => $text,
                        'model' => 'omni-moderation-latest'
                    ]);

                if ($response->successful()) {
                    $result = $response->json('results.0');
                    if ($result['flagged'] ?? false) {
                        Log::info("OpenAI flagged text as inappropriate.");
                        return true;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("OpenAI Moderation failed: " . $e->getMessage());
            }
        }

        // 2. Try Perspective API (Fallback AI)
        if ($this->perspectiveApiKey) {
            try {
                $response = Http::post("https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key={$this->perspectiveApiKey}", [
                    'comment' => ['text' => $text],
                    'languages' => ['en', 'tl'],
                    'requestedAttributes' => [
                        'TOXICITY' => (object)[],
                        'PROFANITY' => (object)[],
                        'INSULT' => (object)[],
                    ]
                ]);

                if ($response->successful()) {
                    $scores = $response->json('attributeScores');
                    foreach ($scores as $attribute => $data) {
                        $score = $data['summaryScore']['value'] ?? 0;
                        if ($score >= $this->perspectiveThreshold) {
                            Log::info("Perspective API flagged text. Attribute: {$attribute}, Score: {$score}");
                            return true;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Perspective API failed: " . $e->getMessage());
            }
        }

        // 3. Fallback to Local List Check
        return $this->localCheck($text);
    }

    /**
     * Original list-based profanity check.
     */
    protected function localCheck(string $text): bool
    {
        if (empty($this->profanityWords)) {
            return false;
        }

        $text = mb_strtolower($text, 'UTF-8');
        $normalizedText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        $substitutions = [
            '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a', '5' => 's', '7' => 't', '8' => 'b', '@' => 'a', '$' => 's', '+' => 't'
        ];
        $substitutedText = strtr($normalizedText, $substitutions);
        
        foreach ($this->profanityWords as $word) {
            $word = strtolower($word);
            
            if (str_contains($normalizedText, $word) || str_contains($substitutedText, $word)) {
                return true;
            }

            $pattern = "~\\b" . preg_quote($word, '~') . "\\b~iu";
            if (preg_match($pattern, $text)) {
                return true;
            }

            $obfuscatedPattern = $this->generateObfuscatedPattern($word);
            if (preg_match($obfuscatedPattern, $text)) {
                return true;
            }
            
            if (strlen($word) > 3) {
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
        }
        $this->profanityWords = $this->getWordsFromDatabase();
    }
}

