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
        $this->assertEquals(mb_substr($chunks[0], mb_strlen($chunks[0], 'UTF-8') - 100, 100, 'UTF-8'), mb_substr($chunks[1], 0, 100, 'UTF-8'));
    }

    public function testTextSplitter_splitsToExpectedNumberOfChunks(): void
    {
        $textSplitter = new TextSplitter();
        $text = $this->getLoremIpsum();
        $length = (int) ceil(mb_strlen($text, 'UTF-8') / 190);
        $chunks = $textSplitter->splitDocumentIntoChunks($text, 200, 10);
        $this->assertEquals($length, count($chunks));
    }

    public function getLoremIpsum(): string
    {
        return (string) file_get_contents(__DIR__.'/lorem-ipsum.txt');
    }
}
