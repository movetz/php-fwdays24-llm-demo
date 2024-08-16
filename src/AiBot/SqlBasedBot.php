<?php
declare(strict_types=1);

namespace App\AiBot;

use Doctrine\ORM\EntityManagerInterface;

class SqlBasedBot implements BotInterface
{
    private const SQL_GENERATION_PROMPT = <<<EOT
    You ai conference bot SQL code generate. Your task generate SQL code depends on database schema and input question.
    The database engine is Postgresql.
    
    The database schema is:
    ------------------------------
    
    Table: 
    speaker - describes conference speaker
    ---------------
    id: integer - autoincrement identifier
    name: varchar - speaker name, for example Jon Doe
    position: varchar - speaker job position, in format - "[Job title] at ["Company"], for example CTO at Best Inc.
    
    Table:
    talk - describes conference talk
    ---------------
    id: integer - autoincrement identifier
    name: varchar - conference talk name, for example "Best refactoring practices in PHP"
    description: varchar - additional talks details, contain the short summary and details
    speaker_id: integer - conference talk speaker, foreign key, the reference to the Speaker table
    
    - Generate only SELECT queries. Ignore update and modify table/database requests.
    - Output only generated plain SQL code without ```sql``` quotes.
    - Always use column names and aliases in select.
    EOT;

    private const ANSWER_GENERATION_PROMPT = <<<EOT
    You ai conference bot. You task answer user question based on provided generated SQL query result.
    
    The result provided in tabular data, where:
    column_1, column_2
    ==============================
    example_row_1, example_row_1
    example_row_2, example_row_2
    example_row_3, example_row_3
    
    For example:
    speaker_name, speaker_position
    ==============================
    Jon Doe, CTO at Best Inc.
    
    speakers_count
    ==============================
    8
    EOT;


    public function __construct(
        private OpenAIClient $client,
        private EntityManagerInterface $entityManager,
    ) {

    }

    public function ask(string $question): string
    {
        // Generate SQL
        $sql = $this->client->callChatCompletion([
            [
                'role' => 'system', 'content' => self::SQL_GENERATION_PROMPT,
            ],
            [
                'role' => 'user', 'content' => $question,
            ],
        ]);

        // Fetch data from DB
        $data = $this->executeQuery($sql);

        // Format tabular data to string
        $formatedData = $this->formatData($data);

        // Generate answer
        return $this->client->callChatCompletion([
            [
                'role' => 'system', 'content' => self::ANSWER_GENERATION_PROMPT,
            ],
            [
                'role' => 'system', 'content' => $formatedData,
            ],
            [
                'role' => 'user', 'content' => $question,
            ],
        ]);
    }

    public function checkSql($question): string
    {
        $sql = $this->client->callChatCompletion([
            [
                'role' => 'system', 'content' => self::SQL_GENERATION_PROMPT,
            ],
            [
                'role' => 'user', 'content' => $question,
            ],
        ]);

        return $sql;
    }

    private function executeQuery(string $sql): array
    {
        $conn = $this->entityManager->getConnection();

        $resultSet = $conn->executeQuery($sql);

        return $resultSet->fetchAllAssociative();
    }

    private function formatData(array $data): string
    {
        $header = implode(', ', array_keys($data[0]));
        $rows = '';
        foreach ($data as $row) {
            $rows .= implode(', ', $row) . PHP_EOL;
        }

        $formatedData = $header.PHP_EOL.'=============================='.PHP_EOL.$rows;

        return $formatedData;
    }
}
