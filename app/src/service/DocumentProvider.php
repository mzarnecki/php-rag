<?php
declare(strict_types=1);

namespace service;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;

final class DocumentProvider extends AbstractDocumentRepository implements StageInterface
{
    public function getSimilarDocuments(string $embeddingPrompt): array
    {
        $stmt = $this->connection->prepare("SELECT text from document order by embedding <-> :embeddingPrompt limit 15;");
        $stmt->execute(['embeddingPrompt' => $embeddingPrompt]);
        return $stmt->fetchAll();
    }

    /**
     * @param Payload $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        return $payload->setSimilarDocuments($this->getSimilarDocuments($payload->getEmbeddingPrompt()));
    }
}