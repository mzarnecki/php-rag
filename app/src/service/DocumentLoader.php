<?php

declare(strict_types=1);

namespace service;

final class DocumentLoader extends AbstractDocumentRepository
{
    public function __construct(
        private readonly TextEncoderInterface $textEncoder,
        private readonly DocumentTrimmer $documentTrimmer
    ) {
        parent::__construct();
    }

    public function loadDocuments(int $skippedDocumentsNumber = 0): void
    {
        $path = __DIR__.'/../documents';
        $files = array_diff((array) scandir($path), ['.', '..']);
        $total = count($files);

        foreach ($files as $index => $file) {
            if ($index < $skippedDocumentsNumber) {
                continue;
            }
            $document = (string) file_get_contents($path.'/'.$file);

            //cut document to tokens limit 8192
            $document = $this->documentTrimmer->trim($document);

            //load documents to postgres database
            try {
                $responseDocumentsChunks = $this->textEncoder->getEmbeddings($document);
            } catch (\Throwable $e) {
                error_log($e->getMessage());

                return;
            }

            foreach ($responseDocumentsChunks as $chunk) {
                $this->insertDocument((string) $file, $document, $chunk);
            }
            $this->showProgress($index, $total, $skippedDocumentsNumber);
        }
        fwrite(STDOUT, "Loading documents complete\n");
    }

    private function showProgress(int|string $index, int $total, int $skip): void
    {
        if (! is_int($index)) {
            return;
        }
        $all = $total - $skip;
        $numLoaded = $index - $skip + 1;
        $progress = '';
        if ($numLoaded % 10 === 0) {
            $progress = str_repeat('-', $numLoaded / 10);
            fwrite(STDOUT, "{$progress}\nLoaded {$numLoaded} of {$all} source documents to vector DB\n");
        }
    }

    private function insertDocument(
        string $name,
        string $document,
        string $embedding
    ): bool {
        $statement = $this->connection->prepare('INSERT INTO document(name, text, embedding) VALUES(:name, :doc, :embed)');

        return $statement->execute([
            'name' => $name,
            'doc' => $document,
            'embed' => $embedding,
        ]);
    }
}
