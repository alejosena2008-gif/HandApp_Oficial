<?php
require_once "conexion.php";

if ($conn) {
    echo "✅ Conexion exitosa a la base de datos: HandApp";
} else {
    echo "❌ No se pudo conectar a la base de datos";
}

$conn->close();
?>
