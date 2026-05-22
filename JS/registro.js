  function validar(){
        var errores = [];
        var boletav = document.forms.datos.boleta.value;
        var resboleta = boletav.match(/^\d{10}$|^(PE|PP)\d{8}$/);
        marcarCampo("boleta", resboleta);

        var curpv = document.forms.datos.curp.value;
        var rescurp = curpv.match(/^[A-Z]{4}\d{6}(H|M)[A-Z]{5}(\d{2}|[A-Z]\d)$/);
        marcarCampo("curp", rescurp);

        var nombrev = document.forms.datos.nombre.value;
        var resnombre = nombrev.match(/^[A-Z][a-z]+ [A-Z][a-z]+$/);
        marcarCampo("nombre", resnombre);

        var telv = document.forms.datos.telefono.value;
        var restel = telv.match(/^\d{10}$/);
        marcarCampo("telefono", restel);

        var correov = document.forms.datos.correo.value;
        var rescorreo = correov.match(/^[A-Za-z0-9_.]+@alumno.ipn.mx$/);
        marcarCampo("correo", rescorreo);

        if(resboleta == null)  errores.push("Boleta inválida");
        
        if(rescurp == null)  errores.push("Curp inválido");
        
        if(resnombre == null)  errores.push("Nombre inválida");
        
        if(restel == null)  errores.push("Telefono inválida");      
        
        if(rescorreo == null) errores.push("Correo inválida");

          if (errores.length > 0) alert("Algunos datos son inválidos.");
          else  alert("¡Registro exitoso! Boleta: " + boletav + "\nNombre: " + nombrev + "\nTelefono: " + telv + "\nCorreo Instituacional: " + correov + "\nCURP: " + curpv );

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

document.getElementById('escuelap').addEventListener('change', function() {
    var otroInput = document.getElementById('nombreesc');
    if (this.value === '23') {
        otroInput.disabled = false;
        otroInput.focus();
    } else {
        otroInput.disabled = true;
        otroInput.value = ''; 
    }
});
