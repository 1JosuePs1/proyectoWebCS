<!DOCTYPE html>
<html lang="es">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">

    <link rel="stylesheet" href="../../Views/assets/css/LoginYRegistro.css">
    <link rel="stylesheet" href="../../Views/assets/css/main.css">

    <link rel="icon" type="image/png" href="../../Views/assets/image/imgLogo/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
</head>

<body id="inicio">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-md-6 col-lg-4 mx-auto">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header backgroundPrincipal text-white text-center py-4">
                        <img src="../../Views/assets/image/imgLogo/logoBlanco.png" alt="">
                    </div>
                    <div class="card-header backgroundPrincipal text-white text-center py-4">
                        <h3 class="mb-0">Cambiar Contraseña</h3>
                    </div>

                    <div class="card-body p-4">
                        <form action="../../Controllers/recuperarController.php" method="POST" id="formRecuperar" novalidate>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="correoUsuario" name="correoUsuario" placeholder="Correo electrónico">
                                <label for="correoUsuario">Correo electrónico</label>
                                <small id="errorCorreo" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="nuevaPassword" name="nuevaPassword" placeholder="Nueva contraseña">
                                <label for="nuevaPassword">Nueva contraseña</label>
                                <small id="errorNuevaPassword" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="confirmarPassword" name="confirmarPassword" placeholder="Confirmar contraseña">
                                <label for="confirmarPassword">Confirmar contraseña</label>
                                <small id="errorConfirmarPassword" class="text-danger"></small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn backgroundPrincipal btn-lg text-white">Cambiar Contraseña</button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center py-3">
                        <small class="text-muted textLink">
                            <a href="../../Views/Registro/login.php" class="text-primary">Volver a Iniciar Sesión</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>