<?php

namespace App\Command;

use App\AiBot\SqlBasedBot;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'ai-bot:check-sql')]
class AiBotCheckSqlCommand extends Command
{
    public function __construct(
        private SqlBasedBot $bot,
    ) {
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
            $answer = $this->bot->checkSql($question);
            $output->writeln($answer);
        }
    }
}
