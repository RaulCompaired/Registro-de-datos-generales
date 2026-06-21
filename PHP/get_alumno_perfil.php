<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['boleta'])) {
    echo "No autorizado";
    exit;
}

$boleta = $_SESSION['boleta'];
$consulta = mysqli_query($conexion, "SELECT * FROM alumnos WHERE boleta = '$boleta'");
$alumno = mysqli_fetch_assoc($consulta);

if ($alumno) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($alumno);
} else {
    echo "Alumno no encontrado";
}
exit;
?>
