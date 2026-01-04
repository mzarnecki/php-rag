<?php

namespace service\claude;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractClaudeAPIClient
{
    protected ?string $apiKey;

    protected Client $httpClient;

    public function __construct()
    {
        // Load environment variables
        if (! isset($_ENV['ANTHROPIC_API_KEY'])) {
            $dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
            $dotenv->load();
        }

        // Get API key from environment
        $this->apiKey = $_ENV['ANTHROPIC_API_KEY'] ?? null;

        // Initialize Guzzle HTTP client
        $this->httpClient = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'timeout' => 10.0,
        ]);
    }

    /**
     * @return string[][][]
     *
     * @throws \JsonException
     */
    protected function request(string $prompt, string $endpoint, string $model): array
    {
        $url = $endpoint;

        $data = [
            'model' => $model,
            'max_tokens' => 2048,
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ];

        try {
            /** @var ResponseInterface $response */
            $response = $this->httpClient->post($url, [
                'json' => $data,
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 180,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            // Log the response for debugging
            error_log(json_encode($responseData, JSON_THROW_ON_ERROR).PHP_EOL);

            return $responseData ?? [];
        } catch (GuzzleException $e) {
            // Handle the exception and log or rethrow as needed
            throw new \RuntimeException('Request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
