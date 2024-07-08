<?php

namespace App\Models;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class Article
{
    private string $title;
    private string $content;
    private Carbon $date;
    private ?string $uuid;
    private ?int $likes;

    public function __construct(string $title, string $content, Carbon $date , string $uuid = null ,int $likes = 0)//TODO add likes=0
    {
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
        $this->uuid = $uuid;
        $this->likes = $likes;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    public function getLikes(): ?int
    {
        return $this->likes;
    }
}