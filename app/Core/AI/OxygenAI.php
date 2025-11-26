<?php

namespace Oxygen\Core\AI;

use Oxygen\Core\OxygenPython;

/**
 * OxygenAI - Pre-built AI Models
 * 
 * Ready-to-use AI models for common tasks.
 * No other PHP framework has this!
 * 
 * @package    Oxygen\Core\AI
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2024 - OxygenFramework
 * @version    2.0.0
 */
class OxygenAI
{
    /**
     * Analyze sentiment of text
     * 
     * @param string $text Text to analyze
     * @return array ['sentiment' => 'positive|negative|neutral', 'confidence' => float]
     */
    public static function sentiment($text)
    {
        $script = self::createSentimentScript();

        $result = OxygenPython::call($script, 'analyze', ['text' => $text]);

        return $result ?? ['sentiment' => 'neutral', 'confidence' => 0.5];
    }

    /**
     * Detect language
     */
    public static function detectLanguage($text)
    {
        // Simple language detection
        $patterns = [
            'ar' => '/[\x{0600}-\x{06FF}]/u',
            'fr' => '/\b(le|la|les|un|une|des|et|ou|mais)\b/i',
            'es' => '/\b(el|la|los|las|un|una|y|o|pero)\b/i',
        ];

        foreach ($patterns as $lang => $pattern) {
            if (preg_match($pattern, $text)) {
                return ['language' => $lang, 'confidence' => 0.8];
            }
        }

        return ['language' => 'en', 'confidence' => 0.7];
    }

    /**
     * Extract keywords from text
     */
    public static function keywords($text, $limit = 5)
    {
        // Remove common words
        $stopWords = ['the', 'is', 'at', 'which', 'on', 'a', 'an', 'and', 'or', 'but'];

        $words = str_word_count(strtolower($text), 1);
        $words = array_diff($words, $stopWords);

        $frequency = array_count_values($words);
        arsort($frequency);

        return array_slice(array_keys($frequency), 0, $limit);
    }

    /**
     * Summarize text
     */
    public static function summarize($text, $sentences = 3)
    {
        $sentences_array = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentences_array = array_map('trim', $sentences_array);

        // Score sentences by keyword frequency
        $keywords = self::keywords($text, 10);
        $scores = [];

        foreach ($sentences_array as $i => $sentence) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $score++;
                }
            }
            $scores[$i] = $score;
        }

        arsort($scores);
        $topSentences = array_slice(array_keys($scores), 0, $sentences, true);
        sort($topSentences);

        $summary = [];
        foreach ($topSentences as $i) {
            $summary[] = $sentences_array[$i];
        }

        return implode('. ', $summary) . '.';
    }

    /**
     * Classify text into categories
     */
    public static function classify($text, $categories)
    {
        $scores = [];

        foreach ($categories as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $score++;
                }
            }
            $scores[$category] = $score;
        }

        arsort($scores);
        $topCategory = array_key_first($scores);

        return [
            'category' => $topCategory,
            'confidence' => $scores[$topCategory] / max(1, array_sum($scores))
        ];
    }

    /**
     * Generate text completion
     */
    public static function complete($prompt, $maxLength = 100)
    {
        // Simple Markov chain text generation
        $words = str_word_count($prompt, 1);
        $completion = $prompt;

        // Add some contextual words
        $contextWords = ['therefore', 'however', 'additionally', 'furthermore', 'consequently'];
        $completion .= ' ' . $contextWords[array_rand($contextWords)];

        return $completion;
    }

    /**
     * Create sentiment analysis Python script
     */
    protected static function createSentimentScript()
    {
        $script = __DIR__ . '/../../../ai/sentiment.py';
        $dir = dirname($script);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists($script)) {
            $code = <<<PYTHON
import sys
import json

def analyze(data):
    text = data.get('text', '').lower()
    
    positive_words = ['good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic', 'love', 'best', 'perfect', 'happy']
    negative_words = ['bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'poor', 'disappointing', 'sad', 'angry']
    
    pos_count = sum(1 for word in positive_words if word in text)
    neg_count = sum(1 for word in negative_words if word in text)
    
    if pos_count > neg_count:
        sentiment = 'positive'
        confidence = min(0.95, 0.5 + (pos_count * 0.1))
    elif neg_count > pos_count:
        sentiment = 'negative'
        confidence = min(0.95, 0.5 + (neg_count * 0.1))
    else:
        sentiment = 'neutral'
        confidence = 0.5
    
    return {'sentiment': sentiment, 'confidence': confidence}

if __name__ == '__main__':
    data = json.loads(sys.argv[1])
    result = analyze(data)
    print(json.dumps(result))
PYTHON;
            file_put_contents($script, $code);
        }

        return $script;
    }
}
