function validarlogin(){
        var errores = [];
        var boletav = document.forms.datos.boleta.value;
        var resboleta = boletav.match(/^\d{10}$|^(PE|PP)\d{8}$/);
        marcarCampo("boleta", resboleta);

        var passwordv = document.forms.datos.password.value;
        var respassword = passwordv.match(/^[A-Za-z0-9]*[A-Z]+[A-Za-z0-9]*[0-9]+[A-Za-z0-9]*[a-z]+[A-Za-z0-9]*$/);
        marcarCampo("password", respassword);

        if(resboleta == null)  errores.push("Boleta inválida");

        if(respassword == null) errores.push("Contraseña inválida");

          if (errores.length > 0) alert("los datos son inválidos.");
          else  alert("¡Inicio de sesión exitoso!");

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
    
