<?php

declare(strict_types=1);

namespace service\openai;

use League\Pipeline\StageInterface;
use service\GeneratedTextProviderInterface;
use service\pipeline\Payload;

final class GeneratedTextFromGPTProvider extends AbstractGPTAPIClient implements GeneratedTextProviderInterface, StageInterface
{
    private string $model = 'o3';

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        // prepare API input
        $input = $sourceDocuments."\n\n##### INPUT: \n".$prompt."\n##### RESPONSE:\n";

        // get API response
        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'content' => $input,
                    'role' => 'user',
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    /**
     * @param  Payload  $payload
     * @return string
     */
    public function __invoke($payload)
    {
        return $this->generateText($payload->getPrompt(), $payload->getRagPrompt());
    }
}
