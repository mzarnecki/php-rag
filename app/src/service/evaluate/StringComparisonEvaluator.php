<?php

namespace service\evaluate;

class StringComparisonEvaluator
{
    public function calculateBLEU(string $reference, string $candidate, $n = 1): float
    {
        $candidateWords = explode(' ', $candidate);
        $referenceWords = explode(' ', $reference);
        $candidateLength = count($candidateWords);
        $referenceLength = count($referenceWords);

        $nGramMatches = [];
        for ($i = 1; $i <= $n; $i++) {
            $candidateNGrams = $this->getNGrams($candidateWords, $i);
            $referenceNGrams = $this->getNGrams($referenceWords, $i);

            $matches = 0;
            foreach ($candidateNGrams as $ngram) {
                if (in_array($ngram, $referenceNGrams)) {
                    $matches++;
                }
            }
            $nGramMatches[$i] = $matches / max(is_countable($candidateNGrams) ? count($candidateNGrams) : 0, 1);
        }

        $precision = array_product($nGramMatches);
        $brevityPenalty = ($candidateLength > $referenceLength)
            ? 1
            : exp(1 - ($referenceLength / max($candidateLength, 1)));

        return round($brevityPenalty * $precision ** (1 / $n), 2);
    }

    /**
     * @return array{recall: float, precision: float, f1: float}
     */
    public function calculateROUGE(string $reference, string $candidate, int $n = 1): array
    {
        $candidateWords = explode(' ', $candidate);
        $referenceWords = explode(' ', $reference);

        $candidateNGrams = $this->getNGrams($candidateWords, $n);
        $referenceNGrams = $this->getNGrams($referenceWords, $n);

        $matches = 0;
        foreach ($candidateNGrams as $ngram) {
            if (in_array($ngram, $referenceNGrams)) {
                $matches++;
            }
        }

        $recall = $matches / max(is_countable($referenceNGrams) ? count($referenceNGrams) : 0, 1);
        $precision = $matches / max(is_countable($candidateNGrams) ? count($candidateNGrams) : 0, 1);
        $f1Score = ($recall + $precision > 0)
            ? 2 * ($recall * $precision) / ($recall + $precision)
            : 0;

        return [
            'recall' => round($recall, 2),
            'precision' => round($precision, 2),
            'f1' => round($f1Score, 2),
        ];
    }

    /**
     * @return string[]
     */
    private function getNGrams(array $words, int $n): array
    {
        $nGrams = [];
        $wordsCount = count($words);
        for ($i = 0; $i <= $wordsCount - $n; $i++) {
            $nGrams[] = implode(' ', array_slice($words, $i, $n));
        }

        return $nGrams;
    }
}
