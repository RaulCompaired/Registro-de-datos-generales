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


$buscar_alumno = mysqli_query($conexion, "SELECT * FROM alumnos_nuevo_ingreso WHERE boleta = '$usuario'");
if ($user = mysqli_fetch_assoc($buscar_alumno)) {
    if (password_verify($pass, $user['contrasena'])) {
        $_SESSION['boleta'] = $user['boleta'];
        $_SESSION['redirect'] = '../HTML/cuenta.php';
        echo json_encode(['status' => 'success', 'redirect' => '../HTML/cuenta.php']);
        exit;
    }
}



$buscar_admin = mysqli_query($conexion, "SELECT * FROM admin WHERE usuario = '$usuario'");
if ($user = mysqli_fetch_assoc($buscar_admin)) {
    if (password_verify($pass, $user['contrasena'])) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['redirect'] = '../HTML/admin.php';
        echo json_encode(['status' => 'success', 'redirect' => '../HTML/admin.php']);
        exit;
    }
}

// 3. Si no entró a ningún IF, los datos están mal
echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
?>