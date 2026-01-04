<?php

declare(strict_types=1);

namespace service\ollama;

use League\Pipeline\StageInterface;
use service\GeneratedTextProviderInterface;
use service\pipeline\Payload;

final class GeneratedTextFromLocalLlama3Provider extends AbstractOllamaAPIClient implements GeneratedTextProviderInterface, StageInterface
{
    protected string $model;

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  Payload  $payload
     * @return string
     */
    public function __invoke($payload)
    {
        return $this->generateText($payload->getPrompt(), $payload->getRagPrompt());
    }

    protected function getEndpoint(): string
    {
        return '/api/generate';
    }

    /**
     * @return array{model: string, prompt: string}
     */
    protected function getBodyParams(string $input): array
    {
        return [
            'model' => 'llama3.2',
            'prompt' => $input,
        ];
    }
}
