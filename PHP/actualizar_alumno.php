<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $escuela = mysqli_real_escape_string($conexion, $_POST['escuela']);
    $promedio = $_POST['promedio'];

    // Validar promedio en el Backend
    if (!preg_match('/^([6-9](\.\d{1,2})?|10(\.0{1,2})?)$/', $promedio) || floatval($promedio) < 6.00 || floatval($promedio) > 10.00) {
        echo "El promedio debe ser un valor entre 6.00 y 10.00.";
        exit;
    }

    $sql = "UPDATE alumnos 
            SET escuela_procedencia = '$escuela', promedio = '$promedio' 
            WHERE boleta = '$boleta'";

    if (mysqli_query($conexion, $sql)) {
        echo "success";
    } else {
        if (mysqli_errno($conexion) == 1644) {//mysql_erno sirve para capturar el error de la base de datos que lanza el trigger, en este caso es el error 1644
            echo mysqli_error($conexion);
        } else {
            echo "Error al guardar en MySQL: " . mysqli_error($conexion);
        }
    }
}
?>