<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

//$user_id, $projects and $tasks_list in database.php now
$title = "Регистрация";
$user_name = "Виталий";











$content = include_template("register_tmp.php", [
    "projects"         => $projects,
    "tasks_list"       => $tasks_list,
    //"new_task_name"    => $new_task_name,
    //"new_task_project" => $new_task_project,
    //"new_task_date"    => $new_task_date,
    //"errors"           => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);