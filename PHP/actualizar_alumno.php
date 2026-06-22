<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $promedio = $_POST['promedio'];
    $grupo = $_POST['grupo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

    // Validar fecha de nacimiento (edad entre 16 y 100 años)
    if (empty($fecha_nacimiento)) {
        echo "La fecha de nacimiento es obligatoria.";
        exit;
    }
    $nacimiento_dt = date_create($fecha_nacimiento);
    if (!$nacimiento_dt) {
        echo "La fecha de nacimiento no es válida.";
        exit;
    }
    $hoy_dt = date_create('today');
    $edad = date_diff($nacimiento_dt, $hoy_dt)->y;
    if ($edad < 16 || $edad > 100) {
        echo "La edad del alumno debe estar entre 16 y 100 años.";
        exit;
    }

    $romanos = [
        'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5,
        'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10,
        'XI' => 11, 'XII' => 12, 'XIII' => 13, 'XIV' => 14, 'XV' => 15
    ];

    // 2. str_ireplace quita "grupo" sin importar mayúsculas/minúsculas. trim() quita espacios.
    $romano = trim(str_ireplace('grupo', '', $grupo));
    $romano = strtoupper($romano);

    // Validar promedio en el Backend
    if (!preg_match('/^([6-9](\.\d{1,2})?|10(\.0{1,2})?)$/', $promedio) || floatval($promedio) < 6.00 || floatval($promedio) > 10.00) {
        echo "El promedio debe ser un valor entre 6.00 y 10.00.";
        exit;
    }

    $sql = "UPDATE alumnos 
            SET fecha_nacimiento = '$fecha_nacimiento', promedio = '$promedio', grupo_id = " . ($romanos[$romano] ?? 'NULL') . "
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