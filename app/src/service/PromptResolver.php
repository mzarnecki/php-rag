<?php
declare(strict_types=1);

namespace service;

use service\pipeline\Payload;
use League\Pipeline\StageInterface;

final class PromptResolver implements StageInterface
{
    public function getPromptFromInput(): string
    {
        $prompt = $this->getSystemPrompt();
        $prompt .= $_POST['prompt'] ?? null;
        if (! $prompt) {
            $prompt = $argv[0] ?? null;
        }
        if (! $prompt) {
            $prompt = 'What is the result of 2 + 2?';
        }
        return $prompt;
    }

    /**
     * @param Payload $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        return $payload->setPrompt($this->getPromptFromInput());
    }

    private function getSystemPrompt(): string
    {
        return 'You are a helpful assistant that answers questions based on source documents.' . PHP_EOL;
    }
}