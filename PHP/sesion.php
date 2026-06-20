<?php
session_start();
require 'conexion.php';

$correo = $_POST['correo'];
$pass = $_POST['password'];


$buscar_alumno = mysqli_query($conexion, "SELECT * FROM alumnos_nuevo_ingreso WHERE correo = '$correo'");
if ($user = mysqli_fetch_assoc($buscar_alumno)) {
    if (password_verify($pass, $user['contrasena'])) {
        $_SESSION['usuario'] = $user['nombre'];
        $_SESSION['correo'] = $user['correo'];
        echo json_encode(['status' => 'success', 'redirect' => '../HTML/cuenta.php']);
        exit;
    }
}



$buscar_admin = mysqli_query($conexion, "SELECT * FROM administradores WHERE correo = '$correo'");
if ($user = mysqli_fetch_assoc($buscar_admin)) {
    if ($pass === $user['contrasena']) {
        $_SESSION['usuario'] = $user['nombre'];
        $_SESSION['correo'] = $user['correo'];
        echo json_encode(['status' => 'success', 'redirect' => '../HTML/admin.php']);
        exit;
    }
}

// 3. Si no entró a ningún IF, los datos están mal
echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
?>