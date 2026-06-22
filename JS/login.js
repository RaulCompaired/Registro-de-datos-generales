$(document).ready(function () {
    // Si ya existe una sesión activa, redirige inmediatamente sin mostrar el formulario
    $.ajax({
        url: '../PHP/check_session.php',
        type: 'GET',
        success: function (rol) {
            var rolLimpio = rol.trim();
            if (rolLimpio === 'alumno') {
                window.location.href = '../HTML/cuenta.html';
            } else if (rolLimpio === 'admin') {
                window.location.href = '../HTML/admin.html';
            } else {
                verificarParametrosRegistro();
            }
        },
        error: function (xhr, estado, error) {
            console.error("Error al verificar la sesión:", error);
            verificarParametrosRegistro();
        }
    });
});

function verificarParametrosRegistro() {
    var parametrosUrl = new URLSearchParams(window.location.search);
    if (parametrosUrl.get('registrado') === 'true') {
        var boleta = parametrosUrl.get('boleta');
        // Limpiamos los parámetros de la URL para que recargar la página no vuelva a disparar la alerta
        window.history.replaceState({}, document.title, window.location.pathname);

        Swal.fire({
            title: '¡Registro Exitoso!',
            text: 'Tus datos fueron guardados correctamente.',
            icon: 'success',
            confirmButtonColor: '#800020',
            confirmButtonText: 'Aceptar'
        }).then(function () {
            window.location.href = '../PHP/generarPDF.php?boleta=' + encodeURIComponent(boleta);
        });
    }
}

function validarlogin(destino) {
    var errores = [];
    var correov = $("#usuario").val().trim();
    var rescorreo = correov !== "" ? true : null;
    marcarCampo("usuario", rescorreo);

    var contrasenav = $("#contra").val().trim();
    var rescontrasena = contrasenav !== "" ? true : null;
    marcarCampo("contra", rescontrasena);

    if (rescorreo === null) errores.push("El usuario es obligatorio");
    if (rescontrasena === null) errores.push("La contraseña es obligatoria");

    if (errores.length > 0) {
        Swal.fire({
            title: 'Error',
            text: 'Errores encontrados:\n' + errores.join('\n'),
            icon: 'error',
            confirmButtonColor: '#800020'
        });
    } else {
        // Obtenemos los datos del formulario de manera serializada con jQuery
        var datosFormulario = $("#loginForm").serialize();

        $.ajax({
            url: '../PHP/sesion.php',
            type: 'POST',
            data: datosFormulario,
            success: function (respuesta) {
                var res = respuesta.trim();
                if (res === 'alumno') {
                    window.location.href = '../HTML/cuenta.html';
                } else if (res === 'admin') {
                    window.location.href = '../HTML/admin.html';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res,
                        icon: 'error',
                        confirmButtonColor: '#800020'
                    });
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                    $("#btn-ingresar").prop("disabled", true);
                }
            },
            error: function (xhr, estado, error) {
                console.error("Error en la petición AJAX:", error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un problema al conectar con el servidor.',
                    icon: 'error',
                    confirmButtonColor: '#800020'
                });
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
                $("#btn-ingresar").prop("disabled", true);
            }
        });
    }
}

function marcarCampo(id, resultado) {
    var $campo = $("#" + id);
    if (resultado == null) {
        $campo.addClass("is-invalid").removeClass("is-valid");
    } else {
        $campo.removeClass("is-invalid").addClass("is-valid");
    }
}

// Callback para habilitar el botón desde reCAPTCHA
function habilitarBoton() {
    $("#btn-ingresar").prop("disabled", false);
}
