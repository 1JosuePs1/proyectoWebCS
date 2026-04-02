$(document).ready(function () {

    $("#formAuth").submit(function (e) {
        let nombre = $("#InputName").val().trim();
        let correo = $("#InputEmail").val().trim();
        let password = $("#InputPassword").val().trim();
        let confirmPassword = $("#InputPasswordConfirm").val().trim();

        let correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        let valido = true;

        $("#errorNombre").text("");
        $("#errorCorreo").text("");
        $("#errorPassword").text("");
        $("#errorConfirmPassword").text("");

        $("#InputName").removeClass("is-invalid is-valid");
        $("#InputEmail").removeClass("is-invalid is-valid");
        $("#InputPassword").removeClass("is-invalid is-valid");
        $("#InputPasswordConfirm").removeClass("is-invalid is-valid");

        if (nombre === "") {
            $("#errorNombre").text("Debe ingresar el nombre completo");
            $("#InputName").addClass("is-invalid");
            valido = false;
        } else if (nombre.length < 3) {
            $("#errorNombre").text("El nombre debe tener al menos 3 caracteres");
            $("#InputName").addClass("is-invalid");
            valido = false;
        } else {
            $("#InputName").addClass("is-valid");
        }

        if (correo === "") {
            $("#errorCorreo").text("Debe ingresar un correo electrónico");
            $("#InputEmail").addClass("is-invalid");
            valido = false;
        } else if (!correoRegex.test(correo)) {
            $("#errorCorreo").text("Debe ingresar un correo válido");
            $("#InputEmail").addClass("is-invalid");
            valido = false;
        } else {
            $("#InputEmail").addClass("is-valid");
        }

        if (password === "") {
            $("#errorPassword").text("Debe ingresar una contraseña");
            $("#InputPassword").addClass("is-invalid");
            valido = false;
        } else if (!passwordRegex.test(password)) {
            $("#errorPassword").text("La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial");
            $("#InputPassword").addClass("is-invalid");
            valido = false;
        } else {
            $("#InputPassword").addClass("is-valid");
        }

        if (confirmPassword === "") {
            $("#errorConfirmPassword").text("Debe confirmar la contraseña");
            $("#InputPasswordConfirm").addClass("is-invalid");
            valido = false;
        } else if (password !== confirmPassword) {
            $("#errorConfirmPassword").text("Las contraseñas no coinciden");
            $("#InputPasswordConfirm").addClass("is-invalid");
            valido = false;
        } else {
            $("#InputPasswordConfirm").addClass("is-valid");
        }

        if (!valido) {
            e.preventDefault();
        }
    });

});
