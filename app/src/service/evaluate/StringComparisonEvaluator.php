<?php

namespace service\evaluate;

class StringComparisonEvaluator
{
    public function calculateBLEU(string $reference, string $candidate, $n = 1)
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
            $nGramMatches[$i] = $matches / max(count($candidateNGrams), 1);
        }

        $precision = array_product($nGramMatches);
        $brevityPenalty = ($candidateLength > $referenceLength)
            ? 1
            : exp(1 - ($referenceLength / max($candidateLength, 1)));

        return round($brevityPenalty * pow($precision, 1 / $n), 2);
    }

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

        $recall = $matches / max(count($referenceNGrams), 1);
        $precision = $matches / max(count($candidateNGrams), 1);
        $f1Score = ($recall + $precision > 0)
            ? 2 * ($recall * $precision) / ($recall + $precision)
            : 0;

        return [
            'recall' => round($recall, 2),
            'precision' => round($precision, 2),
            'f1' => round($f1Score, 2)
        ];
    }

    private function getNGrams($words, $n) {
        $nGrams = [];
        for ($i = 0; $i <= count($words) - $n; $i++) {
            $nGrams[] = implode(' ', array_slice($words, $i, $n));
        }
        return $nGrams;
    }
}