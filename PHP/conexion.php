<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "handapp";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>