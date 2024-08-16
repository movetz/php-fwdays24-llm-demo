<?php
declare(strict_types=1);

namespace App\AiBot;

interface BotInterface
{
    public function ask(string $question): string;
}
