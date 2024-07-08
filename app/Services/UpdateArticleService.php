<?php

namespace App\Services;

use App\Database\ArticleDatabase;
use App\RedirectResponse;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class UpdateArticleService
{
    private ArticleDatabase $database;
    private LoggerInterface $logger;

    public function __construct(ArticleDatabase $database,LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function execute
    (
        string $title,
        string $newTitle,
        string $newContent
    ): RedirectResponse
    {
        $minCharLength=5;
        if(empty($title) | empty($newTitle) | empty($newContent)) {
            $this->logger->warning("[VALIDATION ERROR] One or more fields are empty");
            return new RedirectResponse('/error');
        }
        if (strlen($newTitle| strlen($newContent<$minCharLength) )){
            $this->logger->warning("[VALIDATION ERROR] New title or new content too short");
        }
        if(
            strlen(trim($title)) < $minCharLength |
            strlen(trim($newTitle)) < $minCharLength |
            strlen(trim($newContent)) < $minCharLength
        ){
            return new RedirectResponse('/error');
        }
        $search = $this->database->search($title);
        if (empty($search)) {
            return new RedirectResponse('/error');
        }
        $data = [
            'title' => $newTitle,
            'content' => $newContent
        ];
        $this->database->update($title, $data);

        $date=Carbon::now();

        $this->logger->info("[UPDATE] $title - $date");

        return new RedirectResponse('/');

    }
}