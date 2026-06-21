function validarlogin(destino) {
    var errores = [];
    var correov = document.forms.datos.usuario.value.trim();
    var rescorreo = correov !== "" ? true : null;
    marcarCampo("usuario", rescorreo);

    var passwordv = document.forms.datos.contra.value.trim();
    var respassword = passwordv !== "" ? true : null;
    marcarCampo("contra", respassword);

    if (rescorreo === null) errores.push("El usuario es obligatorio");
    if (respassword === null) errores.push("La contraseña es obligatoria");

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
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                    document.getElementById("btn-ingresar").disabled = true;
                }
            })
            .catch(error => {
                console.error("Error en la petición:", error);
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
                document.getElementById("btn-ingresar").disabled = true;
            });
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

// Callback functions for reCAPTCHA
function enableBtn() {
    document.getElementById("btn-ingresar").disabled = false;
}




