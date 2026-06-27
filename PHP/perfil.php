<?php
include("conexion.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'No hay sesión activa']);
    exit;
}

// Obtener datos del usuario
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_SESSION['id'];
    $result = $conn->query("SELECT nombre, usuario, correo, foto, rol FROM usuarios WHERE id=$id");
    $user = $result->fetch_assoc();
    echo json_encode(['status' => 'ok', 'usuario' => $user]);
}

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['id'];
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    
    // Subir foto si viene
    $foto = $_SESSION['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = 'user_' . $id . '.' . $ext;
        $ruta = '../IMG/usuarios/' . $nombre_foto;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
            $foto = $nombre_foto;
            $_SESSION['foto'] = $foto;
        }
    }

    $sql = "UPDATE usuarios SET nombre='$nombre', correo='$correo', foto='$foto' WHERE id=$id";
    
    if ($conn->query($sql)) {
        $_SESSION['nombre'] = $nombre;
        echo json_encode(['status' => 'ok', 'foto' => $foto]);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error al actualizar']);
    }
}
?>