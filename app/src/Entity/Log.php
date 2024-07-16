<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: LogRepository::class)]
#[Table(name: 'logs')]
final class Log
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[Column(type: 'string')]
    private string $channel;

    #[Column(type: 'string')]
    private string $level;
    #[Column(type: 'text')]
    private string $message;

    #[Column(type: 'datetime')]
    private \DateTime $time;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    public function setTime(\DateTime $time): void
    {
        $this->time = $time;
    }
}
