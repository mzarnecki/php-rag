<?php

declare(strict_types=1);

namespace command\rag;

use League\Pipeline\FingersCrossedProcessor;
use League\Pipeline\Pipeline;
use Minicli\Command\CommandController;
use service\DocumentProvider;
use service\openai\Ada002TextEncoder;
use service\openai\GeneratedTextFromGPTProvider;
use service\pipeline\Payload;
use service\PromptResolver;
use service\RAGPromptProvider;
use service\TextSplitter;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\textarea;

final class DefaultController extends CommandController
{
    public function handle(): void
    {
        $this->info('Php Rag application');

        do {
            $question = textarea(
                label: 'Find answer in websites database.',
                placeholder: 'What is specialization of programmer and lecturer Michał Żarnecki based on his website content...',
                required: 'A question is required.'
            );

            spin(
                fn () => $this->process($question),
                'Fetching response...'
            );

        } while ($question);
    }

    private function process(string $question): void
    {
        $_POST['prompt'] = $question;

        $promptResolver = new PromptResolver();
        $textEncoder = new Ada002TextEncoder(new TextSplitter());
        $documentProvider = new DocumentProvider();
        $ragPromptProvider = new RAGPromptProvider();
        $generatedTextProvider = new GeneratedTextFromGPTProvider();
        $payload = new Payload();

        $pipeline = (new Pipeline(new FingersCrossedProcessor()))
            ->pipe($promptResolver) //get prompt from POST or CLI
            ->pipe($textEncoder) //get embeddings for prompt
            ->pipe($documentProvider) //find documents with similarity to prompt
            ->pipe($ragPromptProvider) //prepare RAG input - combine prompt with matched source documents
            ->pipe($generatedTextProvider); //get API response
        $response = $pipeline->process($payload);

        $this->info($payload->getRagPrompt(), true);
        $this->display('You asked: '.$question);
        $this->success('Response: '.$response);
        $this->newline();

    }
}
