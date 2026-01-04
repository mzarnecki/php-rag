<?php

namespace service;

interface GeneratedTextProviderInterface
{
    public function generateText(string $prompt, string $sourceDocuments = ''): string;

    public function setModel(string $model): self;
}
