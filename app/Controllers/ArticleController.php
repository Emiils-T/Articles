<?php

namespace App\Controllers;

use App\Database\ArticleDatabase;
use App\Models\Article;
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


    public function __construct
    (
        ArticleDatabase $database,
        createArticleService $createArticleService,
        DeleteArticleService $deleteArticleService,
        UpdateArticleService $updateArticleService
    )
    {
        $this->database = $database;

        $this->createArticleService = $createArticleService;
        $this->deleteArticleService = $deleteArticleService;
        $this->updateArticleService = $updateArticleService;
    }

    public function index(): Response
    {
        $data = $this->database->getAll();
        return new Response('index.html.twig', ['items' => $data]);
    }
    public function show(string $title):Response
    {
        $data = $this->database->search($title);
        return new Response('show.html.twig', [
            'items' => [$data]
        ]);
    }

    public function search(): Response
    {

        $title = $_POST['title'];
        $data = $this->database->search($title);
        return new Response('search.html.twig', ['items' => [$data]]);

    }
    public function deleteForm():Response
    {
        return new Response('delete.html.twig');
    }
    public function delete(): RedirectResponse
    {
        $title = $_POST['title'];

        return $this->deleteArticleService->execute($title);

    }
    public function createForm():Response
    {
        return new Response('create.html.twig');
    }
    public function create(): RedirectResponse
    {
        $title = $_POST['title'];
        $content = $_POST['content'];


        return $this->createArticleService->execute($title,$content);
    }
    public function updateForm():Response
    {
        return new Response('update.html.twig');
    }
    public function update(): RedirectResponse
    {
        $title = $_POST['title'];
        $newTitle = $_POST['newTitle'];
        $newContent = $_POST['newContent'];

        return $this->updateArticleService->execute($title,$newTitle,$newContent);
        //RedirectResponse is inside updateArticleClass
    }
    public function showError()
    {
        return new Response('error.html.twig');
    }

}