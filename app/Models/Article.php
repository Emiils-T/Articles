<?php

namespace App\Models;

use Carbon\Carbon;

class Article
{
    private string $title;
    private string $content;
    private Carbon $date;

    public function __construct(string $title, string $content, Carbon $date)
    {

        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
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

}