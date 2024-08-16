<?php
declare(strict_types=1);

namespace App\AiBot;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class EmbeddingBasedBot implements BotInterface
{
    private const PROMPT = <<<EOT
    You ai conference bot. Your task answer questions depends on provided question and additional data context.
    The data ordered by relevance, top is the first.
    EOT;


    public function __construct(
        private OpenAIClient $client,
        private EntityManagerInterface $entityManager,
    ) {

    }

    public function ask(string $question): string
    {
        // https://api.openai.com/v1/embeddings
        $embedding = $this->client->callEmbeddings($question);
        // Search topics in PostgreSQL + pg_vector extension
        $topics = $this->searchResultInVectorDb($embedding, 3);
        // Format data from array to string with details
        $formatedTopics = $this->formatTopicsForPrompt($topics);
        // Generate answer with LLM
        $answer = $this->client->callChatCompletion([
            [
                'role' => 'system', 'content' => self::PROMPT,
            ],
            [
                'role' => 'system', 'content' => $formatedTopics,
            ],
            [
                'role' => 'user', 'content' => $question,
            ],
        ]);

        return $answer;
    }

    public function checkEmbeddings(string $question): string
    {
        $embedding = $this->client->callEmbeddings($question);
        $topics = $this->searchResultInVectorDb($embedding, 1);
        $formatedTopics = $this->formatTopicsForPrompt($topics);
        return $formatedTopics;
    }

    private function searchResultInVectorDb(array $embedding, int $k): array
    {
        $query = $this->entityManager->createQuery(
            "SELECT cosine_similarity(e.embedding, :vector) as sim, e FROM App\Entity\Talk e ORDER BY sim DESC"
        );
        $query->setParameter('vector', $embedding, 'vector');
        $results = $query
            ->setMaxResults($k)
            ->setFetchMode('App\Entity\Talk', "speaker", ClassMetadata::FETCH_EAGER)
            ->getResult();

        return array_map(fn ($result) => $result[0], $results);
    }

    private function formatTopicsForPrompt(array $data): string
    {
        $formatedData = '';
        foreach ($data as $talk) {
            $formatedData .= "Talk: {$talk->getName()}".PHP_EOL;
            $formatedData .= "Description: {$talk->getDescription()}".PHP_EOL;
            $formatedData .= "Speaker: {$talk->getSpeaker()->getName()}, {$talk->getSpeaker()->getPosition()}".PHP_EOL;
            $formatedData .= "==============".PHP_EOL;
        }

        return $formatedData;
    }
}
