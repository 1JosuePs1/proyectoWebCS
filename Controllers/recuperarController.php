<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/recuperarModel.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoController.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoTemplates.php");

session_start();
$accion = $_POST['accion'] ?? '';
$correo = trim($_POST['correoUsuario'] ?? '');
$nuevaPassword = trim($_POST['nuevaPassword'] ?? '');
$confirmarPassword = trim($_POST['confirmarPassword'] ?? '');

if ($accion === 'solicitar') {
    if (empty($correo)) {
        $_SESSION['error_recuperar'] = "Debe ingresar un correo";
        header("Location: ../Views/Registro/recuperarClave.php");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_recuperar'] = "El correo electronico no es valido";
        header("Location: ../Views/Registro/recuperarClave.php");
        exit();
    }

    $usuario = VerificarCorreoUsuarioModel($correo);

    if (!$usuario) {
        $_SESSION['error_recuperar'] = "Ese correo no esta registrado";
        header("Location: ../Views/Registro/recuperarClave.php");
        exit();
    }

    $correoUsuario = $usuario['emailUsuario'] ?? $correo;
    $nombreUsuario = trim($usuario['nombreCompleto'] ?? '');
    $correoClaveCooldown = strtolower($correoUsuario);
    $cooldownSegundos = 45;

    if (!isset($_SESSION['recuperar_ultimo_envio'])) {
        $_SESSION['recuperar_ultimo_envio'] = [];
    }

    $ultimoEnvio = $_SESSION['recuperar_ultimo_envio'][$correoClaveCooldown] ?? 0;
    $segundosTranscurridos = time() - intval($ultimoEnvio);

    if ($segundosTranscurridos < $cooldownSegundos) {
        $segundosRestantes = $cooldownSegundos - $segundosTranscurridos;
        $_SESSION['error_recuperar'] = "Espera " . $segundosRestantes . " segundos para reenviar el enlace";
        header("Location: ../Views/Registro/recuperarClave.php");
        exit();
    }

    if ($nombreUsuario === '') {
        $parteCorreo = explode('@', $correoUsuario)[0] ?? '';
        $nombreUsuario = ucwords(str_replace(['.', '_', '-'], ' ', $parteCorreo));
    }

    $enlaceCambio = "http://" . $_SERVER['HTTP_HOST'] . "/proyectoWebCS/Views/Registro/cambiarClave.php?correo=" . urlencode($correoUsuario);
    $contenidoCorreo = ConstruirPlantillaRecuperacionClave($nombreUsuario, $correoUsuario, $enlaceCambio);

    if (!EnviarCorreo("Recuperar acceso | My PC Gaming", $contenidoCorreo, $correoUsuario)) {
        $_SESSION['error_recuperar'] = "No se pudo enviar el correo de recuperacion";
        header("Location: ../Views/Registro/recuperarClave.php");
        exit();
    }

    $_SESSION['recuperar_ultimo_envio'][$correoClaveCooldown] = time();

    $_SESSION['mensaje_recuperar'] = "Te enviamos un correo con el enlace para cambiar tu contrasena";
    header("Location: ../Views/Registro/recuperarClave.php");
    exit();
}

if ($accion !== 'cambiar') {
    $_SESSION['error_recuperar'] = "Accion de recuperacion invalida";
    header("Location: ../Views/Registro/recuperarClave.php");
    exit();
}

if (empty($correo) || empty($nuevaPassword) || empty($confirmarPassword)) {
    $_SESSION['error_recuperar'] = "Todos los campos son obligatorios";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_recuperar'] = "El correo electronico no es valido";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}

if (strlen($nuevaPassword) < 8) {
    $_SESSION['error_recuperar'] = "La contrasena debe tener al menos 8 caracteres";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}

if ($nuevaPassword !== $confirmarPassword) {
    $_SESSION['error_recuperar'] = "Las contrasenas no coinciden";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}

$usuario = VerificarCorreoUsuarioModel($correo);

if (!$usuario) {
    $_SESSION['error_recuperar'] = "Ese correo no esta registrado";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}

$passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

if (ActualizarPasswordUsuarioModel($correo, $passwordHash)) {
    $_SESSION['mensaje_login'] = "Contrasena actualizada, ya puedes iniciar sesion";
    header("Location: ../Views/Registro/login.php");
    exit();
} else {
    $_SESSION['error_recuperar'] = "Error al actualizar la contrasena";
    header("Location: ../Views/Registro/cambiarClave.php?correo=" . urlencode($correo));
    exit();
}
?>