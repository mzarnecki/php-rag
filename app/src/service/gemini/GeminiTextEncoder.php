<?php

namespace service\gemini;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;
use service\TextEncoderInterface;
use service\TextSplitter;

final class GeminiTextEncoder extends AbstractGeminiAPIClient implements StageInterface, TextEncoderInterface
{
    private string $embeddingModel = 'text-embedding-004';

    public function __construct(private readonly TextSplitter $textSplitter)
    {
        parent::__construct();
    }

    public function getEmbeddings(string $document): array
    {
        // Split document into overlapping chunks of 9000 characters with 200-character overlap
        $chunks = $this->textSplitter->splitDocumentIntoChunks($document, 9000, 200);
        $embeddings = [];

        foreach ($chunks as $chunk) {
            $response = $this->request(
                'embedContent',
                $this->embeddingModel,
                [
                    'content' => ['parts' => [[
                        'text' => $chunk,
                    ]]],
                ]
            );
            $embeddings[] = json_encode($response['embedding']['values'], JSON_THROW_ON_ERROR);
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
