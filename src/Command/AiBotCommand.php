<?php

namespace App\Command;

use App\AiBot\BotInterface;
use App\AiBot\ContextBasedBot;
use App\AiBot\EmbeddingBasedBot;
use App\AiBot\OpenAIClient;
use App\AiBot\RoutedBot;
use App\AiBot\SqlBasedBot;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'ai-bot:ask')]
class AiBotCommand extends Command
{
    private BotInterface $bot;

    public function __construct(
        private EmbeddingBasedBot $embeddingBasedBot,
        private SqlBasedBot $sqlBasedBot,
        private RoutedBot $routedBot,
        private ContextBasedBot $contextBasedBot,
        private OpenAIClient $client,
    ) {
        $this->bot = $contextBasedBot;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        while (true) {
            $cliQuestion = new Question('<info>Ask me a question ></info>'.PHP_EOL, null);
            $question = $helper->ask($input, $output, $cliQuestion);
            if ($question == 'exit') {
                return Command::SUCCESS;
            }

            $output->writeln('###');
            $answer = $this->bot->ask($question);
            $output->writeln($answer);
        }
    }
}
