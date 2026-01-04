<?php

namespace service\gemini;

use League\Pipeline\StageInterface;
use service\GeneratedTextProviderInterface;
use service\pipeline\Payload;

final class GeneratedTextFromGeminiProvider extends AbstractGeminiAPIClient implements GeneratedTextProviderInterface, StageInterface
{
    private string $model = 'gemini-2.0-flash-exp';

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        // Prepare the content
        $content = $sourceDocuments."\n\n##### INPUT: \n".$prompt."\n##### RESPONSE:\n";

        // Create the request
        $responseData = $this->request(
            'generateContent',
            $this->model,
            [
                'contents' => [
                    'parts' => [
                        ['text' => $content],
                    ],
                ],
            ]
        );

        return $responseData['candidates'][0]['content']['parts'][0]['text'];
    }

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
}
