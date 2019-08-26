<?php

require_once "config.php";
require_once "helpers.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$title = "Дела в порядке";

$projects = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$tasks_list = [
    [
        "task" => "Собеседование в IT компании",
        "date" => "01.12.2019",
        "category" => "Работа",
        "is_complete" => false
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date" => "25.12.2019",
        "category" => "Работа",
        "is_complete" => false
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date" => "21.12.2019",
        "category" => "Учеба",
        "is_complete" => true
    ],
    [
        "task" => "Встреча с другом",
        "date" => "22.12.2019",
        "category" => "Входящие",
        "is_complete" => false
    ],
    [
        "task" => "Купить корм для кота",
        "date" => "28.08.2019",
        "category" => "Домашние дела",
        "is_complete" => false
    ],
    [
        "task" => "Заказать пиццу",
        "date" => null,
        "category" => "Домашние дела",
        "is_complete" => false
    ]
];

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