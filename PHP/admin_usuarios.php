<?php
include("conexion.php");

if (!isset($_SESSION['id']) || $_SESSION['rol'] != 'admin') {
    echo json_encode(['status' => 'error', 'mensaje' => 'No autorizado']);
    exit;
}

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

// Obtener todos los usuarios
if ($accion == 'listar') {
    $result = $conn->query("SELECT id, nombre, usuario, correo, foto, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    echo json_encode(['status' => 'ok', 'usuarios' => $usuarios]);
}

// Eliminar usuario
if ($accion == 'eliminar') {
    $id = intval($_POST['id']);
    if ($id == $_SESSION['id']) {
        echo json_encode(['status' => 'error', 'mensaje' => 'No puedes eliminarte a ti mismo']);
        exit;
    }
    $conn->query("DELETE FROM usuarios WHERE id=$id");
    echo json_encode(['status' => 'ok']);
}

// Editar usuario
if ($accion == 'editar') {
    $id = intval($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $rol = $conn->real_escape_string($_POST['rol']);
    $conn->query("UPDATE usuarios SET nombre='$nombre', correo='$correo', rol='$rol' WHERE id=$id");
    echo json_encode(['status' => 'ok']);
}

// Obtener un usuario
if ($accion == 'obtener') {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT id, nombre, usuario, correo, rol FROM usuarios WHERE id=$id");
    $user = $result->fetch_assoc();
    echo json_encode(['status' => 'ok', 'usuario' => $user]);
}
?>