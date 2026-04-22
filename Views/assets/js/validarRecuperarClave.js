$(document).ready(function () {

    $("#formSolicitarRecuperacion").submit(function (e) {
        let botonEnviar = $("#btnEnviarRecuperacion");

        if (botonEnviar.prop("disabled")) {
            e.preventDefault();
            return;
        }

        let correo = $("#correoUsuario").val().trim();
        let correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let valido = true;

        $("#errorCorreo").text("");
        $("#correoUsuario").removeClass("is-invalid is-valid");

        if (correo === "") {
            $("#errorCorreo").text("Debe ingresar un correo electronico");
            $("#correoUsuario").addClass("is-invalid");
            valido = false;
        } else if (!correoRegex.test(correo)) {
            $("#errorCorreo").text("Debe ingresar un correo valido");
            $("#correoUsuario").addClass("is-invalid");
            valido = false;
        } else {
            $("#correoUsuario").addClass("is-valid");
        }

        if (!valido) {
            e.preventDefault();
            return;
        }

        botonEnviar.prop("disabled", true);
        botonEnviar.text("Enviando...");
    });

});
