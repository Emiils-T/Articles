<?php

namespace App\Database;

use App\Models\Article;
use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;

class ArticleDatabase
{
    private string $filePath;
    private array $connectionParams;
    private Connection $dbalConnection;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $this->connectionParams = array(
            'driver' => 'pdo_sqlite',
            'path' => $this->filePath,
        );
        $this->dbalConnection = DriverManager::getConnection($this->connectionParams);
    }
    public function create():void
    {
        $dbalConnection = DriverManager::getConnection($this->connectionParams);
        $schema = new Schema();

        $articleTable = $schema->createTable('article_database');
        $articleTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $articleTable->addColumn('title', 'string', ['notnull' => true]);
        $articleTable->addColumn('content','string', ['notnull' => true]);
        $articleTable->addColumn('date','string');

        $platform = $dbalConnection->getDatabasePlatform();
        $sqls = $schema->toSql($platform);
        foreach ($sqls as $sql) {
            $dbalConnection->executeStatement($sql);
        }
    }
    public function insert(Article $article): void
    {
        $query = 'INSERT INTO article_database(title,content, date) VALUES(:title,:content, :date)';
        $stmt = $this->dbalConnection->prepare($query);
        $stmt->bindValue(':title', $article->getTitle());
        $stmt->bindValue(':content', $article->getContent());
        $stmt->bindValue(':date', $article->getDate());
        $stmt->executeQuery();
    }
    public function getAll():array
    {
        $articleData = $this->dbalConnection->fetchAllAssociative("SELECT * FROM article_database");
        $items = [];
        foreach ($articleData as $article) {
            $articleItem = new Article
            (
                $article['title'],
                $article['content'],
                Carbon::parse($article['date'])
            );
            $items[] = $articleItem;
        }
        return $items;
    }
    public function search(string $title):?Article
    {

        $query = 'SELECT * FROM article_database WHERE title = :title';
        $stmt = $this->dbalConnection->prepare($query);
        $stmt->bindValue(':title', $title);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        if ($result) {
            return new Article(
                $result[0]["title"],
                $result[0]["content"],
                Carbon::parse($result[0]["date"])
            );
        }
        return null;
    }
    public function delete(string $title): void
    {
        $this->dbalConnection->delete('article_database', ['title' => $title]);
    }
    public function update(string $title, array $data): void
    {
        $this->dbalConnection->update('article_database', $data, ['title' => $title]);
    }

}