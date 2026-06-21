<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['boleta'])) {
    echo "No autorizado";
    exit;
}

$boleta = $_SESSION['boleta'];
$consulta = mysqli_query($conexion, "SELECT * FROM alumnos_nuevo_ingreso WHERE boleta = '$boleta'");
$alumno = mysqli_fetch_assoc($consulta);
?>
<h5 class="text-center mb-4">
    ¡Hola de nuevo, <strong id="alumno-nombre-display"><?php echo $alumno['nombre']; ?></strong>!
</h5>
<span class="text-muted d-block text-center mb-4"> En caso de error en el registro consulta en Gestión Escolar</span>

<ul class="list-group list-group-flush mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <span class="text-muted">Boleta</span>
        <span class="fw-bold"><?php echo $alumno['boleta']; ?></span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <span class="text-muted">CURP</span>
        <span class="fw-bold text-uppercase"><?php echo $alumno['curp']; ?></span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <span class="text-muted">Escuela de Procedencia</span>
        <span class="fw-bold text-end"><?php echo $alumno['escuela_procedencia']; ?></span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <span class="text-muted">Promedio</span>
        <span class="badge fondo-guinda rounded-pill fs-6"><?php echo $alumno['promedio']; ?></span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
        <span class="text-muted">Correo Institucional</span>
        <span class="fw-bold"><?php echo $alumno['correo']; ?></span>
    </li>
</ul>
<div class="d-grid mt-4">
    <a href="" class="btn btn-outline-primary btn-lg">Descargar PDF con la información de tu horario</a>
</div>
<div class="d-grid mt-4">
    <a href="../PHP/cerrarsesion.php" class="btn btn-outline-danger btn-lg">Cerrar Sesión</a>
</div>
