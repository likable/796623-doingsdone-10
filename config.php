<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '30M');

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU');

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "doingsdone");