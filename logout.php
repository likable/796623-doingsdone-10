<?php

if(session_id() === '') {
    session_start();
}
$_SESSION = [];
header("Location: /index.php");
