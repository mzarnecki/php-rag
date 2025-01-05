<?php

namespace service\evaluate;

use service\claude\AbstractClaudeAPIClient;

class CriteriaEvaluator extends AbstractClaudeAPIClient
{
    private string $model = 'claude-3-5-sonnet-20241022';

    public function evaluate(string $prompt, string $answer): string
    {
        $prompt = $this->getEvaluationPrompt($prompt, $answer);
        $response = $this->request(
            $prompt,
            'messages',
            $this->model
        );
        return $response['content'][0]['text'];
    }


    private function getEvaluationPrompt(string $prompt, string $answer): string
    {
        return "You are a helpful assistant that evaluates the quality of an answer based on the following criteria:
        relevance: Does the answer address the question accurately ?
        conciseness: Is the answer free of unnecessary details ?
        clarity: Is the language clear and understandable ?
        creativity : (Optional) Is the response innovative or insightful ?
        factual_accuracy: Are the facts provided correct ?
        
        Score each category above in range 1–5
        
        
        Here is the question: {$prompt}
        
        
        Here is the answer: {$answer}
        
        
        Output a JSON object with criteria as keys.
        Example output should look like this:
        {
            relevance: 5,
            conciseness: 4,
            clarity: 5,
            creativity: 3,
            factual_accuracy: 5
        }";
    }
}