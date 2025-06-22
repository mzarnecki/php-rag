<?php

namespace service\claude;

use League\Pipeline\StageInterface;
use service\GeneratedTextProviderInterface;
use service\pipeline\Payload;

final class GeneratedTextFromClaudeProvider extends AbstractClaudeAPIClient implements GeneratedTextProviderInterface, StageInterface
{
    private string $model = 'claude-3-5-sonnet-20241022';

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        // Make API request
        $response = $this->request(
            $sourceDocuments."\n\n##### INPUT: \n".$prompt."\n##### RESPONSE:\n",
            'messages',
            $this->model
        );

        return $response['content'][0]['text'];
    }

    /**
     * @param  Payload  $payload
     */
    public function __invoke($payload): string
    {
        return $this->generateText($payload->getPrompt(), $payload->getRagPrompt());
    }
}
