<?php

declare(strict_types=1);

namespace test;

use PHPUnit\Framework\TestCase;
use service\PromptResolver;

final class PromptResolverTest extends TestCase
{
    public function testPrompt_isDefaultPromptProvided_whenNoInputIsSend(): void
    {
        $promptResolver = new PromptResolver();
        $prompt = $promptResolver->getPromptFromInput();
        $this->assertNotEmpty($prompt);
    }

    public function testPrompt_isProvided_whenConsoleArgGiven(): void
    {
        global $argv;
        $argv[0] = 'What is the result of 2 + 2?';
        $promptResolver = new PromptResolver();
        $prompt = $promptResolver->getPromptFromInput();
        $this->assertEquals('You are a helpful assistant that answers questions based on source documents.'
            .PHP_EOL.$argv[0], $prompt);
    }

    public function testPrompt_isProvided_whenPostGiven(): void
    {
        $_POST['prompt'] = 'What is the result of 2 + 2?';
        $promptResolver = new PromptResolver();
        $prompt = $promptResolver->getPromptFromInput();
        $this->assertEquals('You are a helpful assistant that answers questions based on source documents.'
            .PHP_EOL.$_POST['prompt'], $prompt);
    }
}
