<?php

use Dotenv\Dotenv;
use League\Pipeline\FingersCrossedProcessor;
use League\Pipeline\Pipeline;
use service\DocumentProvider;
use service\evaluate\CriteriaEvaluator;
use service\evaluate\StringComparisonEvaluator;
use service\pipeline\Payload;
use service\PromptResolver;
use service\RAGPromptProvider;
use service\ServicesForSpecificModelFactory;

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$model = $_ENV['MODEL'];
$servicesForModelFactory = new ServicesForSpecificModelFactory();

$promptResolver = new PromptResolver();
$textEncoder = $servicesForModelFactory->getEmbeddingsService($model);
$documentProvider = new DocumentProvider();
$generatedTextProvider = $servicesForModelFactory->getGeneratedTextProvider($model);
$ragPromptProvider = new RAGPromptProvider();

$payload = (new Payload())->setUseReranking(false);

$pipeline = (new Pipeline(new FingersCrossedProcessor()))
    ->pipe($promptResolver) //get prompt from POST or CLI
    ->pipe($textEncoder) //get embeddings for prompt
    ->pipe($documentProvider) //find documents with similarity to prompt
    ->pipe($ragPromptProvider) //prepare RAG input - combine prompt with matched source documents
    ->pipe($generatedTextProvider); //get API response

$response = $pipeline->process($payload);

if (isset($_GET['api'])) {
    $resp = [
        'response' => $response,
        'documents' => $payload->getSimilarDocumentsNames(),
    ];
} else {
    echo '<h1>RESPONSE:</h1>';
    echo '<br /><br />';
    echo $response;
    echo '<br /><br /><br /><br />';
    echo '<h1>DOCUMENTS:</h1>';
    echo '<br /><br />';
    echo $payload->getRagPrompt();
}

$evaluate = $_POST['evaluate'] ?? $_GET['evaluate'] ?? null;

if ($evaluate) {
    $criteriaEvaluator = new CriteriaEvaluator($generatedTextProvider);
    $tokenSimilarityEvaluator = new StringComparisonEvaluator();
    $compareResp = 'Michał Żarnecki programmer is not the same person as Michał Żarnecki audio engineer. 
        Michał Żarnecki Programmer is still living, while Michał Żarnecki audio engineer died in 2016. They cannot be the same person.
        Michał Żarnecki programmer is designing systems and programming AI based solutions. He is also a lecturer.
        Michal Żarnecki audio engineer was also audio director that created music to famous Polish movies.';

    try {
        $resp['evaluation']['ROUGE'] = $tokenSimilarityEvaluator->calculateROUGE($compareResp, $response);
        $resp['evaluation']['BLEU'] = $tokenSimilarityEvaluator->calculateBLEU($compareResp, $response);
        $resp['evaluation']['criteria'] = $criteriaEvaluator->evaluate($payload->getRagPrompt(), $response);
    } catch (Throwable $e) {
        $resp['evaluation']['error'] = 'Error occured during evaluation, check logs.';
    }
}

echo json_encode($resp, JSON_THROW_ON_ERROR);
