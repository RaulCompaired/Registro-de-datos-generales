<!DOCTYPE html>
<html lang="es">

<?php
session_start();
require '../PHP/conexion.php';

// Validar que exista una sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../HTML/logincuenta.html");
    exit;
}

// Consultamos a todos los alumnos registrados
$consulta = mysqli_query($conexion, "SELECT boleta, nombre, fecha_nacimiento,genero,curp, entidad_federativa,escuela_procedencia, promedio FROM alumnos_nuevo_ingreso");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="descripcion" content="Página de cuenta">
    <title>CUENTA</title>

    <link rel="icon" href="../imagenes/LogoEquipo.jpg" type="image/png">

    <!--Bootstrap-->
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">

    <!--Mi CSS-->
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/cuenta.css">
        <link rel="stylesheet" href="../CSS/letras.css">


    <!--Se puede agregar scripts también aqui-->
</head>

<!--Cuerpo del programa (contenido de la página)-->

<body>

    <!--Encabezado de la página-->
    <header class="header-principal">
        <div id="header">
            <nav class="navbar navbar-light bg-light">
                <div class="d-flex justify-content-between align-items-center w-100 px-4">

                    <a class="navbar-brand m-0" href="https://www.ipn.mx" target="_blank" rel="noopener">
                        <img src="../imagenes/ipn_logo.webp" width="100" height="100" alt="Logo ESCOM">
                    </a>

                    <div class="m-0 text-center text-wrap">
                        <h1><strong>¡BIENVENIDOS ALUMNOS DE NUEVO INGRESO!</strong></h1>
                    </div>

                    <a class="navbar-brand m-0" href="https://www.escom.ipn.mx" target="_blank" rel="noopener">
                        <img src="../imagenes/logo_escom.png" width="100" height="100" alt="Logo Derecho">
                    </a>

                </div>
            </nav>
        </div>
        <!-- Barra de navegación -->
        <nav class="navbar navbar-expand-lg navbar-dark shadow-lg p-3 mb-0">

            <!--Botón de Inicio colocado a la izquierda-->
            <a class="navbar-brand mx-3" href="inicio.html">Inicio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample02"
                aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample02">
                <!--Elegimos la clase navbar-nav y mx-auto= margen izquierdo y derecho (lo que deja los elementos al centro)-->
                <ul class="navbar-nav ms-auto">
                    <!--Es la clase de items de la navbar y el mx-4= margen izquierdo y derecho en cada item (1 es mínimo-5 es máximo)-->

                    <!--Item de navegación que lleva a la página de Registro-->
                    <li class="nav-item active mx-4">
                        <a class="nav-link" href="registro.html">Registro <span class="sr-only"></span></a>
                    </li>

                    

                    <!--Item de navegación que lleva a la página de Cuenta-->
                    <li class="nav-item active mx-4">
                        <a class="nav-link" href="logincuenta.html">Cuenta</a>
                    </li>

                </ul>
            </div>
        </nav>

    </header>

    <!--Contenido Principal-->
    <div class="card shadow-lg border-0 bg-white p-4 my-5 mx-auto" style="border-radius: 20px; max-width: 95%;">
    
    <div class="card-header bg-white border-0 pt-3 pb-4">
        <h3 class="fw-bold mb-1" style="color: #005580; font-size: 2rem;">Alumnos</h3>
        <p class="text-muted mb-0" style="font-size: 1.1rem;">Modificación de datos</p>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 1.1rem; min-width: 800px;">
            
            <thead>
                <tr class="bg-primary">
                    <th class="text-black border-0 py-4 px-4" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;">Boleta</th>
                    <th class="text-black border-0 py-4 px-3">Nombre</th>
                    <th class="text-black border-0 py-4 px-3">Fecha de Nacimiento</th>
                    <th class="text-black border-0 py-4 px-3">Género</th>
                    <th class="text-black border-0 py-4 px-3">CURP</th>
                    <th class="text-black border-0 py-4 px-3">Entidad Federativa</th>
                    <th class="text-black border-0 py-4 px-3">Escuela de Procedencia</th>
                    <th class="text-black border-0 py-4 px-3">Promedio</th>
                    <th class="text-black border-0 py-4 px-4 text-center" style="border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Acciones</th>
                </tr>
            </thead>
            
            <tbody>
                <?php while ($fila = mysqli_fetch_assoc($consulta)) { ?>
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
                            onclick="abrirModal('<?php echo $fila['boleta']; ?>', '<?php echo $fila['nombre']; ?>', '<?php echo $fila['fecha_nacimiento']; ?>', '<?php echo $fila['genero']; ?>', '<?php echo $fila['curp']; ?>', '<?php echo $fila['entidad_federativa']; ?>', '<?php echo $fila['escuela_procedencia']; ?>', '<?php echo $fila['promedio']; ?>')">
                            Editar
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header fondo-guinda">
                    <h5 class="modal-title text-white">Modificar Datos del Alumno</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="editBoleta" name="boleta">

                        <div class="mb-3">
                            <label class="form-label">Nombre del Alumno (Solo lectura)</label>
                            <input type="text" class="form-control" id="editNombre" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="editFechaNacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Género</label>
                            <select class="form-control" id="editGenero" name="genero">
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CURP</label>
                            <input type="text" class="form-control" id="editCURP" name="curp" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Entidad Federativa</label>
                            <input type="text" class="form-control" id="editEntidad" name="entidad_federativa">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Escuela de Procedencia</label>
                            <input type="text" class="form-control" id="editEscuela" name="escuela" >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Promedio</label>
                            <input type="number" step="0.01" class="form-control" id="editPromedio" name="promedio">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarCambios()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

   
    

    <!--FOOTER-->
    <div class="bg-dark text-white pt-4 pb-2 mt-5">
        <div class="container">

            <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 my-5 border-top border-secondary">


                <div class="col mb-3">
                    <p>EQUIPO TOP GLOBALES</p>
                    <a href="https://www.youtube.com/watch?v=DLzxrzFCyOs&list=RDDLzxrzFCyOs&start_radio=1"
                        target="_blank" rel="noopener"
                        class="d-flex align-items-center mb-3 link-light text-decoration-none">
                        <img src="../imagenes/LogoEquipo.jpg" alt="LogoEquipo2" height="80" class="me-2">
                    </a>
                    <p class="text-white-50">© 2026</p>
                </div>

                <div class="col mb-3">
                    <h5>Enlaces</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="https://www.ipn.mx" target="_blank" rel="noopener"
                                class="nav-link p-0 text-white-50">IPN
                                OFICIAL</a></li>
                        <li class="nav-item mb-2"><a href="https://uteycv.escom.ipn.mx/s/ni/registro/" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">Registro de datos
                                generales</a></li>
                        <li class="nav-item mb-2"><a href="https://www.escom.ipn.mx/nuevoingreso25_2/#jornada"
                                target="_blank" rel="noopener" class="nav-link p-0 text-white-50">Programa</a></li>
                        <li class="nav-item mb-2"><a
                                href="https://www.escom.ipn.mx/nuevoingreso25_2/assets/docs/ProcedimientoInscripcionNuevoIngreso_25_2.pdf"
                                target="_blank" rel="noopener" class="nav-link p-0 text-white-50">Proceso de Inscripción</a>

                    </ul>
                </div>

                <div class="col mb-3">
                    <h5>Programas Académicos</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a
                                href="https://www.escom.ipn.mx/docs/oferta/mapaCurricularISC2020.pdf" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">I.S.C</a></li>
                        <li class="nav-item mb-2"><a
                                href="https://www.escom.ipn.mx/docs/oferta/mapaCurricularIIA2020.pdf" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">I.I.A</a></li>
                        <li class="nav-item mb-2"><a
                                href="https://www.escom.ipn.mx/docs/oferta/mapaCurricularLCD2020H.pdf" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">L.C.D</a></li>
                    </ul>
                </div>

                <div class="col mb-3">
                    <h5>Síguenos en...</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="https://www.instagram.com/escom_ipn_mx/" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">Instagram</a></li>
                        <li class="nav-item mb-2"><a href="https://www.facebook.com/escomipnmx/" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">Facebook - ESCOM</a></li>
                        <li class="nav-item mb-2"><a href="https://www.ipn.mx/gacetapolitecnica/" target="_blank"
                                rel="noopener" class="nav-link p-0 text-white-50">Gaceta Politécnica</a></li>
                    </ul>
                </div>
            </footer>
        </div>
    </div>
    <!--FIN DE FOOTER-->


    <script src="../bootstrap/bootstrap.bundle.min.js"></script>
    

</body>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function abrirModal(boleta, nombre, fecha_nacimiento, genero, curp, entidad_federativa, escuela, promedio) {
            document.getElementById('editBoleta').value = boleta;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editFechaNacimiento').value = fecha_nacimiento;
            document.getElementById('editGenero').value = genero;
            document.getElementById('editCURP').value = curp;
            document.getElementById('editEntidad').value = entidad_federativa;
            document.getElementById('editEscuela').value = escuela;
            document.getElementById('editPromedio').value = promedio;
            
            var myModal = new bootstrap.Modal(document.getElementById('modalEditar'));
            myModal.show();
        }

        function guardarCambios() {
            const formulario = document.getElementById('formEditar');
            const datos = new FormData(formulario);

            fetch('../PHP/actualizar_alumno.php', {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: 'Los datos se guardaron correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        location.reload(); // Recargamos la página para ver los cambios en la tabla
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</html>



