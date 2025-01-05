<?php
namespace service\ollama;

use GuzzleHttp\Client;

Abstract class AbstractOllamaAPIClient
{
    protected function request(string $input)
    {
        # get response
        $client = new Client();
        $response = $client->request('POST', 'http://ollama-container:11434' . $this->getEndpoint(), [
            'body' => json_encode($this->getBodyParams($input))
        ]);
        return $response->getBody()->getContents();
    }

    public function generateText(string $prompt, string $sourceDocuments): string
    {
        # prepare input
        $input = "Source documents:\n" . $sourceDocuments . "\n\n##### INPUT: \n"  . $prompt . "\n##### RESPONSE:\n";
        error_log("Source documents: " . $sourceDocuments . PHP_EOL);
        error_log("\n\n##### INPUT: \n"  . $prompt . "\n##### RESPONSE:\n");
        $body = $this->request($input);

        $rows = preg_split('/\n/', $body);
        $response = array_map(function($item){
            $row = json_decode($item, true);
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