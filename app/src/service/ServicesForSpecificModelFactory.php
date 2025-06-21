<?php

namespace service;

use service\claude\GeneratedTextFromClaudeProvider;
use service\deepseek\GeneratedTextFromDeepSeekProvider;
use service\gemini\GeminiTextEncoder;
use service\gemini\GeneratedTextFromGeminiProvider;
use service\ollama\GeneratedTextFromLocalBielikProvider;
use service\ollama\GeneratedTextFromLocalDeepSeekCoderV2Provider;
use service\ollama\GeneratedTextFromLocalDeepSeekR17BProvider;
use service\ollama\GeneratedTextFromLocalLlama3Provider;
use service\ollama\GeneratedTextFromMixtralProvider;
use service\ollama\MxbaiTextEncoder;
use service\openai\Ada002TextEncoder;
use service\openai\GeneratedTextFromGPTProvider;

class ServicesForSpecificModelFactory
{
    public function getGeneratedTextProvider(string $model): GeneratedTextProviderInterface
    {
        $model = strtolower($model);
        $mapping = [
            'gpt-4.1' => GeneratedTextFromGPTProvider::class,
            'o3' => GeneratedTextFromGPTProvider::class,
            'o4-mini' => GeneratedTextFromGPTProvider::class,
            'gpt-4o' => GeneratedTextFromGPTProvider::class,
            'claude-3.7' => GeneratedTextFromClaudeProvider::class,
            'claude-3.5' => GeneratedTextFromClaudeProvider::class,
            'deepseek' => GeneratedTextFromDeepSeekProvider::class,
            'llama3.2' => GeneratedTextFromLocalLlama3Provider::class,
            'mixtral' => GeneratedTextFromMixtralProvider::class,
            'bielik' => GeneratedTextFromLocalBielikProvider::class,
            'deepseek-r1-7b' => GeneratedTextFromLocalDeepSeekR17BProvider::class,
            'deepseek-coder-v2' => GeneratedTextFromLocalDeepSeekCoderV2Provider::class,
            'gemini2' => GeneratedTextFromGeminiProvider::class,
        ];

        if (! isset($mapping[$model])) {
            throw new \Exception(sprintf('No service found for model %s', static::class));
        }

        return new $mapping[$model];
    }

    public function getEmbeddingsService(string $model): TextEncoderInterface
    {
        $model = strtolower($model);
        $mapping = [
            'gpt-4.1' => Ada002TextEncoder::class,
            'o3' => Ada002TextEncoder::class,
            'o4-mini' => Ada002TextEncoder::class,
            'gpt-4o' => Ada002TextEncoder::class,
            'claude-3.5' => Ada002TextEncoder::class,
            'deepseek' => Ada002TextEncoder::class,
            'llama3.2' => MxbaiTextEncoder::class,
            'mixtral' => MxbaiTextEncoder::class,
            'bielik' => MxbaiTextEncoder::class,
            'deepseek-r1-7b' => MxbaiTextEncoder::class,
            'deepseek-coder-v2' => MxbaiTextEncoder::class,
            'gemini2' => GeminiTextEncoder::class,
        ];

        if (! isset($mapping[$model])) {
            throw new \Exception(sprintf('No service found for model %s', static::class));
        }

        return new $mapping[$model](new TextSplitter());
    }
}
