<?php

use Dotenv\Dotenv;
use service\DocumentLoader;
use service\ServicesForSpecificModelFactory;

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$model = $_ENV['MODEL'];

$textEncoder = (new ServicesForSpecificModelFactory())->getEmbeddingsService($model);
$documentLoader = new DocumentLoader($textEncoder);

//load documents
$documentLoader->loadDocuments();
