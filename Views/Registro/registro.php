<!DOCTYPE html>
<html lang="es">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">

    <!-- CONEXION CON BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- CONEXION CON JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- CSS LOGIN Y REGISTRO -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body id="inicio" class="bg-light">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-md-6 col-lg-4 mx-auto">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header backgroundPrincipal text-white text-center py-4">
                        <img src="../assets/image/imgLogo/logoBlanco.png" alt="">
                    </div>
                    <div class="card-header backgroundPrincipal text-white text-center py-4">
                        <h3 class="mb-0">Registro de Usuario</h3>
                    </div>

                    <div class="card-body p-4">
                        <form action="../../Controllers/registroController.php" method="POST" id="formAuth" novalidate>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="InputName" name="nombreUsuario" placeholder="Nombre completo">
                                <label for="InputName">Nombre completo</label>
                                <small id="errorNombre" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="InputEmail" name="correoUsuario" placeholder="Correo">
                                <label for="InputEmail">Correo electrónico</label>
                                <small id="errorCorreo" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="InputPassword" name="passwordUsuario" placeholder="Contraseña">
                                <label for="InputPassword">Contraseña</label>
                                <small id="errorPassword" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="InputPasswordConfirm" name="confirmPassword" placeholder="Confirmar">
                                <label for="InputPasswordConfirm">Confirmar contraseña</label>
                                <small id="errorConfirmPassword" class="text-danger"></small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn backgroundPrincipal btn-lg text-white">Registrarse</button>
                            </div>

                            <div class="text-center py-3">
                                <small class="text-muted">Ya tienes cuenta <a href="../../index.php" class="text-primary">Login</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
