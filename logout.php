<?php

session_start();
$_SESSION = [];
setcookie('login', '', time());
setcookie('ikey', '', time());
setcookie(session_name(), '', time(), '/');
session_destroy();

echo "<h1>Досвидания.</h1>";

echo "<a href='index.php'>Вернуться на главную.</a>";