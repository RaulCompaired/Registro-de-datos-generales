<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boleta = $_POST['boleta'];
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $fecha_nacimiento = $_POST['nacimiento'];
    $genero = $_POST['genero'];
    $curp = $_POST['curp'];
    $entidad = $_POST['procedenciafed'];
    $escuela = mysqli_real_escape_string($conexion, $_POST['escuelap']);
    $nombre_escuela = mysqli_real_escape_string($conexion, $_POST['nombreesc'] ?? '');
    $promedio = $_POST['promedio'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    $contrasena_plana = $_POST['password'];
    $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

    // Iniciar transacción
    mysqli_begin_transaction($conexion);

    // 1. Verificar si la boleta, curp o correo ya existen
    $check_query = mysqli_query($conexion, "SELECT boleta, curp, correo FROM alumnos WHERE boleta = '$boleta' OR curp = '$curp' OR correo = '$correo' LIMIT 1 FOR UPDATE");
    if ($existing = mysqli_fetch_assoc($check_query)) {
        if ($existing['boleta'] === $boleta) {
            echo "El número de boleta ya está registrado.";
        } 
        else {
            echo "El correo electrónico ya está registrado.";
        }
        mysqli_rollback($conexion);
        exit;
    }

    // 2. Obtener la lista de todos los grupos y su límite
    $groups_query = mysqli_query($conexion, "SELECT id, limite_alumnos FROM grupos ORDER BY id FOR UPDATE");
    $grupos = [];
    $group_ids = [];
    while ($row = mysqli_fetch_assoc($groups_query)) {
        $gid = (int)$row['id'];
        $group_ids[] = $gid;
        $grupos[$gid] = [
            'limite_alumnos' => (int)$row['limite_alumnos'],
            'ocupados' => 0
        ];
    }

    $num_grupos = count($group_ids);
    if ($num_grupos == 0) {
        mysqli_rollback($conexion);
        echo "Error: No hay grupos configurados en la base de datos.";
        exit;
    }

    // 3. Contar alumnos por grupo
    $occupancy_query = mysqli_query($conexion, "SELECT grupo_id, COUNT(*) as ocupados FROM alumnos WHERE grupo_id IS NOT NULL GROUP BY grupo_id");
    while ($row = mysqli_fetch_assoc($occupancy_query)) {
        $gid = (int)$row['grupo_id'];
        if (isset($grupos[$gid])) {
            $grupos[$gid]['ocupados'] = (int)$row['ocupados'];
        }
    }

    // 4. Obtener el último grupo_id asignado a un alumno
    $last_group_query = mysqli_query($conexion, "SELECT grupo_id FROM alumnos WHERE grupo_id IS NOT NULL ORDER BY id DESC LIMIT 1 FOR UPDATE");
    $last_grupo_id = null;
    if ($row = mysqli_fetch_assoc($last_group_query)) {
        $last_grupo_id = (int)$row['grupo_id'];
    }

    // 5. Encontrar el índice del último grupo_id asignado
    $last_index = -1;
    if ($last_grupo_id !== null) {
        $last_index = array_search($last_grupo_id, $group_ids);
        if ($last_index === false) {
            $last_index = -1;
        }
    }

    // 6. Asignar el siguiente grupo con cupo disponible usando Round Robin
    $grupo_asignado_id = null;
    for ($i = 0; $i < $num_grupos; $i++) {
        $next_index = ($last_index + 1 + $i) % $num_grupos;
        $candidate_id = $group_ids[$next_index];
        if ($grupos[$candidate_id]['ocupados'] < $grupos[$candidate_id]['limite_alumnos']) {
            $grupo_asignado_id = $candidate_id;
            break;
        }
    }

    if ($grupo_asignado_id === null) {
        mysqli_rollback($conexion);
        echo "No hay cupo disponible en ningún grupo.";
        exit;
    }

    // 7. Insertar el alumno con el grupo asignado
    $sql = "INSERT INTO alumnos 
                (boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela_procedencia, nombre_escuela, promedio, correo, contrasena, telefono, grupo_id) 
                VALUES 
                ('$boleta', '$nombre', '$fecha_nacimiento', '$genero', '$curp', '$entidad', '$escuela', '$nombre_escuela', '$promedio', '$correo', '$contrasena_hash', '$telefono', $grupo_asignado_id)";

    if (mysqli_query($conexion, $sql)) {
        mysqli_commit($conexion);
        echo "success";
    } else {
        mysqli_rollback($conexion);
        echo "Error al insertar registro en la base de datos: " . mysqli_error($conexion);
    }
} else {
    echo "Método no permitido";
}
exit;
?>