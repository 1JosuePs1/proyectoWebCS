<?php
session_start();
$correoRecuperacion = trim($_GET['correo'] ?? '');

if ($correoRecuperacion === '') {
    $_SESSION['error_recuperar'] = "Primero solicita el enlace de recuperacion";
    header("Location: ../../Views/Registro/recuperarClave.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">

    <link rel="stylesheet" href="../../Views/assets/css/main.css">

    <link rel="icon" type="image/png" href="../../Views/assets/image/imgLogo/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                            <input type="hidden" name="accion" value="cambiar">

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="correoUsuario" name="correoUsuario" value="<?= htmlspecialchars($correoRecuperacion) ?>" placeholder="Correo electrónico" readonly>
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
                            <a href="../../Views/Registro/recuperarClave.php" class="text-primary">Volver a Recuperar</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/proyectoWebCS/Views/assets/js/validarCambiarClave.js"></script>
    <script src="/proyectoWebCS/Views/assets/js/modalError.js"></script>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/modalError.php"; ?>

    <?php if (isset($_SESSION['error_recuperar'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarModalError(<?= json_encode($_SESSION['error_recuperar']) ?>);
        });
    </script>
    <?php unset($_SESSION['error_recuperar']); endif; ?>
</body>
</html>