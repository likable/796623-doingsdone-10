<?php

require_once "config.php";
require_once "helpers.php";
require_once "database.php";
require_once "vendor/autoload.php";

if(session_id() == '') {
    session_start();
}
$user_id = $_SESSION["user_id"] ?? "";
$user_name = $_SESSION["user_name"] ?? "";
$show_complete_tasks = $_SESSION["show_complete_tasks"] ?? 0;

//смена видимости выполненных задач
if (!empty($_GET["show_complete_tasks"])) {
    if ($show_complete_tasks === 0) {
        $_SESSION["show_complete_tasks"] = 1;
    } else {
        $_SESSION["show_complete_tasks"] = 0;
    }
    header("Location: /index.php");
}

//$projects and $tasks_list are in database.php
$title = "Дела в порядке";

//проверка анонимности пользователя
if (empty($user_id)) {
    //показ гостевой страницы
    $content = include_template("guest_tmp.php", []);
    
} else {
    //работа с авторизованным пользователем
    
    $project_id = -1;
    if (isset($_GET["project_id"])) {
        $project_id = $_GET["project_id"];
    }
    
    //проверка на наличие проектов
    if (count($projects) === 0) {
        //Нет ни одного проекта
        header("Location: /error.php?error_num=1");
        die();
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

    $param_tasks_list = [];
    
    //показ записей из выбранного проекта
    if ($project_id === -1) {
        $param_tasks_list = $tasks_list;
    } else {
        $query_param = "SELECT status, task_title, file_path, task_expiration, "
            . "title AS category, t.id AS tid FROM tasks t "
            . "LEFT JOIN projects p ON project_id = p.id "
            . "WHERE t.author_id = ? AND project_id = ? "
            . "ORDER BY dt_add DESC;";
        $stmt_param = db_get_prepare_stmt($connect, $query_param, 
                [$user_id, $project_id]);
        mysqli_stmt_execute($stmt_param);
        $stmt_param_result = mysqli_stmt_get_result($stmt_param);
        $projects_count = mysqli_num_rows($stmt_param_result);
        $param_tasks_list = mysqli_fetch_all($stmt_param_result, MYSQLI_ASSOC);
        if ($projects_count === 0) {
            //Записей в проекте не обнаружено
            header("Location: /error.php?error_num=3");
            die();
        }
    }
    
    //проверка поискового запроса
    $get_search = $_GET["search"] ?? "";
    $search = trim($get_search);
    
    if (!empty($search)) {
        $query_search = "SELECT status, task_title, file_path, "
            . "task_expiration FROM tasks "
            . "WHERE MATCH(task_title) AGAINST(?) AND author_id = ? "
            . "ORDER BY dt_add DESC;";
        $stmt_search = db_get_prepare_stmt($connect, $query_search, 
                [$search, $user_id]);
        mysqli_stmt_execute($stmt_search);
        $stmt_search_result = mysqli_stmt_get_result($stmt_search);
        $param_tasks_list = mysqli_fetch_all($stmt_search_result, MYSQLI_ASSOC);
    }
    
    //фильтрация задач
    $filtred_tasks_list = [];
    
    $tasks_switch_mode = $_GET["tasks_switch_mode"] ?? false;
    if ($tasks_switch_mode) {
        $_SESSION["tasks_switch_mode"] = $tasks_switch_mode;
    }
    $tasks_switch_mode = $_SESSION["tasks_switch_mode"] ?? "all";
    
    foreach ($param_tasks_list as $param_task) {
        $task_time = strtotime($param_task["task_expiration"]);
        $task_day = date("Y-m-d", $task_time);
        $today = date("Y-m-d");
        $tomorrow = date("Y-m-d", time() + 86400);
        
        if ($tasks_switch_mode == "today" && $task_day == $today) {
            $filtred_tasks_list[] = $param_task;
        } elseif ($tasks_switch_mode == "tomorrow" && $task_day == $tomorrow) {
            $filtred_tasks_list[] = $param_task;
        } elseif ($tasks_switch_mode == "expired" && 
                $task_time < strtotime($today) && $task_time > 1) {
            $filtred_tasks_list[] = $param_task;
        } elseif ($tasks_switch_mode == "all") {
            $filtred_tasks_list[] = $param_task;
        }
    }

    $param_tasks_list = $filtred_tasks_list;
    
    //смена статуса задачи
    $task_id_for_change_status = $_GET["task_id_for_change_status"] ?? -1;

    //проверка на принадлежность задачи пользователю
    $is_users_task = false;
    $last_status = -1;
    $new_status = -1;
    
    foreach ($tasks_list as $tasks_list_item) {
        if ($tasks_list_item["tid"] == $task_id_for_change_status) {
            $is_users_task = true;
            $last_status = $tasks_list_item["status"];
            break;
        }
    }
    
    //запрос на изменение статуса
    if ($task_id_for_change_status !== -1 && $is_users_task) {
        //новое значение статуса
        if ($last_status === 1) {
            $new_status = 0;
        } else {
            $new_status = 1;
        }
        
        $query_status = "UPDATE tasks SET status = ? WHERE id = ?;";
        $stmt_status = db_get_prepare_stmt($connect, $query_status, 
                [$new_status, $task_id_for_change_status]);
        mysqli_stmt_execute($stmt_status);
        
        //обновление страницы для отображения изменений
        header("Location: /index.php");
    }
    
    $content = include_template("main.php", [
        "projects"            => $projects,
        "show_complete_tasks" => $show_complete_tasks,
        "tasks_list"          => $tasks_list,
        "param_tasks_list"    => $param_tasks_list,
        "project_id"          => $project_id,
        "tasks_switch_mode"   => $tasks_switch_mode
    ]);
}

$layout = include_template("layout.php", [
    "title"     => $title,
    "user_name" => $user_name,
    "content"   => $content
]);

print($layout);
