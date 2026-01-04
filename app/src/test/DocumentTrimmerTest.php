<?php

declare(strict_types=1);

namespace test;

use PHPUnit\Framework\TestCase;
use service\DocumentTrimmer;

class DocumentTrimmerTest extends TestCase
{
    private DocumentTrimmer $trimmer;

    protected function setUp(): void
    {
        $this->trimmer = new DocumentTrimmer();
    }

    public function testTrimReturnsOriginalStringIfBelowThreshold(): void
    {
        $shortText = 'This is a short document.';
        $result = $this->trimmer->trim($shortText);

        $this->assertEquals($shortText, $result);
    }

    public function testTrimCutsDocumentTo6000WordsWhenOverThreshold(): void
    {
        // The threshold is 0.75 * 8000 = 6000 letters/tokens (via preg_match_all)
        // Creating a string with 7000 words (mostly letters) to trigger the trim
        $longText = str_repeat('word ', 7000);
        $result = $this->trimmer->trim(trim($longText));

        $wordCount = count(explode(' ', $result));

        // Should be trimmed to exactly 6000 words
        $this->assertEquals(6000, $wordCount);

        $this->assertStringStartsWith('word word word', $result);
    }
}
