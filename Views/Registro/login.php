<!DOCTYPE html>
<html lang="es">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">

    <!-- CSS -->
    <link rel="stylesheet" href="../../Views/assets/css/LoginYRegistro.css">
    <link rel="stylesheet" href="../../Views/assets/css/main.css">

    <link rel="icon" type="image/png" href="../../Views/assets/image/imgLogo/logo.png">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
                        <h3 class="mb-0">Iniciar Sesión</h3>
                    </div>

                    <div class="card-body p-4">
                        <form action="../../Controllers/loginController.php" method="POST" id="formLogin" novalidate>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="correoUsuario" name="correoUsuario" placeholder="Correo electrónico">
                                <label for="correoUsuario">Correo electrónico</label>
                                <small id="errorCorreo" class="text-danger"></small>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="passwordUsuario" name="passwordUsuario" placeholder="Contraseña">
                                <label for="passwordUsuario">Contraseña</label>
                                <small id="errorPassword" class="text-danger"></small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn backgroundPrincipal btn-lg text-white">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center py-3">
                        <small class="text-muted textLink"><a href="../../Views/Registro/cambiarClave.php" class="text-primary">Olvido su contraseña</a></small>
                    </div>
                    <div class="text-center py-3">
                        <small class="text-muted textLink">¿No tienes cuenta? <a href="../../Views/Registro/registro.php" class="text-primary">Regístrate aquí</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>