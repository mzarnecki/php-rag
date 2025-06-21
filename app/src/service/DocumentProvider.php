<?php

declare(strict_types=1);

namespace service;

use League\Pipeline\StageInterface;
use service\pipeline\Payload;

final class DocumentProvider extends AbstractDocumentRepository implements StageInterface
{
    /**
     * @param  string  $distanceFunction  l2|cosine|innerProduct
     */
    public function getSimilarDocuments(
        string $prompt,
        string $embeddingPrompt,
        bool $useReranking = false,
        int $limit = 10,
        string $distanceFunction = 'l2'
    ): array {
        $query = $this->getQueryForDistanceFunction($distanceFunction, $limit);
        $stmt = $this->connection->prepare($query);
        $stmt->execute(['embeddingPrompt' => $embeddingPrompt]);
        $documents = $stmt->fetchAll();
        error_log('RETRIEVED DOCUMENTS:'.PHP_EOL);
        $documentsNames = [];
        foreach ($documents as &$document) {
            $documentsNames[] = $document['name'];
            error_log($document['name'].PHP_EOL);
        }
        if ($useReranking) {
            $documents = $this->rerank($prompt, $documents);
        } else {
            $documents = array_map(fn($document) => $document['text'], $documents);
        }

        return [$documents, $documentsNames];
    }

    /**
     * Sometimes its worth to use efficient algorithm for querying vector DB and more precise to rerank chosen documents in
     */
    public function rerank(string $prompt, array $documents): array
    {
        $documentsReranked = [];
        foreach ($documents as $document) {
            $intersection = array_intersect(explode(' ', (string) $document['text']), explode(' ', $prompt));
            $index = count($intersection);
            while (isset($documentsReranked[$index])) {
                $index++;
            }
            $documentsReranked[$index] = $document['text'];
        }
        krsort($documentsReranked);

        return $documentsReranked;
    }

    private function getQueryForDistanceFunction(
        string $distanceFunction,
        int $limit = 10
    ): string {
        return match ($distanceFunction) {
            'l2' => $this->getQueryForL2Distance($limit),
            'cosine' => $this->getQueryForCosineDistance($limit),
            'innerProduct' => $this->getQueryForInnerProduct($limit),
            default => throw new \LogicException('Unknown distance function: '.$distanceFunction),
        };
    }

    private function getQueryForL2Distance(int $limit = 10): string
    {
        return "SELECT name, text from document order by embedding <-> :embeddingPrompt limit {$limit};";
    }

    private function getQueryForCosineDistance(int $limit = 10): string
    {
        return "SELECT name, text from document order by 1 - (embedding <=> :embeddingPrompt) limit {$limit};";
    }

    private function getQueryForInnerProduct(int $limit = 10): string
    {
        return "SELECT name, text from document order by (embedding <#> :embeddingPrompt) * -1 limit {$limit};";
    }

    /**
     * @param  Payload  $payload
     * @return Payload
     */
    public function __invoke($payload)
    {
        [$documents, $documentsNames] = $this->getSimilarDocuments(
            $payload->getPrompt(),
            $payload->getEmbeddingPrompt(),
            $payload->useReranking()
        );
        $payload->setSimilarDocuments($documents);
        $payload->setSimilarDocumentsNames($documentsNames);

        return $payload;
    }
}
