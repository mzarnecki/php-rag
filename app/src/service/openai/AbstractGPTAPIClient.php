<?php

declare(strict_types=1);

namespace service\openai;

use Dotenv\Dotenv;
use OpenAI;
use OpenAI\Client;

abstract class AbstractGPTAPIClient
{
    protected Client $client;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();
        $this->client = OpenAI::Client($_ENV['OPENAI_API_KEY']);
    }
}
