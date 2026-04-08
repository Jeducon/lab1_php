<?php

$mysqli = new mysqli('localhost', 'root', '', 'gamezone_db');

if ($mysqli->connect_error) {
    die('Помилка підключення до БД: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');