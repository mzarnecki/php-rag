<?php
declare(strict_types=1);

namespace test;

use PHPUnit\Framework\TestCase;
use service\evaluate\TokenBasedSimilarityEvaluator;


class TokenBasedSimilarityEvaluatorTest extends TestCase
{
    public function testCalculateRouge(): void
    {
        $reference = "that's the way cookie crumbles";
        $candidate = "this is the way cookie is crashed";

        $rougeScores = (new TokenBasedSimilarityEvaluator())->calculateROUGE($reference, $candidate);

        $this->assertEquals([
                'precision' => 0.43,
                'recall' => 0.60,
                'f1' => 0.50
            ],
            $rougeScores
        );
    }

    public function testCalculateBleu(): void
    {
        $reference = "that's the way cookie crumbles";
        $candidate = "this is the way cookie is crashed";

        $bleuScore = (new TokenBasedSimilarityEvaluator())->calculateBleu($reference, $candidate, 1);

        $this->assertEquals(0.43, $bleuScore);
    }
}