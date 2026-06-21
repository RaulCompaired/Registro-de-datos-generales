<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $nombre = $_POST['nombre'];
    $fecha_nacimiento = $_POST['nacimiento'];
    $genero = $_POST['genero'] ?? '';
    $curp = $_POST['curp'];
    $entidad = $_POST['procedenciafed'];
    $escuela = $_POST['escuelap'];
    $nombre_escuela = $_POST['nombreesc'];
    $promedio = $_POST['promedio'];
    $correo = $_POST['correo'];

    $contrasena_plana = $_POST['password'];
    $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

    // Verificar si la boleta ya existe para evitar errores SQL
    $check_query = mysqli_query($conexion, "SELECT boleta FROM alumnos_nuevo_ingreso WHERE boleta = '$boleta'");
    if (mysqli_num_rows($check_query) > 0) {
        echo "El número de boleta ya está registrado.";
        exit;
    }

    $sql = "INSERT INTO alumnos_nuevo_ingreso 
                (boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela_procedencia, nombre_escuela, promedio, correo, contrasena) 
                VALUES 
                ('$boleta', '$nombre', '$fecha_nacimiento', '$genero', '$curp', '$entidad', '$escuela', '$nombre_escuela', '$promedio', '$correo', '$contrasena_hash')";

    if (mysqli_query($conexion, $sql)) {
        echo "success";
    } else {
        echo "Error al insertar registro en la base de datos: " . mysqli_error($conexion);
    }
} else {
    echo "Método no permitido";
}
exit;
?>