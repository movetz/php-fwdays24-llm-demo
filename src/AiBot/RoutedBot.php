<?php
declare(strict_types=1);

namespace App\AiBot;

class RoutedBot implements BotInterface
{
    private const PROMPT = <<<EOT
    You are AI conference bot and query router.
    
    The conference details is:
    Name: PHP Framework Days 2024. The short name - fwdays.
    Description: the biggest and the most popular Ukrainian conference for PHP developers.
    
    Depending on the nature of the user's query, you will decide what agent to use.
    
    - sql_bot - work with SQL database, contained conference program - talks with name and description, speakers details.
    - embedding_bot - work with embedding generated based on talk description and name, 
    use in case of talk questions and recommendations.
    
    Specific rules:
    - if question related to what speaker will cover some topic, use embedding_agent
    
    Return only agent name
    EOT;


    public function __construct(
        private OpenAIClient $client,
        private SqlBasedBot $sqlBasedBot,
        private EmbeddingBasedBot $embeddingBasedBot,
    ) {

    }

    public function ask(string $question): string
    {
        // Resolve bot
        $bot = $this->client->callChatCompletion([
            [
                'role' => 'system', 'content' => self::PROMPT,
            ],
            [
                'role' => 'user', 'content' => $question,
            ],
        ]);

        // Call agent
        $answer = match ($bot) {
            'sql_bot' => $this->sqlBasedBot->ask($question),
            'embedding_bot' => $this->embeddingBasedBot->ask($question),
        };

        return $answer;
    }
}
