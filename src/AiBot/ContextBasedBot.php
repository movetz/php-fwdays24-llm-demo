<?php
declare(strict_types=1);

namespace App\AiBot;

class ContextBasedBot implements BotInterface
{
    private const PROMPT = <<<EOT
    You ai conference bot. Your task answer questions depends on provided details. If the question isn't related
    to the program say "Seems your question isn't related to the conference".
    
    Details:
    ==============
    Name: PHP Framework Days 2024. The short name - fwdays.
    The date: 17 Aug 2024
    Location: Kyiv, Podil Mall
    Description: the biggest and the most popular Ukrainian conference for PHP developers.
    There will be Ukrainian and international experts in the field of PHP, 
    discussions with top specialists, interesting cases.
    Talks:
    ---------
    Speaker: Mykhailo Bodnarchuk
    Position: CTO at Testomat.io
    Name: The right approaches to testing in PHP: Or how quantity can beat quality.
    ---------
    Speaker name: Vladyslav Pozdniakov
    Position: Software Engineer at MacPaw
    Name: The story of one sunset and five burnouts or how we migrated a legacy.
    ---------
    Speaker name: Maksym Mova
    Position: Engineering Manager at MacPaw
    Name: How to craft your AI bot using PHP. Step by step guide.
    ---------
    Speaker: Dmytro Nemesh
    Position: CTO at Lalafo
    Name: How we optimized our product without paid solutions

     Additional rules:
     =============
     - Do not use * symbols for formating
     - If user ask to say hello/hi, generate politely and friendly welcome message and
     share short conference details and program.
    EOT;

    public function __construct(
        private OpenAIClient $openAIclient
    ) {
    }

    public function ask(string $question): string
    {
        // POST https://api.openai.com/v1/chat/completions
        return $this->openAIclient->callChatCompletion([
            [
                'role' => 'system',
                'content' => self::PROMPT,
            ],
            [
                'role' => 'user',
                'content' => $question,
            ],
        ], 'gpt-3.5-turbo-0125');
    }
}
