<?php

namespace service\claude;

/**
 * "Anthropic does not offer its own embedding model."
 * Use encoder from other LLM API
 * see https://docs.anthropic.com/en/docs/build-with-claude/embeddings
 */
final class ClaudeTextEncoder extends AbstractClaudeAPIClient
{
}
