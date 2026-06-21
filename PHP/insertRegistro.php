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

    // Validaciones de formato en el Backend (PHP)
    $errores = [];

    // 1. Boleta
    if (!preg_match('/^\d{10}$|^(PE|PP)\d{8}$/', $boleta)) {
        $errores[] = "El numero de boleta no es valido (debe tener 10 digitos o comenzar con PE/PP seguido de 8 digitos).";
    }

    // 2. CURP
    if (!preg_match('/^[A-Z]{4}\d{6}(H|M)[A-Z]{5}(\d{2}|[A-Z]\d)$/', $curp)) {
        $errores[] = "El CURP no es valido.";
    }

    // 3. Nombre (usar el valor raw recibido por POST para la validacion)
    $nombre_raw = $_POST['nombre'] ?? '';
    if (!preg_match('/^[A-Z][a-z]+ [A-Z][a-z]+( |[A-Z a-z])*$/', $nombre_raw)) {
        $errores[] = "El nombre no es valido (debe iniciar con mayusculas y contener al menos nombre y apellido).";
    }

    // 4. Telefono (si se ingresa, debe tener 10 digitos)
    if (!empty($telefono) && !preg_match('/^\d{10}$/', $telefono)) {
        $errores[] = "El telefono debe tener exactamente 10 digitos.";
    }

    // 5. Promedio
    if (!preg_match('/^([6-9](\.\d{1,2})?|10(\.0{1,2})?)$/', $promedio) || floatval($promedio) < 6.00 || floatval($promedio) > 10.00) {
        $errores[] = "El promedio debe ser un valor entre 6.00 y 10.00.";
    }

    // 6. Correo
    if (!preg_match('/^[A-Za-z0-9_.]+@alumno.ipn.mx$/', $correo)) {
        $errores[] = "El correo electronico debe pertenecer al dominio @alumno.ipn.mx.";
    }

    // 7. Contraseña
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\s]).{6,}$/', $contrasena_plana)) {
        $errores[] = "La contrasena debe tener al menos 6 caracteres, una mayuscula, un numero y un caracter especial.";
    }

    // 8. Fecha de nacimiento (Edad entre 16 y 100 años)
    if (empty($fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento es requerida.";
    } else {
        $date_nacimiento = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
        if (!$date_nacimiento) {
            $errores[] = "La fecha de nacimiento no tiene un formato valido.";
        } else {
            $hoy = new DateTime();
            $edad = $hoy->diff($date_nacimiento)->y;
            if ($edad < 16 || $edad > 100) {
                $errores[] = "La edad del alumno debe estar entre 16 y 100 anos.";
            }
        }
    }

    // Si hay errores de formato, terminar la ejecucion y mostrarlos
    if (!empty($errores)) {
        echo implode("\n", $errores);
        exit;
    }

    // Iniciar transacción
    mysqli_begin_transaction($conexion);

    // 1. Verificar si la boleta o correo ya existen
    $check_query = mysqli_query($conexion, "SELECT boleta, correo FROM alumnos WHERE boleta = '$boleta' OR correo = '$correo' LIMIT 1 FOR UPDATE");
    if ($existing = mysqli_fetch_assoc($check_query)) {
        if (strcasecmp($existing['boleta'], $boleta) === 0) {
            echo "El número de boleta ya está registrado.";
        } else {
            echo "El correo electrónico ya está registrado.";
        }
        mysqli_rollback($conexion);
        exit;
    }

    // 2. Obtener la lista de todos los grupos y su cupo actual
    $groups_query = mysqli_query($conexion, "SELECT id, limite_alumnos, inscritos FROM grupos ORDER BY id FOR UPDATE");
    $grupos = [];
    $group_ids = [];
    while ($row = mysqli_fetch_assoc($groups_query)) {
        $gid = (int)$row['id'];
        $group_ids[] = $gid;
        $grupos[$gid] = [
            'limite_alumnos' => (int)$row['limite_alumnos'],
            'ocupados' => (int)$row['inscritos']
        ];
    }

    $num_grupos = count($group_ids);
    if ($num_grupos == 0) {
        mysqli_rollback($conexion);
        echo "Error: No hay grupos configurados en la base de datos.";
        exit;
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
        if (mysqli_errno($conexion) == 1644) {//mysql_erno sirve para capturar el error de la base de datos que lanza el trigger, en este caso es el error 1644
            echo mysqli_error($conexion);
        } else {
            echo "Error al insertar registro en la base de datos: " . mysqli_error($conexion);
        }
    }
} else {
    echo "Método no permitido";
}
exit;
?>