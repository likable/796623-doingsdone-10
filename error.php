<?php

require_once "database.php";

//$user_id, $user_name, $projects and $tasks_list are in database.php

//незалогиненный пользователь не должен видеть эту страницу
if (empty($user_id)) {
    header("Location: /");
    exit;
}

$title = "Ошибка";

$error_num = 0; 
if (isset($_GET["error_num"])) {
    $error_num = $_GET["error_num"];
}

switch ($error_num) {
    case 1 :
        $error_text = "У вас ещё нет ни одного проекта";
        break;
    case 2 :
        http_response_code(404);
        $error_text = "Такого проекта не существует";
        break;
    case 3 :
        $error_text = "В этом проекте ещё нет задач";
        break;
    default :
        http_response_code(404);
        $error_text = "Ошибка 404";
}

$section = include_template("section.php", [
    "projects"   => $projects,
    "tasks_list" => $tasks_list,
    "project_id" => -1
]);

$content = include_template("error_tmp.php", [
    "section"    => $section,
    "error_text" => $error_text
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);
