<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'admin') {
    echo "<tr><td colspan='9' class='text-center'>No autorizado</td></tr>";
    exit;
}

$consulta = mysqli_query($conexion, "SELECT boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela_procedencia, promedio FROM alumnos");
while ($fila = mysqli_fetch_assoc($consulta)) { ?>
<tr style="height: 75px; transition: background-color 0.2s ease;">
    <td class="px-4 fw-bold" style="color: #005580;"><?php echo $fila['boleta']; ?></td>
    <td class="px-3 fw-semibold text-dark"><?php echo $fila['nombre']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['fecha_nacimiento']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['genero']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['curp']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['entidad_federativa']; ?></td>
    <td class="px-3 text-secondary"><?php echo $fila['escuela_procedencia']; ?></td>
    <td class="px-3">
        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 fs-6">
            <?php echo $fila['promedio']; ?>
        </span>
    </td>
    <td class="px-4 text-center">
        <button class="btn text-white px-4 py-2 rounded-3 fw-bold shadow-sm" 
            style="background-color: #006699; border: none; transition: 0.2s;"
            onmouseover="this.style.backgroundColor='#005580'" 
            onmouseout="this.style.backgroundColor='#006699'"
            onclick="abrirModal(
                '<?php echo htmlspecialchars(addslashes($fila['boleta']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['nombre']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['fecha_nacimiento']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['genero']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['curp']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['entidad_federativa']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['escuela_procedencia']), ENT_QUOTES, 'UTF-8'); ?>', 
                '<?php echo htmlspecialchars(addslashes($fila['promedio']), ENT_QUOTES, 'UTF-8'); ?>'
            )">
            Editar
        </button>
    </td>
</tr>
<?php } ?>
