<?php

namespace App\Services;

use App\Database\ArticleDatabase;
use App\Models\Article;
use App\RedirectResponse;
use Carbon\Carbon;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
class CreateArticleService
{
    private ArticleDatabase $database;
    private LoggerInterface $logger;

    public function __construct(ArticleDatabase $database,LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function execute(string $title, string $content)
    {
        $minCharLength = 5;
        $contenttMinLength = 10;
        if(empty($title) | empty($content) ) {
            $this->logger->warning("[VALIDATION ERROR] One or more fields are empty");
            return new RedirectResponse('/error');
        }
        if(
            strlen(trim($title)) < $minCharLength |
            strlen(trim($content)) < $contenttMinLength
        ){
            $this->logger->warning("[VALIDATION ERROR] Title or content too short test");
            return new RedirectResponse('/error');
        }

        $article = new Article(
            $title,
            $content,
            $date=Carbon::now(),
        );

        $this->database->insert($article);

        $this->logger->info("[CREATE] $title - $date");
        return new RedirectResponse("/");
    }
}