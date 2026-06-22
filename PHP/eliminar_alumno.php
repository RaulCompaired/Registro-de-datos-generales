<?php
session_start();
require 'conexion.php';

// Verificar la sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'admin') {
    echo "No autorizado";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'] ?? '';

    if (empty($boleta)) {
        echo "La boleta es obligatoria.";
        exit;
    }

    $boleta = mysqli_real_escape_string($conexion, $boleta);

    $sql = "DELETE FROM alumnos WHERE boleta = '$boleta'";

    if (mysqli_query($conexion, $sql)) {
        echo "success";
    } else {
        echo "Error al eliminar de la base de datos: " . mysqli_error($conexion);
    }
} else {
    echo "Método no permitido";
}
?>
