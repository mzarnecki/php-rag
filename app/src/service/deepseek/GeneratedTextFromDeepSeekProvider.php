<?php

namespace service\deepseek;

use service\GeneratedTextProviderInterface;
use League\Pipeline\StageInterface;

final class GeneratedTextFromDeepSeekProvider extends AbstractDeepSeekAPIClient implements StageInterface, GeneratedTextProviderInterface
{
    private string $model = 'deepseek-chat';

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $sourceDocuments . "\n\n" . $prompt],
        ];

        $response = $this->request(
            messages: $messages,
            model: $this->model,
            stream: false
        );

        return $response['choices'][0]['message']['content'] ?? '';
    }

    public function __invoke($payload)
    {
        return $this->generateText(
            $payload->getPrompt(),
            $payload->getRagPrompt()
        );
    }
}