<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Дела в порядке";
$user_name = "Виталий";

//$user_id and $tasks_list in database.php now

$project_id = -1;
if (isset($_GET["project_id"])) {
    $project_id = $_GET["project_id"];
}

//проверка на наличие параметра и существование проекта у пользователя
$query_status = false;
foreach ($projects as $project) {
    if ($project_id == $project["id"] || $project_id === -1) {
        $query_status = true;
    }
}

if (!$query_status) {
    //Несуществующий или пустой параметр запроса
    header("Location: /error.php?error_num=2");
    die();
}

if ($project_id === -1) {
    $param_tasks_list = $tasks_list;
} else {
    $query_param = "SELECT status, task_title, file_path, task_expiration, "
        . "title AS category FROM tasks t LEFT JOIN projects p "
        . "ON project_id = p.id WHERE t.author_id = ? AND project_id = ?";
    $stmt_param = db_get_prepare_stmt($connect, $query_param, 
            [$user_id, $project_id]);
    mysqli_stmt_execute($stmt_param);
    $stmt_param_result = mysqli_stmt_get_result($stmt_param);
    $projects_count = mysqli_num_rows($stmt_param_result);
    $param_tasks_list = mysqli_fetch_all($stmt_param_result, MYSQLI_ASSOC);
    if ($projects_count === 0) {
        //Записей с таким параметром не обнаружено
        header("Location: /error.php?error_num=3");
        die();
    }
}

$content = include_template("main.php", [
    "projects"            => $projects,
    "show_complete_tasks" => $show_complete_tasks,
    "tasks_list"          => $tasks_list,
    "param_tasks_list"    => $param_tasks_list,
    "project_id"          => $project_id
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);
