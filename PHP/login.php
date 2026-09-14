<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM usuarios WHERE (usuario='$usuario' OR correo='$usuario') AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['foto'] = $user['foto'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] == 'admin') {
            echo json_encode(['status' => 'ok', 'rol' => 'admin']);
        } else {
            echo json_encode(['status' => 'ok', 'rol' => 'usuario']);
        }
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Usuario o contraseña incorrectos']);
    }
}
?>