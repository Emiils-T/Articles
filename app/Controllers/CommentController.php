<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Database\CommentDatabase;
use App\RedirectResponse;

class CommentController
{
    private CommentDatabase $database;

    public function __construct(CommentDatabase $database)
    {

        $this->database = $database;
    }

    public function postComment():RedirectResponse
    {
        $author = $_POST['author'];
        $content = $_POST['content'];
        $articleId = $_POST['articleID'];
        $title = $_POST['title'];


        $comment = new Comment($author,$content,$articleId);

        $this->database->insert($comment);

        return new RedirectResponse("/articles/$title");

    }

    public function likeComment():RedirectResponse//TODO add delete comments by id
    {
        $title = $_POST['title'];
        $commentID = $_POST['commentID'];
        $comment = $this->database->searchByID($commentID);
        $data = ['likes'=>$comment->getLikes()+1];
        $this->database->update($commentID,$data);

        return new RedirectResponse("/articles/$title");
    }
    public function deleteComment():RedirectResponse
    {
        $title = $_POST['title'];
        $commentID = $_POST['commentID'];

        $this->database->delete($commentID);
        return new RedirectResponse("/articles/$title");

    }

}