<?php

declare(strict_types=1);

namespace service\openai;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;
use service\TextEncoderInterface;
use service\TextSplitter;

final class Ada002TextEncoder extends AbstractGPTAPIClient implements StageInterface, TextEncoderInterface
{
    private string $embeddingModel = 'text-embedding-ada-002';

    public function __construct(private readonly TextSplitter $textSplitter)
    {
        parent::__construct();
    }

    public function getEmbeddings(string $document): array
    {
        $chunks = $this->textSplitter->splitDocumentIntoChunks($document, 9000, 200);
        $embeddings = [];

        foreach ($chunks as $chunk) {
            $response = $this->client->embeddings()->create([
                'input' => $chunk,
                'model' => $this->embeddingModel,
            ]);
            $embeddings[] = json_encode($response->embeddings[0]->embedding, JSON_THROW_ON_ERROR);
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
}
