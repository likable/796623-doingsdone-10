<?php

require_once "config.php";
require_once "helpers.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Дела в порядке";

$user_name = "Виталий";
$user_id = 2;
$projects = [];
$tasks_list = [];

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connect == false) {
    print("Ошибка подключения к базе данных");
} else {
    mysqli_set_charset($connect, "utf8");
    
    $query_projects = "SELECT title FROM projects WHERE author_id = ?";
    $stmt_projects = db_get_prepare_stmt($connect, $query_projects, [$user_id]);
    mysqli_stmt_execute($stmt_projects);
    $stmt_projects_result = mysqli_stmt_get_result($stmt_projects);
    $db_projects = mysqli_fetch_all($stmt_projects_result, MYSQLI_ASSOC);

    foreach($db_projects as $db_project) {
        $projects[] = $db_project["title"];
    }
    
    $query_tasks = "SELECT status, task_title, file_path, task_expiration, "
            . "title AS category FROM tasks t LEFT JOIN projects p "
            . "ON project_id = p.id WHERE t.author_id = ?";
    $stmt_tasks = db_get_prepare_stmt($connect, $query_tasks, [$user_id]);
    mysqli_stmt_execute($stmt_tasks);
    $stmt_tasks_result = mysqli_stmt_get_result($stmt_tasks);
    $tasks_list = mysqli_fetch_all($stmt_tasks_result, MYSQLI_ASSOC);
}

$content = include_template("main.php", [
    "projects"            => $projects,
    "show_complete_tasks" => $show_complete_tasks,
    "tasks_list"          => $tasks_list
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);