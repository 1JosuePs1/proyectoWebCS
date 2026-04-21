<?php
$phpMailerSrc = dirname(__DIR__) . "/PHPMailer/src/";
require_once($phpMailerSrc . "PHPMailer.php");
require_once($phpMailerSrc . "SMTP.php");
require_once($phpMailerSrc . "Exception.php");

function EnviarCorreo($asunto, $contenido, $destinatario)
{
    $correoSalida = "osaborio90676@ufide.ac.cr";
    $contrasennaSalida = "Noahhermoso5";

    $claseMailer = class_exists('PHPMailer\\PHPMailer\\PHPMailer')
        ? 'PHPMailer\\PHPMailer\\PHPMailer'
        : 'PHPMailer';

    $mail = new $claseMailer(true);
    $mail->CharSet = "UTF-8";

    try {
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->Host = "smtp.office365.com";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = $correoSalida;
        $mail->Password = $contrasennaSalida;

        $mail->setFrom($correoSalida);
        $mail->Subject = $asunto;
        $mail->MsgHTML($contenido);
        $mail->addAddress($destinatario);

        return $mail->send();
    } catch (\Throwable $e) {
        return false;
    }
}
?>