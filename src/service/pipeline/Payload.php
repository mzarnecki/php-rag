<?php

declare(strict_types=1);

namespace service\pipeline;

final class Payload
{
    private string $prompt;

    private string $embeddingPrompt;

    /** @var string[] */
    private array $similarDocuments;

    /** @var string[] */
    private array $similarDocumentsNames;

    private string $ragPrompt;

    private bool $useReranking = false;

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function getEmbeddingPrompt(): string
    {
        return $this->embeddingPrompt;
    }

    public function setEmbeddingPrompt(string $embeddingPrompt): self
    {
        $this->embeddingPrompt = $embeddingPrompt;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSimilarDocuments(): array
    {
        return $this->similarDocuments;
    }

    /**
     * @param  string[]  $similarDocuments
     * @return $this
     */
    public function setSimilarDocuments(array $similarDocuments): self
    {
        $this->similarDocuments = $similarDocuments;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSimilarDocumentsNames(): array
    {
        return $this->similarDocumentsNames;
    }

    /**
     * @param  string[]  $similarDocumentsNames
     * @return $this
     */
    public function setSimilarDocumentsNames(array $similarDocumentsNames): self
    {
        $this->similarDocumentsNames = $similarDocumentsNames;

        return $this;
    }

    public function getRagPrompt(): string
    {
        return $this->ragPrompt;
    }

    public function setRagPrompt(string $ragPrompt): self
    {
        $this->ragPrompt = $ragPrompt;

        return $this;
    }

    public function useReranking(): bool
    {
        return $this->useReranking;
    }

    public function setUseReranking(bool $useReranking): self
    {
        $this->useReranking = $useReranking;

        return $this;
    }
}
