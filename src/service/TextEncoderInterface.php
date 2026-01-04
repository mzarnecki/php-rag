<?php

namespace service;

interface TextEncoderInterface
{
    /**
     * @return string[] chunks with embeddings
     */
    public function getEmbeddings(string $document): array;
}
