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
        $length = strlen($document);
        $start = 0;

        while ($start < $length) {
            $end = min($start + $chunkSize, $length);
            $chunks[] = substr($document, $start, $end - $start);
            $start += ($chunkSize - $overlap);
        }

        return $chunks;
    }
}
