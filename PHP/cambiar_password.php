<?php
include("conexion.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'No hay sesión activa']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = $_SESSION['id'];
    $pass_actual = md5($_POST['pass_actual']);
    $pass_nueva  = md5($_POST['pass_nueva']);

    // Verificar que la contraseña actual es correcta
    $result = $conn->query("SELECT id FROM usuarios WHERE id=$id AND password='$pass_actual'");
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'mensaje' => 'La contraseña actual es incorrecta']);
        exit;
    }

    if ($conn->query("UPDATE usuarios SET password='$pass_nueva' WHERE id=$id")) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error al actualizar la contraseña']);
    }
}
?>