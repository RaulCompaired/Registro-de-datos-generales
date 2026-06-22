<?php
/**
 * Herramienta de Siembra y Control de Datos de Prueba
 * Registro de Datos Generales
 */

require_once 'PHP/conexion.php';

// Establecer cabeceras para evitar problemas con UTF-8
header('Content-Type: text/html; charset=utf-8');

$mensaje = '';
$tipo_mensaje = ''; // success, error, info

// Procesar acciones
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'vaciar') {
        // Iniciar transacción
        mysqli_begin_transaction($conexion);
        try {
            // Eliminar todos los alumnos
            $sql_del = "DELETE FROM alumnos";
            if (!mysqli_query($conexion, $sql_del)) {
                throw new Exception("Error al vaciar alumnos: " . mysqli_error($conexion));
            }

            // Forzar que el contador de inscritos en grupos vuelva a cero (por si acaso)
            $sql_reset_grupos = "UPDATE grupos SET inscritos = 0";
            if (!mysqli_query($conexion, $sql_reset_grupos)) {
                throw new Exception("Error al resetear grupos: " . mysqli_error($conexion));
            }

            mysqli_commit($conexion);
            $mensaje = "¡Base de datos limpiada con éxito! Se eliminaron todos los alumnos.";
            $tipo_mensaje = "success";
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            $mensaje = "Error: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    } 
    elseif ($accion === 'generar') {
        $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 10;
        if ($cantidad < 1 || $cantidad > 500) {
            $cantidad = 10;
        }

        // Datos para generación aleatoria
        $nombres_m = ['Juan', 'Pedro', 'Luis', 'Jose', 'Carlos', 'Alejandro', 'Fernando', 'Jorge', 'David', 'Ricardo', 'Roberto', 'Francisco', 'Miguel', 'Angel', 'Manuel', 'Santiago', 'Sebastian', 'Daniel', 'Javier', 'Eduardo'];
        $nombres_f = ['Maria', 'Ana', 'Sofia', 'Laura', 'Elena', 'Gabriela', 'Valeria', 'Diana', 'Camila', 'Patricia', 'Andrea', 'Leticia', 'Monica', 'Carmen', 'Beatriz', 'Adriana', 'Elizabeth', 'Natalia', 'Isabella', 'Daniela'];
        
        $apellidos = ['Perez', 'Garcia', 'Lopez', 'Martinez', 'Rodriguez', 'Sanchez', 'Ramirez', 'Cruz', 'Gomez', 'Flores', 'Morales', 'Vazquez', 'Jimenez', 'Reyes', 'Diaz', 'Torres', 'Hernandez', 'Ruiz', 'Mendoza', 'Aguilar', 'Castillo', 'Ortiz', 'Romero', 'Moreno', 'Alvarez', 'Rivera', 'Chavez', 'Juarez', 'Ramos', 'Herrera'];
        
        $entidades = ['Ciudad de Mexico', 'Estado de Mexico', 'Jalisco', 'Nuevo Leon', 'Puebla', 'Veracruz', 'Guanajuato', 'Queretaro', 'Hidalgo', 'Morelos'];
        $escuelas_tipo = ['CECyT', 'CET', 'Preparatoria Oficial', 'Colegio de Bachilleres', 'CBTis'];
        $escuelas_nombres = [
            'CECyT 9 Juan de Dios Batiz', 
            'CECyT 3 Estanislao Ramirez Ruiz', 
            'CECyT 1 Gonzalo Vazquez Vela', 
            'Preparatoria Oficial No. 10', 
            'Colegio de Bachilleres Plantel 2', 
            'CBTis 223', 
            'CECyT 13 Ricardo Flores Magon',
            'CECyT 11 Wilfrido Massieu'
        ];

        // Hash precalculado para "Alumno123!" (cumple con las políticas de contraseña)
        $contrasena_hash = password_hash('Alumno123!', PASSWORD_DEFAULT);

        // Obtener grupos y sus límites y ocupación actual para la asignación Round Robin
        $query_grupos = mysqli_query($conexion, "SELECT id, limite_alumnos, inscritos FROM grupos ORDER BY id");
        $grupos = [];
        while ($r = mysqli_fetch_assoc($query_grupos)) {
            $grupos[] = [
                'id' => intval($r['id']),
                'limite' => intval($r['limite_alumnos']),
                'inscritos' => intval($r['inscritos'])
            ];
        }

        // Obtener el índice del último grupo asignado para continuar el Round Robin
        $last_query = mysqli_query($conexion, "SELECT grupo_id FROM alumnos WHERE grupo_id IS NOT NULL ORDER BY id DESC LIMIT 1");
        $last_grupo_id = null;
        if ($row = mysqli_fetch_assoc($last_query)) {
            $last_grupo_id = intval($row['grupo_id']);
        }
        
        $current_index = -1;
        if ($last_grupo_id !== null) {
            foreach ($grupos as $idx => $g) {
                if ($g['id'] === $last_grupo_id) {
                    $current_index = $idx;
                    break;
                }
            }
        }

        $exitosos = 0;
        $errores_gen = [];

        // Generar alumnos en lotes
        for ($i = 0; $i < $cantidad; $i++) {
            // 1. Género y Nombre
            $es_hombre = (rand(0, 1) === 0);
            $genero = $es_hombre ? 'Hombre' : 'Mujer';
            $primer_nombre = $es_hombre ? $nombres_m[array_rand($nombres_m)] : $nombres_f[array_rand($nombres_f)];
            $apellido_paterno = $apellidos[array_rand($apellidos)];
            $apellido_materno = $apellidos[array_rand($apellidos)];
            $nombre_completo = "$primer_nombre $apellido_paterno $apellido_materno";

            // 2. Fecha de Nacimiento (Edad 16 a 25 años)
            $anio = rand(2000, 2009);
            $mes = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $dia = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT); // Evitar 29-31 para simplificar fechas válidas
            $fecha_nacimiento = "$anio-$mes-$dia";

            // 3. Boleta (10 dígitos: e.g. 2024 + 6 dígitos)
            $boleta = "2024" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // 4. CURP
            // 4 letras (del nombre o aleatorias de la A a la Z)
            $letras = '';
            for ($k = 0; $k < 4; $k++) {
                $letras .= chr(rand(65, 90)); // A-Z
            }
            $digitos_fecha = substr($anio, 2, 2) . $mes . $dia;
            $letra_genero = $es_hombre ? 'H' : 'M';
            // Estado y diferenciadores
            $letras_estado = '';
            for ($k = 0; $k < 5; $k++) {
                $letras_estado .= chr(rand(65, 90)); // A-Z
            }
            $homoclave = strval(rand(0, 9)) . strval(rand(0, 9));
            $curp = $letras . $digitos_fecha . $letra_genero . $letras_estado . $homoclave;

            // 5. Correo
            $correo = strtolower($primer_nombre) . "." . strtolower($apellido_paterno) . "." . rand(100, 999) . "@alumno.ipn.mx";
            // Limpiar acentos o caracteres extraños para el correo si existieran (nombres base no los tienen pero por seguridad)
            $correo = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $correo);

            // 6. Teléfono (10 dígitos)
            $telefono = "55" . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            // 7. Escuela y Promedio
            $entidad = $entidades[array_rand($entidades)];
            $escuela = $escuelas_tipo[array_rand($escuelas_tipo)];
            $nombre_escuela = $escuelas_nombres[array_rand($escuelas_nombres)];
            $promedio = number_format(rand(600, 1000) / 100, 2, '.', '');

            // 8. Encontrar grupo con cupo disponible por Round Robin
            $grupo_asignado_id = null;
            $num_grupos = count($grupos);
            for ($j = 0; $j < $num_grupos; $j++) {
                $next_index = ($current_index + 1 + $j) % $num_grupos;
                if ($grupos[$next_index]['inscritos'] < $grupos[$next_index]['limite']) {
                    $grupo_asignado_id = $grupos[$next_index]['id'];
                    $current_index = $next_index;
                    
                    // Actualizar en memoria el contador para la siguiente iteración del loop
                    $grupos[$next_index]['inscritos']++;
                    break;
                }
            }

            if ($grupo_asignado_id === null) {
                $errores_gen[] = "No hay cupo disponible en ningún grupo para el alumno: $nombre_completo";
                continue;
            }

            // 9. Insertar
            $sql_ins = "INSERT INTO alumnos 
                (boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela_procedencia, nombre_escuela, promedio, correo, contrasena, telefono, grupo_id) 
                VALUES 
                ('$boleta', '$nombre_completo', '$fecha_nacimiento', '$genero', '$curp', '$entidad', '$escuela', '$nombre_escuela', $promedio, '$correo', '$contrasena_hash', '$telefono', $grupo_asignado_id)";

            if (mysqli_query($conexion, $sql_ins)) {
                $exitosos++;
            } else {
                $errores_gen[] = "Error al insertar a $nombre_completo: " . mysqli_error($conexion);
            }
        }

        if ($exitosos > 0) {
            $mensaje = "¡Se generaron con éxito $exitosos registros de alumnos!";
            $tipo_mensaje = "success";
        }
        if (!empty($errores_gen)) {
            $mensaje .= "<br>Hubo algunos problemas:<br><ul><li>" . implode("</li><li>", array_slice($errores_gen, 0, 5)) . "</li></ul>";
            if (count($errores_gen) > 5) {
                $mensaje .= "y " . (count($errores_gen) - 5) . " errores más...";
            }
            $tipo_mensaje = $exitosos > 0 ? "warning" : "danger";
        }
    }
}

// Obtener estadísticas de la base de datos
$res_total = mysqli_query($conexion, "SELECT COUNT(*) as total FROM alumnos");
$total_alumnos = mysqli_fetch_assoc($res_total)['total'];

$res_grupos = mysqli_query($conexion, "
    SELECT g.id, g.nombre, g.limite_alumnos, g.hora_inicio, g.hora_fin, l.nombre as lab, g.inscritos
    FROM grupos g
    JOIN laboratorios l ON g.laboratorio_id = l.id
    ORDER BY g.id
");
$grupos_stats = [];
while ($row = mysqli_fetch_assoc($res_grupos)) {
    $grupos_stats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Datos de Prueba (Seed Tool)</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(22, 28, 45, 0.6);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-color);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a78bfa, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid transparent;
            line-height: 1.5;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }

        /* Panel Principal */
        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: 1fr 2fr;
            }
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-badge {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Formularios y Botones */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .select-input, .text-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .select-input:focus, .text-input:focus {
            border-color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
            transform: translateY(-1px);
        }

        .divider {
            height: 1px;
            background: var(--border-color);
            margin: 2rem 0;
        }

        /* Lista de grupos */
        .grupos-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 992px) {
            .grupos-list {
                grid-template-columns: 1fr 1fr;
            }
        }

        .grupo-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .grupo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .grupo-name {
            font-weight: 600;
            color: white;
        }

        .grupo-lab {
            font-size: 0.8rem;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
        }

        .grupo-time {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #10b981);
            border-radius: 4px;
            transition: width 0.3s;
        }

        .progress-bar.full {
            background: var(--danger);
        }

        .grupo-count {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .btn-link {
            display: block;
            text-align: center;
            color: var(--text-muted);
            text-decoration: none;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .btn-link:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Control de Datos de Prueba</h1>
        <p>Herramienta para sembrar alumnos aleatorios y vaciar los registros del examen</p>
    </header>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <!-- Columna de Acciones -->
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-title">
                    <span>Estado Actual</span>
                </div>
                <div style="font-size: 2.5rem; font-weight: 700; text-align: center; margin: 1rem 0; color: #a5b4fc;">
                    <?php echo $total_alumnos; ?>
                </div>
                <div style="text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    Alumnos registrados
                </div>
                
                <a href="HTML/admin.html" class="btn btn-primary" style="text-decoration: none; margin-bottom: 0.5rem;">
                    Ir al Panel de Admin ↗
                </a>
                <a href="index.html" class="btn-link">Volver al Registro</a>
            </div>

            <div class="card">
                <div class="card-title">Acciones</div>
                
                <!-- Formulario Generar -->
                <form method="POST" action="">
                    <input type="hidden" name="accion" value="generar">
                    <div class="form-group">
                        <label for="cantidad">Cantidad de alumnos a generar:</label>
                        <select name="cantidad" id="cantidad" class="select-input">
                            <option value="1">1 Alumno</option>
                            <option value="5" selected>5 Alumnos</option>
                            <option value="15">15 Alumnos (1 por grupo)</option>
                            <option value="30">30 Alumnos (2 por grupo)</option>
                            <option value="75">75 Alumnos</option>
                            <option value="150">150 Alumnos</option>
                            <option value="450">450 Alumnos (Llenar al límite)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Generar Alumnos</button>
                </form>

                <div class="divider"></div>

                <!-- Formulario Vaciar -->
                <form method="POST" action="" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a TODOS los alumnos registrados? Esto no se puede deshacer.');">
                    <input type="hidden" name="accion" value="vaciar">
                    <div style="margin-bottom: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                        Elimina todas las filas de la tabla <code>alumnos</code> y reestablece el conteo de inscritos de todos los grupos a cero.
                    </div>
                    <button type="submit" class="btn btn-danger">Vaciar Base de Datos</button>
                </form>
            </div>
        </div>

        <!-- Columna de Distribución de Grupos -->
        <div>
            <div class="card">
                <div class="card-title">
                    <span>Ocupación de Grupos</span>
                    <span class="stat-badge"><?php echo count($grupos_stats); ?> Grupos</span>
                </div>
                
                <div class="grupos-list">
                    <?php foreach ($grupos_stats as $g): 
                        $porcentaje = ($g['inscritos'] / $g['limite_alumnos']) * 100;
                        $is_full = ($g['inscritos'] >= $g['limite_alumnos']);
                    ?>
                        <div class="grupo-item">
                            <div>
                                <div class="grupo-header">
                                    <span class="grupo-name"><?php echo htmlspecialchars($g['nombre']); ?></span>
                                    <span class="grupo-lab"><?php echo htmlspecialchars($g['lab']); ?></span>
                                </div>
                                <div class="grupo-time">
                                    🕒 <?php echo substr($g['hora_inicio'], 0, 5) . ' - ' . substr($g['hora_fin'], 0, 5); ?>
                                </div>
                            </div>
                            <div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar <?php echo $is_full ? 'full' : ''; ?>" style="width: <?php echo min(100, $porcentaje); ?>%"></div>
                                </div>
                                <div class="grupo-count">
                                    <span><?php echo $g['inscritos']; ?> / <?php echo $g['limite_alumnos']; ?></span>
                                    <span><?php echo number_format($porcentaje, 0); ?>%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
