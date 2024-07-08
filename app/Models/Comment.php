<?php

namespace App\Models;

use Carbon\Carbon;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

class Comment
{
    private string $author;
    private string $content;
    private int $likes;
    private string $commentID;
    private ?Carbon $date;
    private string $articleId;

    public function __construct
    (
        string $author,
        string $content,
        string $articleId,
        int $likes = 0,
        Carbon $date = null,
        string $commentID =null
    )
    {
        $this->author = $author;
        $this->content = $content;
        $this->articleId = $articleId;
        $this->likes = $likes;
        $this->date = $date ?? Carbon::now("Europe/Riga");
        $this->commentID = $commentID ?? Uuid::uuid4();
    }
    public function getDate(): ?Carbon
    {
        return $this->date;
    }
    public function getCommentID(): string
    {
        return $this->commentID;
    }
    public function getAuthor(): string
    {
        return $this->author;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getLikes(): ?int
    {
        return $this->likes;
    }
    public function getArticleId(): string
    {
        return $this->articleId;
    }

}