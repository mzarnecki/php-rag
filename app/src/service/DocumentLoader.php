<?php

declare(strict_types=1);

namespace service;

final class DocumentLoader extends AbstractDocumentRepository
{
    public function __construct(
        private readonly TextEncoderInterface $textEncoder
    ) {
        parent::__construct();
    }

    public function loadDocuments(): void
    {
        $path = __DIR__.'/../documents';
        $files = array_diff((array) scandir($path), ['.', '..']);
        $total = count($files);

        $skipFirstN = 1330; //replace this to skip N documents for debugging
        foreach ($files as $index => $file) {
            if ($index < $skipFirstN) {
                continue;
            }
            $document = (string) file_get_contents($path.'/'.$file);
            //cut document to tokens limit 8192
            if (str_word_count($document) >= 0.75 * 8000) {
                $words = explode(' ', $document);
                $document = implode(' ', array_slice($words, 0, 6000));
            }
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
            $this->showProgress($index, $total, $skipFirstN);
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
