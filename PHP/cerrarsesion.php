<?php
session_start(); // Unirse a la sesión actual
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión en el servidor

// Lo mandamos al login vacío de nuevo
header("Location: ../HTML/logincuenta.html");
exit;
?>