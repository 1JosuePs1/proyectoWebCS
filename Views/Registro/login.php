<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">

    <!-- CSS -->
 
    <link rel="stylesheet" href="../../Views/assets/css/main.css">

    <link rel="icon" type="image/png" href="../../Views/assets/image/imgLogo/logo.png">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="/proyectoWebCS/Views/assets/js/validarLogin.js"></script>
    <script src="/proyectoWebCS/Views/assets/js/modalError.js"></script>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/modalError.php"; ?>

    <?php if (isset($_SESSION['error_login'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarModalError(<?= json_encode($_SESSION['error_login']) ?>);
        });
    </script>
    <?php unset($_SESSION['error_login']); endif; ?>
</body>

</html>