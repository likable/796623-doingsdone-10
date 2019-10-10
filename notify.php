<?php

require_once "database.php";

//Поиск истекающих заданий
$query_tasks = "SELECT task_title, task_expiration, email, name FROM tasks "
        . "LEFT JOIN users ON users.id = author_id WHERE status = 0 AND "
        . "DATEDIFF(task_expiration, NOW()) = 0 ORDER BY email;";
$query_tasks_result = mysqli_query($connect, $query_tasks);
$tasks_list = mysqli_fetch_all($query_tasks_result, MYSQLI_ASSOC);

//группировка задач по почтовым адресам
$unique_emails = [];
$email_grouped_tasks = [];

foreach ($tasks_list as $task_item) {
    if (!in_array($task_item["email"], $unique_emails)) {
        $unique_emails[] = $task_item["email"];
    }
}

foreach ($unique_emails as $email) {
    foreach ($tasks_list as $task_item) {
        if ($email === $task_item["email"]) {
            $email_grouped_tasks[$email]["tasks"][] = $task_item["task_title"];
            $email_grouped_tasks[$email]["name"] = $task_item["name"];
            $email_grouped_tasks[$email]["dt"] = $task_item["task_expiration"];
        }
    }
}

//отправка писем
foreach ($email_grouped_tasks as $user_mail => $data) {
    $tasks_string = implode(", ", $data["tasks"]);
    $name = $data["name"];
    $dt = $data["dt"];
    
    $mail_content = "Уважаемый, {$name}. У вас запланирована задача ";
    $mail_content .= "\"{$tasks_string}\" на {$dt}";
    
    $mail_theme = "Уведомление от сервиса «Дела в порядке»";

    //Конфигурация транспорта
    $transport = new Swift_SmtpTransport("phpdemo.ru", 25);
    $transport->setUsername("keks@phpdemo.ru");
    $transport->setPassword("htmlacademy");

    //Формирование сообщения
    $message = new Swift_Message($mail_theme);
    $message->setTo([$user_mail => 'Пользователью сервиса «Дела в порядке»']);
    $message->setBody($mail_content);
    $message->setFrom("keks@phpdemo.ru");

    //Отправка сообщения
    $mailer = new Swift_Mailer($transport);
    $mailer->send($message);
}
