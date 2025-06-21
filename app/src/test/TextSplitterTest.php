<?php

declare(strict_types=1);

namespace test;

use PHPUnit\Framework\TestCase;
use service\TextSplitter;

class TextSplitterTest extends TestCase
{
    public function testTextSplitter_keepsCommonPartBetweenChunks(): void
    {
        $textSplitter = new TextSplitter();
        $chunks = $textSplitter->splitDocumentIntoChunks($this->getLoremIpsum(), 300, 100);
        $this->assertEquals(substr((string) $chunks[0], strlen((string) $chunks[0]) - 100), substr((string) $chunks[1], 0, 100));
    }

    public function testTextSplitter_splitsToExpectedNumberOfChunks(): void
    {
        $textSplitter = new TextSplitter();
        $text = $this->getLoremIpsum();
        $length = (int) ceil(strlen($text) / 190);
        $chunks = $textSplitter->splitDocumentIntoChunks($text, 200, 10);
        $this->assertEquals($length, count($chunks));
    }

    public function getLoremIpsum(): string
    {
        return file_get_contents(__DIR__.'/lorem-ipsum.txt');
    }
}
