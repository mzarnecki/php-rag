<?php

declare(strict_types=1);

namespace service;

use League\Pipeline\StageInterface;
use Rajentrivedi\TokenizerX\TokenizerX;
use service\pipeline\Payload;

final class RAGPromptProvider implements StageInterface
{
    public const CONTEXT_TOKEN_COUNT = 5000;

    /**
     * @param  string[]  $documents
     */
    public function getRAGPrompt(array $documents, string $prompt): string
    {
        $contextTokenCount = self::CONTEXT_TOKEN_COUNT - TokenizerX::count($prompt) - 20;
        $input = '';
        foreach ($documents as $document) {
            $input .= $document."\n\n";
            $tokens = TokenizerX::tokens($input, 'gpt-4');

            if (count($tokens) > $contextTokenCount) {
                break;
            }
        }

        return $input;
    }

    /**
     * @param  Payload  $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        return $payload->setRagPrompt($this->getRAGPrompt($payload->getSimilarDocuments(), $payload->getPrompt()));
    }
}
