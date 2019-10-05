<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

//$user_id, $projects and $tasks_list in database.php now
$title = "Регистрация";
$user_name = "Виталий";

/******************************************************************
 * 
 * Внимание, для незалогиненого пользователя недолжно быть кнопки
 * "Добавить задачу" в правом верхнем углу
 * 
 ******************************************************************/

$new_user_email = "";
$new_user_password = "";
$new_user_name = "";

//Сохранение информации из заполненных полей
if (!empty($_POST["email"])) {
    $new_user_email = htmlspecialchars(trim($_POST["email"]));
}
if (!empty($_POST["password"])) {
    $new_user_password = htmlspecialchars(trim($_POST["password"]));
}
if (!empty($_POST["name"])) {
    $new_user_name = htmlspecialchars(trim($_POST["name"]));
}

$required_fields = ["email", "password", "name"];
$errors = [];

//Проверка формы на отправленность
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Проверка адреса электронной почты на валидность
    if (!filter_var($new_user_email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "E-mail введён некорректно";
    }
    
    //Проверка на заполненность обязательных полей и их длину
    foreach ($required_fields as $required_field) {
        if (empty(trim($_POST[$required_field]))) {
            $errors[$required_field] = "Поле обязательно для заполнения";
        } elseif (strlen(trim($_POST[$required_field])) > 127) {
            $errors[$required_field] = "Введена слишком длинная строка";
        }
    }

    //Проверка на существование почтового адреса в БД  
    if (empty($errors["email"])) {
        $query_email = "SELECT id FROM users WHERE email = ?;";
        $stmt_email = db_get_prepare_stmt($connect, $query_email, 
                [$new_user_email]);
        mysqli_stmt_execute($stmt_email);
        $stmt_email_result = mysqli_stmt_get_result($stmt_email);
        if (mysqli_num_rows($stmt_email_result)) {
            $errors["email"] = "Пользователь с такой почтой уже существует";
        }
    }
 
    //все проверки пройдены, сохраняем пользователя в БД
    if (count($errors) === 0) {
        //Создание хэша пароля для его хранения в БД
        $pass_hash = password_hash($new_user_password, PASSWORD_DEFAULT);
        
        //Добавление в базу
        $query_new_user = "INSERT INTO users (email, name, password) "
                . "VALUES (?, ?, ?);";
        $stmt_new_user = db_get_prepare_stmt($connect, $query_new_user, 
                [$new_user_email, 
                 $new_user_name, 
                 $pass_hash]);
        mysqli_stmt_execute($stmt_new_user);

        //редирект на главную
        header("Location: /index.php");
    }
}

$content = include_template("register_tmp.php", [
    "projects"          => $projects,
    "tasks_list"        => $tasks_list,
    "new_user_email"    => $new_user_email,
    "new_user_password" => $new_user_password,
    "new_user_name"     => $new_user_name,
    "errors"            => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);