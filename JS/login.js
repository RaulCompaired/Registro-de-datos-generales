function validarlogin(destino) {
    var errores = [];
    var correov = document.forms.datos.correo.value;
    var rescorreo = correov.match(/^[A-Za-z0-9_.]+(@alumno.ipn.mx$ || @administrador.ipn.mx$)/);
    marcarCampo("correo", rescorreo);

    var passwordv = document.forms.datos.password.value;
    var respassword = passwordv.match(/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\s]).{6,}$/);
    marcarCampo("password", respassword);

    if (rescorreo == null) errores.push("Correo inválido");
    if (respassword == null) errores.push("Contraseña inválida");

    if (errores.length > 0) {
        Swal.fire({
            title: 'Error',
            text: 'Errores encontrados:\n' + errores.join('\n'),
            icon: 'error',
            confirmButtonColor: '#800020'
        });


    }

    else {

            var formulario = document.forms.datos;
            const datosFormulario = new FormData(formulario);

            fetch('../PHP/sesion.php', {
                method: 'POST',
                body: datosFormulario
            })
                .then(respuesta => respuesta.json())
                .then(data => {
                    if (data.status === 'success') {
                            Swal.fire({
                                title: '¡Bienvenidoooo!',
                                text: 'Iniciando sesión...',
                                icon: 'success',
                                timer: 1500, 
                                showConfirmButton: false // Esconde el botón para que sea más fluido
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#800020'
                        });
                    }
                })
                .catch(error => console.error("Error en la petición:", error));
        }


    }






function marcarCampo(id, resultado) {
    var campo = document.getElementById(id);
    if (resultado == null) {
        campo.classList.add("is-invalid");
        campo.classList.remove("is-valid");
    } else {
        campo.classList.remove("is-invalid");
        campo.classList.add("is-valid");
    }
}



