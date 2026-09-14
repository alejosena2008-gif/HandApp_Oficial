<?php
<<<<<<< HEAD
=======
session_start();

>>>>>>> cf8d436cfed3aa7b45a4cf179e038a78e7e2d3b9
$host = "localhost";
$user = "root";
$pass = "";
$db = "handapp";

$conn = new mysqli($host, $user, $pass, $db);
<<<<<<< HEAD
=======
$conn->set_charset("utf8");
>>>>>>> cf8d436cfed3aa7b45a4cf179e038a78e7e2d3b9

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>