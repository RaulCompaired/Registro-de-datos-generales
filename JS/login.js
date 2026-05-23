function validarlogin(destino){
        var errores = [];
        var boletav = document.forms.datos.boleta.value;
        var resboleta = boletav.match(/^\d{10}$|^(PE|PP)\d{8}$/);
        marcarCampo("boleta", resboleta);

        var passwordv = document.forms.datos.password.value;
        var respassword = passwordv.match(/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\s]).{6,}$/);
        marcarCampo("password", respassword);

        if(resboleta == null)  errores.push("Boleta inválida");
        if(respassword == null) errores.push("Contraseña inválida");

        if (errores.length > 0) {
            // Si hay errores, solo mostramos la alerta y el código se detiene aquí. No avanza.
            alert("Los datos son inválidos.");
        } else {
            // Si no hay errores, mostramos el éxito y redirigimos a la cuenta
            alert("¡Inicio de sesión exitoso!");
            window.location.href = destino; 
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
    
