<?php

require_once "database.php";

//$user_id, $user_name, $projects and $tasks_list are in database.php

//незалогиненный пользователь не должен видеть эту страницу
if (empty($user_id)) {
    header("Location: /");
    exit;
}

$title = "Добавление задачи";

$new_task_name = "";
$new_task_project = "";
$new_task_project_id = "";
$new_task_date = null;

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

$required_fields = ["name", "project"];
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
    
    //Проверка даты, если она введена
    if ($new_task_date) {
        //Проверка на соответствие формату, а затем на актуальность
        if (!is_date_valid($new_task_date)) {
            $errors["date"] = "Неправильный формат даты";
        } elseif (strtotime($new_task_date) < strtotime(date("Y-m-d"))) {
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

    //Проверка названия на соответствие формату ячейки базы данных
    if (strlen($new_task_name) > 127) {
        $errors["name"] = "Слишком длинное название";
    }
    
    //Проверка на наличие файла и его размер
    if (!empty($_FILES["file"]["tmp_name"])) {
        if ($_FILES["file"]["size"] > 20000000) {
            $errors["file"] = "Размер файла превышает 20МБ";
        } elseif (strlen($_FILES["file"]["name"]) > 40) {
            $errors["file"] = "Имя файла слишком длинное";
        }
    }
    
    //валидация
    if (count($errors) === 0) {
        //перенос файла из временной папки в постоянную
        $file_name = null;
        if (!empty($_FILES["file"]["tmp_name"])) {
            $file_name = time() . "-" . $_FILES["file"]["name"];
            $file_new_path = __DIR__ . "/uploads/";
            move_uploaded_file($_FILES["file"]["tmp_name"], 
                    $file_new_path . $file_name);
        }

        //Добавление в базу
        $query_new_task = "INSERT INTO tasks (task_title, file_path, "
                . "task_expiration, author_id, project_id) VALUES "
                . "(?, ?, ?, ?, ?);";
        $stmt_new_task = db_get_prepare_stmt($connect, $query_new_task, 
                [$new_task_name, 
                 $file_name, 
                 $new_task_date, 
                 $user_id, 
                 $new_task_project_id]);
        mysqli_stmt_execute($stmt_new_task);

        //редирект на главную
        header("Location: /index.php");
    }
}

$section = include_template("section.php", [
    "projects"   => $projects,
    "tasks_list" => $tasks_list,
    "project_id" => -1
]);

$content = include_template("add_tmp.php", [
    "section"          => $section,
    "new_task_name"    => $new_task_name,
    "new_task_project" => $new_task_project,
    "new_task_date"    => $new_task_date,
    "errors"           => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);