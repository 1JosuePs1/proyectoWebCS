<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/usuarioController.php";

$usuario = ObtenerUsuarioPorIdController($_SESSION['idUsuario']);

$campos = [
    ['label' => 'Nombre completo',    'name' => 'nombreCompleto',  'type' => 'text',  'value' => $usuario['nombreCompleto'],  'readonly' => false],
    ['label' => 'Correo electrónico', 'name' => 'emailUsuario',    'type' => 'email', 'value' => $usuario['emailUsuario'],    'readonly' => true],
    ['label' => 'Fecha de registro',  'name' => 'fechaRegistro',   'type' => 'date',  'value' => $usuario['fechaRegistro'],   'readonly' => true],
    ['label' => 'Estado',             'name' => 'estadoUsuario',   'type' => 'text',  'value' => $usuario['estadoUsuario'],   'readonly' => true],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Perfil | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body class="bg-light">

    <?php require('../components/nav.php') ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-header backgroundPrincipal text-white text-center py-3">
                        <h3 class="mb-0"><i class="bi bi-person-circle me-2"></i>Mi Perfil</h3>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == '1'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Perfil actualizado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_usuario'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['error_usuario']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php unset($_SESSION['error_usuario']); endif; ?>

                        <form action="../../Controllers/usuarioController.php" method="POST" id="formPerfil">

                            <?php foreach ($campos as $campo): ?>
                                <div class="form-floating mb-3">
                                    <input
                                        type="<?= htmlspecialchars($campo['type']) ?>"
                                        class="form-control"
                                        id="input_<?= htmlspecialchars($campo['name']) ?>"
                                        name="<?= htmlspecialchars($campo['name']) ?>"
                                        placeholder="<?= htmlspecialchars($campo['label']) ?>"
                                        value="<?= htmlspecialchars($campo['value'] ?? '') ?>"
                                        <?= $campo['readonly'] ? 'readonly' : '' ?>
                                    >
                                    <label for="input_<?= htmlspecialchars($campo['name']) ?>">
                                        <?= htmlspecialchars($campo['label']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <hr class="my-3">
                            <p class="text-muted small mb-2">Dejar en blanco para mantener la contraseña actual</p>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="inputPasswordNueva" name="passwordNueva" placeholder="Nueva contraseña">
                                <label for="inputPasswordNueva">Nueva contraseña</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="inputConfirmarPassword" name="confirmarPassword" placeholder="Confirmar contraseña">
                                <label for="inputConfirmarPassword">Confirmar contraseña</label>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn backgroundPrincipal btn-lg text-white">
                                    <i class="bi bi-save me-2"></i>Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
