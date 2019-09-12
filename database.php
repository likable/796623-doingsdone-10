<?php

require_once "config.php";
require_once "helpers.php";

$user_id = 1;
$projects = [];
$tasks_list = [];

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connect == false) {
    die("<u><em>Ошибка подключения к базе данных</em></u>");
} else {
    mysqli_set_charset($connect, "utf8");
    
    $query_projects = "SELECT id, title FROM projects WHERE author_id = ?";
    $stmt_projects = db_get_prepare_stmt($connect, $query_projects, [$user_id]);
    mysqli_stmt_execute($stmt_projects);
    $stmt_projects_result = mysqli_stmt_get_result($stmt_projects);
    $projects = mysqli_fetch_all($stmt_projects_result, MYSQLI_ASSOC);
    
    $query_tasks = "SELECT status, task_title, file_path, task_expiration, "
        . "title AS category FROM tasks t LEFT JOIN projects p "
        . "ON project_id = p.id WHERE t.author_id = ?";
    $stmt_tasks = db_get_prepare_stmt($connect, $query_tasks, [$user_id]);
    mysqli_stmt_execute($stmt_tasks);
    $stmt_tasks_result = mysqli_stmt_get_result($stmt_tasks);
    $tasks_list = mysqli_fetch_all($stmt_tasks_result, MYSQLI_ASSOC);
}
