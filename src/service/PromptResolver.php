<?php

declare(strict_types=1);

namespace service;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;

final class PromptResolver implements StageInterface
{
    public function getPromptFromInput(): string
    {
        $prompt = $_POST['prompt'] ?? null;
        if (! $prompt) {
            global $argv;
            $prompt = $argv[0] ?? null;
        }
        if (! $prompt) {
            $prompt = 'What is the result of 2 + 2?';
        }

        return $this->getSystemPrompt().$prompt;
    }

    /**
     * @param  Payload  $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        return $payload->setPrompt($this->getPromptFromInput());
    }

    private function getSystemPrompt(): string
    {
        return 'You are a helpful assistant that answers questions based on source documents.'.PHP_EOL;
    }
}
