<?php

namespace service\ollama;

use League\Pipeline\StageInterface;
use service\GeneratedTextProviderInterface;
use service\pipeline\Payload;

class GeneratedTextFromLocalDeepSeekCoderV2Provider extends AbstractOllamaAPIClient
    implements StageInterface, GeneratedTextProviderInterface
{
    /**
     * @param Payload $payload
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

    protected function getBodyParams(string $input): array
    {
        return [
            "model" => "deepseek-coder-v2",
            "prompt" => $input
        ];
    }
}
{

}