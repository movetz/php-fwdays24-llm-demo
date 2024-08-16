<?php

namespace App\Entity;

use App\Repository\TalkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalkRepository::class)]
class Talk
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\Column(length: 2048)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Speaker::class, cascade: ['persist'])]
    private ?Speaker $speaker = null;

    #[ORM\Column(type: 'vector', length: 1536, nullable: true)]
    private ?array $embedding = null;

    public function __construct(string $name, string $description, Speaker $speaker)
    {
        $this->name = $name;
        $this->description = $description;
        $this->speaker = $speaker;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSpeaker(): ?Speaker
    {
        return $this->speaker;
    }

    public function setSpeaker(?Speaker $speaker): static
    {
        $this->speaker = $speaker;

        return $this;
    }

    public function setEmbedding(array $embedding): static
    {
        $this->embedding = $embedding;

        return $this;
    }

    public function getEmbedding(): array
    {
        return $this->embedding;
    }
}
