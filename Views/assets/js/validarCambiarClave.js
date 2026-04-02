$(document).ready(function () {

    $("#formRecuperar").submit(function (e) {
        let correo = $("#correoUsuario").val().trim();
        let nuevaPassword = $("#nuevaPassword").val().trim();
        let confirmarPassword = $("#confirmarPassword").val().trim();

        let correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        let valido = true;

        $("#errorCorreo").text("");
        $("#errorNuevaPassword").text("");
        $("#errorConfirmarPassword").text("");

        $("#correoUsuario").removeClass("is-invalid is-valid");
        $("#nuevaPassword").removeClass("is-invalid is-valid");
        $("#confirmarPassword").removeClass("is-invalid is-valid");

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

        if (nuevaPassword === "") {
            $("#errorNuevaPassword").text("Debe ingresar una nueva contraseña");
            $("#nuevaPassword").addClass("is-invalid");
            valido = false;
        } else if (!passwordRegex.test(nuevaPassword)) {
            $("#errorNuevaPassword").text("La contraseña debe tener minimo 8 caracteres, una mayuscula, un numero y un caracter especial");
            $("#nuevaPassword").addClass("is-invalid");
            valido = false;
        } else {
            $("#nuevaPassword").addClass("is-valid");
        }

        if (confirmarPassword === "") {
            $("#errorConfirmarPassword").text("Debe confirmar la contraseña");
            $("#confirmarPassword").addClass("is-invalid");
            valido = false;
        } else if (nuevaPassword !== confirmarPassword) {
            $("#errorConfirmarPassword").text("Las contraseñas no coinciden");
            $("#confirmarPassword").addClass("is-invalid");
            valido = false;
        } else {
            $("#confirmarPassword").addClass("is-valid");
        }

        if (!valido) {
            e.preventDefault();
        }
    });

});
