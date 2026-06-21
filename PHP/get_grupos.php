<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'admin') {
    echo "<tr><td colspan='9' class='text-center'>No autorizado</td></tr>";
    exit;
}

$consulta = mysqli_query($conexion, "SELECT * FROM grupos");
while ($fila = mysqli_fetch_assoc($consulta)) { ?>
<tr style="height: 75px; transition: background-color 0.2s ease;">
    <td class="px-3 fw-semibold text-dark"><?php echo $fila['nombre']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['laboratorio_id']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['hora_inicio']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['hora_fin']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['limite_alumnos']; ?></td>
    <td class="px-3">
        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 fs-6">
            <?php echo $fila['inscritos']; ?>
        </span>
    </td>
    
</tr>
<?php } ?>
