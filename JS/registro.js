  function validar(){
        var errores = [];
        var boletav = document.forms.datos.boleta.value;
        var resboleta = boletav.match(/^\d{10}$|^(PE|PP)\d{8}$/);
        marcarCampo("boleta", resboleta);

        var curpv = document.forms.datos.curp.value;
        var rescurp = curpv.match(/^[A-Z]{4}\d{6}(H|M)[A-Z]{5}(\d{2}|[A-Z]\d)$/);
        marcarCampo("curp", rescurp);

        var nombrev = document.forms.datos.nombre.value;
        var resnombre = nombrev.match(/^[A-Z][a-z]+ [A-Z][a-z]+( |[A-Z a-z])*$/);
        marcarCampo("nombre", resnombre);

        var telv = document.forms.datos.telefono.value;
        var restel = telv.match(/^\d{10}$/);
        marcarCampo("telefono", restel);

        var califv = document.forms.datos.promedio.value;
        var rescalif = califv.match(/^([6-9](\.\d)?|10(\.0)?)$/);
        marcarCampo("promedio", rescalif);

        var correov = document.forms.datos.correo.value;
        var rescorreo = correov.match(/^[A-Za-z0-9_.]+@alumno.ipn.mx$/);
        marcarCampo("correo", rescorreo);

        var passwordv = document.forms.datos.password.value;
        var respass = passwordv.match(/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\s]).{6,}$/);
        marcarCampo("password", respass);

        const nacim = document.getElementById("nacimiento").value;
        const prom = document.getElementById("promedio").value;
        const tel = document.getElementById("telefono").value;
        const campoNacimiento = document.getElementById("nacimiento");
        const generoElemento = document.querySelector('input[name="genero"]:checked');
        const genero = generoElemento ? generoElemento.id : null;
        const generoRadios = document.querySelectorAll('input[name="genero"]');
        const pf = document.getElementById("procedenciafed").value;
        let ep = document.getElementById("escuelap").value;
        if (ep === "Otro") ep = document.getElementById("nombreesc").value;

        const fechaNacimientoValida = nacim && nacim >= campoNacimiento.min && nacim <= campoNacimiento.max;

        marcarCampo("nacimiento", fechaNacimientoValida ? "ok" : null);

        marcarCampo("procedenciafed", pf ? pf : null);
        marcarCampo("escuelap", ep ? ep : null);


        if (document.getElementById("escuelap").value === "Otro") {
            marcarCampo("nombreesc", ep ? ep : null);
        } else {
            marcarCampo("nombreesc", "ok");
        }
        

        if(resboleta == null)  errores.push("Boleta inválida");
        
        if(rescurp == null)  errores.push("Curp inválido");
        
        if(resnombre == null)  errores.push("Nombre inválida");
        
        if(restel == null)  errores.push("Telefono inválida");      
        
        if(rescorreo == null) errores.push("Correo inválida");

        if(respass == null) errores.push("Contraseña inválida");

        if (genero == null) errores.push("Debes elegir un genero");
        generoRadios.forEach(function(radio) {
            if (genero == null) {
                radio.classList.add("is-invalid");
                radio.classList.remove("is-valid");
            } else {
                radio.classList.remove("is-invalid");
                radio.classList.add("is-valid");
            }
        });

        if (!nacim) errores.push("Debes elegir una fecha de nacimiento");

        if (nacim && !fechaNacimientoValida) errores.push("La fecha de nacimiento debe estar entre 16 y 100 años");

        if (!pf) errores.push("Debes elegir una entidad federativa");

        if (!ep) errores.push("Debes elegir una escuela de procedencia");

        const listaModal = document.getElementById("modalp");  

         if (errores.length > 0) {
            document.getElementById("modalp").innerHTML = "<li class='list-group-item list-group-item-danger'>Los datos ingresados son inválidos</li>";
            var miModalErrores = new bootstrap.Modal(document.getElementById('modalform'));
            miModalErrores.show();
        } 
        else {
        document.getElementById("modalp").innerHTML = 
            "<li class='list-group-item'>No. Boleta: " + boletav + "</li>" +
            "<li class='list-group-item'>Nombre: " + nombrev + "</li>" +
            "<li class='list-group-item'>Fecha de Nacimiento: " + nacim + "</li>" +
            "<li class='list-group-item'>Género: " + genero + "</li>" +
            "<li class='list-group-item'>CURP: " + curpv + "</li>" +
            "<li class='list-group-item'>Entidad Federativa: " + pf + "</li>" +
            "<li class='list-group-item'>Telefono: " + tel + "</li>" +
            "<li class='list-group-item'>Escuela de Procedencia: " + ep + "</li>" +
            "<li class='list-group-item'>Promedio: " + prom + "</li>" +
            "<li class='list-group-item'>Correo: " + correov + "</li>" +
            "<li class='list-group-item'>Contraseña: " + passwordv + "</li>";

        var miModal = new bootstrap.Modal(document.getElementById('modalform'));
        miModal.show();
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

document.getElementById('escuelap').addEventListener('change', function() {
    var otroInput = document.getElementById('nombreesc');
    if (this.value === 'Otro') {
        otroInput.disabled = false;
        otroInput.focus();
    } else {
        otroInput.disabled = true;
        otroInput.value = ''; 
    }
});



//CALENDARIO
//Bloqueamos el calendario para que no puedan exigir fecha futura y que tengan almenos 16 (pensando en los genios) y una máximo de 100 años
window.addEventListener('DOMContentLoaded', function() {
    var campoNacimiento = document.getElementById('nacimiento');
    var hoy = new Date();
    
    //El mínimo de edad donde ademas comprobamos que sea de dos digitos para que se adapte al formato de html
    var fechaMax = new Date(hoy);
    fechaMax.setFullYear(fechaMax.getFullYear() - 16);
    var anioMax = fechaMax.getFullYear();
    var mesMax = String(fechaMax.getMonth() + 1).padStart(2, '0');
    var diaMax = String(fechaMax.getDate()).padStart(2, '0');
    campoNacimiento.max = anioMax + "-" + mesMax + "-" + diaMax;

    //Repetimos el mismo proceso pero para poner el máximo de edad en 100 años
    var fechaMin = new Date(hoy);
    fechaMin.setFullYear(fechaMin.getFullYear() - 100);
    var anioMin = fechaMin.getFullYear();
    var mesMin = String(fechaMin.getMonth() + 1).padStart(2, '0');
    var diaMin = String(fechaMin.getDate()).padStart(2, '0');
    campoNacimiento.min = anioMin + "-" + mesMin + "-" + diaMin;
});