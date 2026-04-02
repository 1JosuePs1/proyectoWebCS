$(document).ready(function () {

    $("#formLogin").submit(function (e) {
        let correo = $("#correoUsuario").val().trim();
        let password = $("#passwordUsuario").val().trim();

        let correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let valido = true;

        $("#errorCorreo").text("");
        $("#errorPassword").text("");

        $("#correoUsuario").removeClass("is-invalid is-valid");
        $("#passwordUsuario").removeClass("is-invalid is-valid");

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

        if (password === "") {
            $("#errorPassword").text("Debe ingresar la contrasena");
            $("#passwordUsuario").addClass("is-invalid");
            valido = false;
        } else {
            $("#passwordUsuario").addClass("is-valid");
        }

        if (!valido) {
            e.preventDefault();
        }
    });

});
