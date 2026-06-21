<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $escuela = $_POST['escuela'];
    $promedio = $_POST['promedio'];

    $sql = "UPDATE alumnos_nuevo_ingreso 
            SET escuela_procedencia = '$escuela', promedio = '$promedio' 
            WHERE boleta = '$boleta'";

    if (mysqli_query($conexion, $sql)) {
        echo "success";
    } else {
        echo "Error al guardar en MySQL: " . mysqli_error($conexion);
    }
}
?>