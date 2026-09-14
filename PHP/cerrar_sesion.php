<?php
include("conexion.php");
session_destroy();
header("Location: ../HTML/login.html");
exit;
?>
