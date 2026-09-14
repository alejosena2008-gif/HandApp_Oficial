<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $password = md5($_POST['password']);
    $rol = 'usuario';

    // Verificar si el usuario o correo ya existe
    $check = $conn->query("SELECT id FROM usuarios WHERE usuario='$usuario' OR correo='$correo'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'mensaje' => 'El usuario o correo ya está registrado']);
        exit;
    }

    $sql = "INSERT INTO usuarios (nombre, usuario, correo, password, rol) 
            VALUES ('$nombre', '$usuario', '$correo', '$password', '$rol')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'ok', 'mensaje' => 'Registro exitoso']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error al registrar']);
    }
}
?>