<?php

namespace App\Controllers;

use App\Database\ArticleDatabase;
use App\Database\CommentDatabase;
use App\RedirectResponse;
use App\Response;
use App\Services\DeleteArticleService;
use App\Services\UpdateArticleService;
use App\Services\CreateArticleService;



class ArticleController
{
    private ArticleDatabase $database;
    private CreateArticleService $createArticleService;
    private DeleteArticleService $deleteArticleService;
    private UpdateArticleService $updateArticleService;
    private CommentDatabase $commentDatabase;


    public function __construct
    (
        ArticleDatabase $database,
        createArticleService $createArticleService,
        DeleteArticleService $deleteArticleService,
        UpdateArticleService $updateArticleService,
        CommentDatabase $commentDatabase
    )
    {
        $this->database = $database;

        $this->createArticleService = $createArticleService;
        $this->deleteArticleService = $deleteArticleService;
        $this->updateArticleService = $updateArticleService;
        $this->commentDatabase = $commentDatabase;
    }

    public function index(): Response
    {
        $data = $this->database->getAll();

        return new Response('Article/index.html.twig', ['items' => $data]);
    }
    public function show(string $title):Response
    {
        $data = $this->database->search($title);
        $comments = $this->commentDatabase->getAllForArticle($data->getUuid());
        return new Response('Article/show.html.twig', [
            'items' => [$data],
            'comments' => $comments
        ]);
    }

    public function search(): Response
    {

        $title = $_POST['title'];
        $data = $this->database->search($title);
        return new Response('Article/search.html.twig', ['items' => [$data]]);

    }
    public function deleteForm():Response
    {
        return new Response('Article/delete.html.twig');
    }
    public function delete(): RedirectResponse
    {
        $title = $_POST['title'];

        return $this->deleteArticleService->execute($title);

    }
    public function createForm():Response
    {
        return new Response('Article/create.html.twig');
    }
    public function create(): RedirectResponse
    {
        $title = $_POST['title'];
        $content = $_POST['content'];


        return $this->createArticleService->execute($title,$content);
    }
    public function updateForm():Response
    {
        return new Response('Article/update.html.twig');
    }
    public function update(): RedirectResponse
    {
        $title = $_POST['title'];
        $newTitle = $_POST['newTitle'];
        $newContent = $_POST['newContent'];

        return $this->updateArticleService->execute($title,$newTitle,$newContent);
        //RedirectResponse is inside updateArticleClass
    }
    public function like():RedirectResponse
    {
        $title = $_POST['title'];
        $id = $_POST['id'];
        $article = $this->database->searchByID($id);
        $data = ['likes'=>$article->getLikes()+1];

        $this->database->updateByID($id,$data);

        return new RedirectResponse("/articles/$title");
    }
    public function showError()
    {
        return new Response('error.html.twig');
    }

}