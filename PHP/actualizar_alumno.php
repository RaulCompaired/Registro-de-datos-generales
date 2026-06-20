<?php
require 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $escuela = $_POST['escuela'];
    $promedio = $_POST['promedio'];

    // Actualizamos usando la boleta como identificador (para no afectar a otros alumnos)
    $sql = "UPDATE alumnos_nuevo_ingreso 
            SET escuela_procedencia = '$escuela', promedio = '$promedio' 
            WHERE boleta = '$boleta'";

    if (mysqli_query($conexion, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar en MySQL: ' . mysqli_error($conexion)]);
    }
}
?>