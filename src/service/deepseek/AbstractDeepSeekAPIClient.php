<?php

namespace service\deepseek;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractDeepSeekAPIClient
{
    protected ?string $apiKey;

    protected Client $httpClient;

    public function __construct()
    {
        if (! isset($_ENV['DEEPSEEK_API_KEY'])) {
            $dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
            $dotenv->load();
        }

        $this->apiKey = $_ENV['DEEPSEEK_API_KEY'];

        $this->httpClient = new Client([
            'base_uri' => 'https://api.deepseek.com/',
            'timeout' => 10.0,
        ]);
    }

    /**
     * @param  string[][]  $messages
     * @param  string[]  $options
     * @return string[][][][]
     *
     * @throws \JsonException
     */
    protected function request(array $messages, string $model, bool $stream = false, array $options = []): array
    {
        $data = array_merge([
            'model' => $model,
            'messages' => $messages,
            'stream' => $stream,
        ], $options);

        try {
            /** @var ResponseInterface $response */
            $response = $this->httpClient->post('chat/completions', [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 180,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            error_log(json_encode($responseData, JSON_THROW_ON_ERROR).PHP_EOL);

            return $responseData ?? [];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('DeepSeek API request failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
