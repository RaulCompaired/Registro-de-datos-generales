$(document).ready(function() {
    // Si ya existe una sesión activa, redirige inmediatamente sin mostrar el formulario
    $.ajax({
        url: '../PHP/check_session.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.logged_in) {
                window.location.href = data.redirect;
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al verificar la sesión:", error);
        }
    });
});

function validarlogin(destino) {
    var errores = [];
    var correov = $("#usuario").val().trim();
    var rescorreo = correov !== "" ? true : null;
    marcarCampo("usuario", rescorreo);

    var passwordv = $("#contra").val().trim();
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
    } else {
        // Obtenemos los datos del formulario de manera serializada con jQuery
        var datosFormulario = $("#loginForm").serialize();

        $.ajax({
            url: '../PHP/sesion.php',
            type: 'POST',
            data: datosFormulario,
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    // Redirección instantánea sin doble alerta ni retardo artificial
                    window.location.href = data.redirect;
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
                    $("#btn-ingresar").prop("disabled", true);
                }
            },
            error: function(xhr, status, error) {
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
function enableBtn() {
    $("#btn-ingresar").prop("disabled", false);
}
