<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $escuela = mysqli_real_escape_string($conexion, $_POST['escuela']);
    $promedio = $_POST['promedio'];

    $sql = "UPDATE alumnos 
            SET escuela_procedencia = '$escuela', promedio = '$promedio' 
            WHERE boleta = '$boleta'";

    if (mysqli_query($conexion, $sql)) {
        echo "success";
    } else {
        echo "Error al guardar en MySQL: " . mysqli_error($conexion);
    }
}
?>