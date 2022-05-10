<?php
$server_name = 'localhost';
$username = '';
$password = '';
$db_name = '';

$conn = new mysqli($server_name, $username, $password, $db_name);

if ($conn->connect_error) {
    die('sql server connection error: ' . $conn->connect_error);
}

$conn->query('SET NAMES UTF8'); // 設定編碼
$conn->query('SET time_zone = "+8:00"'); // 設定台灣時間
?>