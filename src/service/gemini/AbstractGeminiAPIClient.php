<?php

namespace service\gemini;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractGeminiAPIClient
{
    protected ?string $apiKey;

    protected Client $httpClient;

    public function __construct()
    {
        // Load environment variables
        if (! isset($_ENV['GOOGLE_GEMINI_API_KEY'])) {
            $dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
            $dotenv->load();
        }

        // Get API key from environment
        $this->apiKey = $_ENV['GOOGLE_GEMINI_API_KEY'];

        // Initialize Guzzle HTTP client
        $this->httpClient = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
            'timeout' => 10.0,
        ]);
    }

    /**
     * @param  string[][][][]  $data
     * @return string[][][][][][]
     *
     * @throws \JsonException
     */
    protected function request(string $method, string $model, array $data): array
    {
        $url = "models/$model:$method?key=".$this->apiKey;

        try {
            /** @var ResponseInterface $response */
            $response = $this->httpClient->post($url, [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 180,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $responseData ?? [];
        } catch (GuzzleException $e) {
            // Handle the exception and log or rethrow as needed
            throw new \RuntimeException('Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
