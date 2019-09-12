<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

http_response_code(404);

$title = "Ошибка";
$user_name = "Виталий";

$error_num = 0; 
if (isset($_GET["error_num"])) {
    $error_num = $_GET["error_num"];
}

switch ($error_num) {
    case 2 :
        $error_text = "Такого проекта не существует";
        break;
    case 3 :
        $error_text = "В этом проекте ещё нет задач";
        break;
    default :
        $error_text = "Ошибка 404";
}

$content = include_template("error_tmp.php", [
    "projects"   => $projects,
    "tasks_list" => $tasks_list,
    "error_text" => $error_text
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);
