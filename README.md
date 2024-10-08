# PHP Framework days LLM AI bot demo

## Installation

1. `docker compose up -d` - run PostgreSQL vs pg_vector
2. `composer install`
3. `php bin/console doctrine:migrations:migrate`
4. Add Open AI key to .env file `OPEN_AI_KEY` env variable
5. `php bin/console ai-bot:upload-data` - export data


## Launch

```php
$client = new OpenAIClient($httpClient, $openAiKey);
$bot = new RoutedBot(
    $client,
    new EmbeddingBasedBot($client, $em),
    new SqlBasedBot($client, $em),
);

$answer = $bot->ask($question);
```

`CMD php bin/console ai-bot:ask` - to launch bot, type `exit` - to close

## Bot types

- [AiBot\ContextBasedBot.php](src/AiBot/ContextBasedBot.php) - use prompt as additional context source
- [AiBot\EmbeddingBasedBot.php](src/AiBot/EmbeddingBasedBot.php)  - use embeddings and pg_vector extension as additional context
- [AiBot\SqlBasedBot.php](src/AiBot/SqlBasedBot.php) - use generated SQL queries and fetch data from PostgreSQL
- [AiBot\RoutedBot.php](src/AiBot/RoutedBot.php) - use ChatGPT router to choose EmbeddingBasedBot or SqlBasedBot

## Additional links

- [langchain-ai/langchain](https://github.com/angchain-ai/langchain)
- [run-llama/llama_index](https://github.com/run-llama/llama_index)
- [theodo-group/LLPhant](https://github.com/theodo-group/LLPhant)
- [kambo-1st/langchain-php](https://github.com/kambo-1st/langchain-php)
