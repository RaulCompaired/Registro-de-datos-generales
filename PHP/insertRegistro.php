<?php

require_once 'conexion.php';


    $boleta = $_POST['boleta'];
    $nombre = $_POST['nombre'];
    $fecha_nacimiento = $_POST['nacimiento'];
    $genero = $_POST['genero'];
    $curp = $_POST['curp'];
    $entidad = $_POST['procedenciafed'];
    $escuela = $_POST['escuelap'];
    $nombre_escuela = $_POST['nombreesc'];
    $promedio = $_POST['promedio'];
    $correo = $_POST['correo'];

    //hasheo para que la profa diga wow
    $contrasena_plana = $_POST['password'];
    $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

    $sql = "INSERT INTO alumnos_nuevo_ingreso 
                (boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela_procedencia, nombre_escuela, promedio, correo, contrasena) 
                VALUES 
                ('$boleta', '$nombre', '$fecha_nacimiento', '$genero', '$curp', '$entidad', '$escuela', '$nombre_escuela', '$promedio', '$correo', '$contrasena_hash')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../HTML/logincuenta.html");
        exit;
    } else {
        echo "Error al insertar registro";
    }
?>