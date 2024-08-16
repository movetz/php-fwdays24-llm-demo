<?php
declare(strict_types=1);

namespace App\Command;

use App\AiBot\OpenAIClient;
use App\Entity\Speaker;
use App\Entity\Talk;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'ai-bot:upload-data')]
class AiBotUploadDataCommand extends Command
{
    public function __construct(
        private OpenAIClient $client,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $talks = [
            new Talk(
                'The right approaches to testing in PHP: Or how quantity can beat quality',
                'In this talk, I\'ll share my experience as a testing consultant and why testing is the responsibility of the whole team. We\'ll cover test architecture, including E2E, functional, unit tests, and manual testing. Special attention will be given to popular PHP testing tools such as PHPUnit, Codeception, Pest and Behat, as well as non-PHP testing tools such as Cypress, Playwright, CodeceptJS and Webdriver Bidi.',
                new Speaker('Mykhailo Bodnarchuk', 'CTO at Testomat.io')
            ),
            new Talk(
                'The story of one sunset and five burnouts or how we migrated a legacy',
                'I will talk about the problems and nuances of migration from legacy products. Also, using my own experience, I will share how we worked with legacy in our team, what challenges and problems we faced, and how we got out of these situations during migration.',
                new Speaker('Vladyslav Pozdniakov', 'Software Engineer at MacPaw')
            ),
            new Talk(
                'How we optimized our product without paid solutions',
                'The story of how we refused third-party DevOps services and took over all services ourselves. In the process, we made a complete revision of the infrastructure, added new monitoring and profiling tools on production. Action algorithms were built based on tools: Graylog, Grafana, influxDB, Pyroscope, Prometheus. As a result, the utilization of resources decreased by two times, and responses to key APIs were accelerated. During the talk, I\'ll cover what problems we found, how exactly we improved the metrics, and how we got to swoole.',
                new Speaker('Dmytro Nemesh', 'CTO at Lalafo')
            ),
            new Talk(
                'PHP Core – Part 1 – Understanding Variables Types',
                'Talk on data types and how PHP works with them at the core level with use cases for more efficient utilization.',
                new Speaker('Denys Kurasov', 'Lead PHP Software Engineer at Growe')
            ),
            new Talk(
                'Copilot: an NPC or the main character',
                'In my presentation, I will discuss all the advantages and disadvantages of using Github Copilot for PHP development. Additionally, I will compare it with existing competitors and explain why they are better or worse than the Microsoft product. I will also address security issues and provide recommendations for companies on how to prevent their code from being exposed to the public. Throughout the presentation, I will gather arguments to answer the question: is Copilot an NPC or the main character?',
                new Speaker('Olena Kirichok', 'Software Engineer at Accolade Inc.')
            ),
            new Talk(
                'Chat with your private data using Llama3 and LLPhant in PHP',
                'In this talk, I\'ll give a quick introduction to LLM and how to use it in a PHP application. I\'ll show some examples using the LLPhant project including a retrieval-augmented generation (RAG) system using a local LLM (Llama 3) and Elasticsearch as a vector database.',
                new Speaker('Enrico Zimuel', 'Tech Lead at Elastic')
            ),
            new Talk(
                '"Pardon my French" or The technical aspects of i18n and l10n',
                'Localization and Internationalization are two core concepts to create sites and applications for international users. In creating and working on such international websites I repeatedly made or found the same mistakes and pitfalls that made creating international websites much harder than necessary. Let me take you on a tour through the concepts and common problems that arise and see how they can be solved with less headache than you might think.',
                new Speaker('Andreas Heigl', 'Founder at stella-maris.solutions')
            ),
            new Talk(
                'Better Code Design in PHP',
                'Are you tired of spending hours reading code just to find the right place to make a small change? Are you forced to split your application into several just so that devs don\'t step on each other\'s toes? This talk unites theory with practice to show you how to structure your code so that it is easy to read, to test and to maintain. You will step out with concrete ideas of how to improve your code design.',
                new Speaker('Anna Filina', 'Legacy archaeologist at Filina Consulting')
            ),
        ];

        foreach ($talks as $talk) {
            $text = $talk->getName() . ' ' . $talk->getDescription();
            $embedding = $this->client->callEmbeddings($text);
            $talk->setEmbedding($embedding);

            $this->entityManager->persist($talk);
            $output->writeln("Add: {$talk->getName()}");
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
