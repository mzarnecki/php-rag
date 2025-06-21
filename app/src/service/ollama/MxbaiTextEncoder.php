<?php

declare(strict_types=1);

namespace service\ollama;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;
use service\TextEncoderInterface;
use service\TextSplitter;

final class MxbaiTextEncoder extends AbstractOllamaAPIClient implements StageInterface, TextEncoderInterface
{
    private string $embeddingModel = 'mxbai-embed-large';

    public function __construct(private readonly TextSplitter $textSplitter)
    {
    }

    public function getEmbeddings(string $document): array
    {
        $chunks = $this->textSplitter->splitDocumentIntoChunks($document, 9000, 200);
        $embeddings = [];

        foreach ($chunks as $chunk) {
            $response = $this->request($chunk);
            $embeddings[] = json_encode(json_decode((string) $response, true, 512, JSON_THROW_ON_ERROR)['embedding'], JSON_THROW_ON_ERROR);
        }

        return $embeddings;
    }

    /**
     * @param  Payload  $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        return $payload->setEmbeddingPrompt($this->getEmbeddings($payload->getPrompt())[0]);
    }

    protected function getEndpoint(): string
    {
        return '/api/embeddings';
    }

    /**
     * @return array{model: string, prompt: string}
     */
    protected function getBodyParams(string $input): array
    {
        return [
            'model' => $this->embeddingModel,
            'prompt' => $input,
        ];
    }
}
