<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

if(session_id() == '') {
    session_start();
}
$user_id = $_SESSION["user_id"] ?? "";
$user_name = $_SESSION["user_name"] ?? "";

//незалогиненный пользователь не должен видеть эту страницу
if (empty($user_id)) {
    header("Location: /");
    exit;
}

//$projects and $tasks_list are in database.php
$title = "Добавление проекта";

$new_project_name = "";

//Сохранение информации из заполненных полей
if (!empty($_POST["name"])) {
    $new_project_name = htmlspecialchars(trim($_POST["name"]));
}

$required_fields = ["name"];
$errors = [];

//Проверка на отправление формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Проверка на заполненность обязательных полей
    foreach ($required_fields as $required_field) {
        //нужна двойная проверка, т.к. если в массиве $_POST не будет элемента,
        //то применение к нему функции trim вызовет ошибку
        if (empty($_POST[$required_field]) || 
                empty(trim($_POST[$required_field]))) {
            $errors[$required_field] = "Поле обязательно для заполнения";
        }  
    }
    
    //Проверка на длину текста
    if (strlen($new_project_name) > 63) {
        $errors["name"] = "Слишком длинное название";
    }
    
    //Проверка на существование проекта
    foreach ($projects as $project) {
        if (mb_strtolower($new_project_name) === 
                mb_strtolower($project["title"])) {
            $errors["name"] = "Проект с таким названием уже существует";
            break;
        }
    }

    //Добавляю проект в БД
    if (count($errors) === 0) {
        $query_new_project = "INSERT INTO projects (title, author_id) "
                . "VALUES (?, ?);";
        $stmt_new_project = db_get_prepare_stmt($connect, $query_new_project, 
                [$new_project_name, $user_id]);
        mysqli_stmt_execute($stmt_new_project);

        //редирект на главную
        header("Location: /index.php");
    }
}

$content = include_template("project_tmp.php", [
    "projects"         => $projects,
    "tasks_list"       => $tasks_list,
    "new_project_name" => $new_project_name,
    "errors"           => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);