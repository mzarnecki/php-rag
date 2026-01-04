<?php

namespace service;

class TextSplitter
{
    /**
     * @return string[]
     */
    public function splitDocumentIntoChunks(string $document, int $chunkSize, int $overlap): array
    {
        $chunks = [];
        $length = mb_strlen($document, 'UTF-8');
        $start = 0;

        while ($start < $length) {
            $end = min($start + $chunkSize, $length);
            $chunks[] = mb_substr($document, $start, $end - $start, 'UTF-8');
            $start += ($chunkSize - $overlap);
        }

        return $chunks;
    }
}
