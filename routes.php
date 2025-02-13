<?php

return [
    ["GET","/",[App\Controllers\ArticleController::class,"index"]],
    ["POST","/search",[App\Controllers\ArticleController::class,"search"]],

    ["GET","/articles/{title}",[App\Controllers\ArticleController::class,"show"]],

    ["GET","/error",[App\Controllers\ArticleController::class,"showError"]],


    ["GET","/create",[App\Controllers\ArticleController::class,"createForm"]],
    ["POST","/create",[App\Controllers\ArticleController::class,"create"]],


    ["GET","/delete",[App\Controllers\ArticleController::class,"deleteForm"]],
    ["POST","/delete",[App\Controllers\ArticleController::class,"delete"]],

    ["GET","/update",[App\Controllers\ArticleController::class,"updateForm"]],
    ["POST","/update",[App\Controllers\ArticleController::class,"update"]],

];