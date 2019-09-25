<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

//$user_id, $projects and $tasks_list in database.php now
$title = "Добавление задачи";
$user_name = "Виталий";

$new_task_name = "";
$new_task_project = "";
$new_task_project_id = "";
$new_task_date = "";

//Сохранение информации из заполненных полей
if (!empty($_POST["name"])) {
    $new_task_name = htmlspecialchars(trim($_POST["name"]));
}
if (!empty($_POST["project"])) {
    $new_task_project = htmlspecialchars(trim($_POST["project"]));
}
if (!empty($_POST["date"])) {
    $new_task_date = htmlspecialchars(trim($_POST["date"]));
}

$required_fields = ["name", "project", "blabla"];
$errors = [];

//Проверка на отправление формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Проверка на заполненность обязательных полей
    foreach ($required_fields as $required_field) {
        if (empty($_POST[$required_field]) || 
                empty(trim($_POST[$required_field]))) {
            $errors[$required_field] = "Поле обязательно для заполнения";
        }  
    }
    
    //Проверка даты, если она введена
    if ($new_task_date !== "") {
        //Проверка на соответствие формату, а затем на актуальность
        if (!is_date_valid($new_task_date)) {
            $errors["date"] = "Неправильный формат даты";
        } elseif (strtotime($new_task_date) < time()) {
            $errors["date"] = "Нельзя указывать дату из прошлого";
        }
    }
    
    //Проверка на существование проекта
    foreach ($projects as $project) {
        if ($new_task_project === $project["title"]) {
            $new_task_project_id = $project["id"] ?? "error";
            break;
        } else {
            $new_task_project_id = "error";
        }
    }
    //если не найден идентификатор проекта и не было ошибок с полем project...
    if (!is_numeric($new_task_project_id) && empty($errors["project"])) {
        $errors["project"] = "Указан несуществующий проект";
    }
    echo "new_task_project_id = {$new_task_project_id}";
    
    //Проверка на соответствие формату ячеек базы данных
    
    
    
    //Проверка на наличие файла и его размер

    
    

    

    
    //валидация
    //если ошибок нет, перенести файл из временной папки в нормальную
    //затем добавить информацию в БД
    //а потом сделать редирект
    if (count($errors) === 0) {
        echo "Ошибок нет";

    } else {
        echo "Дофига ошибок, ты куда смотришь?";
    }
    
    
    
    
    
    echo "<pre>";
    var_dump($errors);
    var_dump($projects);
    echo "</pre>";
    

}

//echo "<pre>";
//var_dump($_SERVER);
//echo "</pre>";






$content = include_template("add_tmp.php", [
    "projects"            => $projects,
    "tasks_list"          => $tasks_list,
    "new_task_name"       => $new_task_name,
    "new_task_project"    => $new_task_project,
    "new_task_date"       => $new_task_date,
    "errors"              => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);