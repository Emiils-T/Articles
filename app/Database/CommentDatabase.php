<?php

namespace App\Database;

use App\Models\Comment;
use Carbon\Carbon;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class CommentDatabase
{
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

        $articleTable = $schema->createTable('comment_database');
        $articleTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $articleTable->addColumn('author', 'string', ['notnull' => true]);
        $articleTable->addColumn('content','string', ['notnull' => true]);
        $articleTable->addColumn('likes','integer');
        $articleTable->addColumn('date','string');
        $articleTable->addColumn('commentID','string');
        $articleTable->addColumn('articleID','string');


        $platform = $dbalConnection->getDatabasePlatform();
        $sqls = $schema->toSql($platform);
        foreach ($sqls as $sql) {
            $dbalConnection->executeStatement($sql);
        }
    }
    public function insert(Comment $comment): void
    {
        $query = 'INSERT INTO comment_database (author, content, likes, date, commentID, articleID) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->dbalConnection->prepare($query);
        $stmt->bindValue(1, $comment->getAuthor());
        $stmt->bindValue(2, $comment->getContent());
        $stmt->bindValue(3, $comment->getLikes());
        $stmt->bindValue(4, $comment->getDate());
        $stmt->bindValue(5, $comment->getCommentID());
        $stmt->bindValue(6, $comment->getArticleId());
        $stmt->executeQuery();
    }
    public function getAllForArticle(string $articleID):array
    {
        $comments = $this->dbalConnection->fetchAllAssociative(
            "SELECT * FROM comment_database WHERE articleID = :articleID",[ 'articleID' => $articleID]
    );
        $items = [];
        foreach ($comments as $comment) {
            $comment = new Comment
            (
                $comment['author'],
                $comment['content'],
                $comment['articleID'],
                $comment['likes'],
                Carbon::parse($comment['date']),
                $comment['commentID'],
            );
            $items[] = $comment;
        }
        return $items;
    }
    public function searchByID(string $commentID):?Comment
    {
        $query = 'SELECT * FROM comment_database WHERE commentID = :commentID';
        $stmt = $this->dbalConnection->prepare($query);
        $stmt->bindValue(':commentID', $commentID);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        if ($result) {
            return new Comment(
                $result[0]['author'],
                $result[0]['content'],
                $result[0]['articleID'],
                $result[0]['likes'],
                Carbon::parse($result[0]['date'],"Europe/Riga"),
                $result[0]['commentID'],
            );
        }
        return null;

    }
    public function delete(string $id): void
    {
        $this->dbalConnection->delete('comment_database', ['commentID' => $id]);
    }
    public function update(string $id, array $data): void
    {
        $this->dbalConnection->update('comment_database', $data, ['commentID' => $id]);
    }

}