<?php

namespace App\Services;

use App\Database\ArticleDatabase;
use App\RedirectResponse;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class DeleteArticleService
{
    private ArticleDatabase $database;
    private LoggerInterface $logger;

    public function __construct(ArticleDatabase $database,LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function execute(string $title):RedirectResponse
    {
        $search = $this->database->search($title);
        if (empty($search)) {
            return new RedirectResponse('/error');
        }
        $this->database->delete($title);
        $date=Carbon::now();

        $this->logger->info("[DELETE] $title - $date");
        return new RedirectResponse('/');
    }
}