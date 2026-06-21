<?php
session_start();
require 'conexion.php';
require 'config.php';

$usuario = $_POST['usuario'];
$pass = $_POST['contra'];
$recaptcha_secret = RECAPTCHA_SECRET;
$response = $_POST['g-recaptcha-response'];

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$response}");
$responseData = json_decode($verify);

if (!$responseData->success) {
    // El captcha no es válido, no permitimos iniciar sesión
    echo "Por favor, completa el captcha correctamente.";
    exit;
}


$buscar_alumno = mysqli_query($conexion, "SELECT * FROM alumnos WHERE boleta = '$usuario'");
if ($user = mysqli_fetch_assoc($buscar_alumno)) {
    if (password_verify($pass, $user['contrasena'])) {
        $_SESSION['boleta'] = $user['boleta'];
        $_SESSION['usuario'] = $user['nombre'];
        $_SESSION['correo'] = $user['correo'];
        $_SESSION['role'] = 'alumno';
        $_SESSION['redirect'] = '../HTML/cuenta.html';
        echo "alumno";
        exit;
    }
}



$buscar_admin = mysqli_query($conexion, "SELECT * FROM `admin` WHERE usuario = '$usuario'");
if ($user = mysqli_fetch_assoc($buscar_admin)) {
    if (password_verify($pass, $user['contrasena'])) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['role'] = 'admin';
        $_SESSION['redirect'] = '../HTML/admin.html';
        echo "admin";
        exit;
    }
}

// 3. Si no entró a ningún IF, los datos están mal
echo "Usuario o contraseña incorrectos";
?>