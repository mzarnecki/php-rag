<?php

namespace service\ollama;

use GuzzleHttp\Client;

abstract class AbstractOllamaAPIClient
{
    protected function request(string $input)
    {
        // get response
        $client = new Client();
        $response = $client->request('POST', 'http://ollama-container:11434'.$this->getEndpoint(), [
            'body' => json_encode($this->getBodyParams($input), JSON_THROW_ON_ERROR),
        ]);

        return $response->getBody()->getContents();
    }

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        // prepare input
        $input = "Source documents:\n".$sourceDocuments."\n\n##### INPUT: \n".$prompt."\n##### RESPONSE:\n";
        error_log('Source documents: '.$sourceDocuments.PHP_EOL);
        error_log("\n\n##### INPUT: \n".$prompt."\n##### RESPONSE:\n");
        $body = $this->request($input);

        $rows = preg_split('/\n/', (string) $body);
        $response = array_map(function ($item) {
            $row = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
            if ($row) {
                return $row['response'];
            }

            return '';
        }, $rows);

        return implode('', $response);
    }

    abstract protected function getEndpoint(): string;

    abstract protected function getBodyParams(string $input): array;
}
