<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['boleta'])) {
    echo "No autorizado";
    exit;
}

$boleta = $_SESSION['boleta'];
$consulta = mysqli_query($conexion, "SELECT a.*, g.nombre AS grupo_nombre, g.hora_inicio, g.hora_fin, l.nombre AS laboratorio_nombre
    FROM alumnos a
    LEFT JOIN grupos g ON a.grupo_id = g.id
    LEFT JOIN laboratorios l ON g.laboratorio_id = l.id
    WHERE a.boleta = '$boleta'");
$alumno = mysqli_fetch_assoc($consulta);

if ($alumno) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($alumno);
} else {
    echo "Alumno no encontrado";
}
exit;
?>
