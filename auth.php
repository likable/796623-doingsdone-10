<?php

require_once "database.php";

//$user_id, $user_name, $projects and $tasks_list are in database.php

//залогиненный пользователь не должен видеть эту страницу
if (!empty($user_id)) {
    header("Location: /");
    exit;
}

$title = "Вход";

$email = "";
$password = "";

if (!empty($_POST["email"])) {
    $email = htmlspecialchars(trim($_POST["email"]));
}
if (!empty($_POST["password"])) {
    $password = htmlspecialchars(trim($_POST["password"]));
}

$required_fields = ["email", "password"];
$errors = [];
$user = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //проверка email на формат
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "E-mail введён некорректно";
    }
    
    //проверка на заполнение обязательных полей
    foreach ($required_fields as $required_field) {
        if (empty(trim($_POST[$required_field]))) {
            $errors[$required_field] = "Поле обязательно для заполнения";
        }
    }
    
    //поиск в БД пользователя с введённым email
    if (count($errors) === 0) {
        $user_query = "SELECT * FROM users WHERE email = ?;";
        $stmt_user = db_get_prepare_stmt($connect, $user_query, [$email]);
        mysqli_stmt_execute($stmt_user);
        $stmt_user_result = mysqli_stmt_get_result($stmt_user);
        $user = mysqli_fetch_assoc($stmt_user_result);
        
        if (mysqli_num_rows($stmt_user_result) === 0) {
            $errors["email"] = "Пользователя с таким E-mail не обнаружено";
        }
    }
    
    //проверка пароля
    if (count($errors) === 0) {
        if (!password_verify($password, $user["password"])) {
            $errors["password"] = "Введён неправильный пароль";
        }
    }
    
    //вход на сайт
    if (count($errors) === 0) {
        if (session_id() == '') {
            session_start();
        }
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        header("Location: /index.php");
    }
}

$content = include_template("auth_tmp.php", [
    "email"     => $email,
    "password"  => $password,
    "errors"    => $errors
]);

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);
