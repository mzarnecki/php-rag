<?php

declare(strict_types=1);

namespace service;

final class DocumentTrimmer
{
    /**
     * //cut document to tokens limit 8192
     */
    public function trim(string $document): string
    {
        if (preg_match_all('/\p{L}+/u', $document) >= 0.75 * 8000) {
            $words = explode(' ', $document);

            return implode(' ', array_slice($words, 0, 6000));
        }

        return $document;
    }
}
