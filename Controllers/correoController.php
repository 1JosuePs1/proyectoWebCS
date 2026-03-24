<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/PHPMailer/src/PHPMailer.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/PHPMailer/src/SMTP.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/PHPMailer/src/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function EnviarCorreo($asunto, $contenido, $destinatario)
{
    $correoSalida = "osaborio90676@ufide.ac.cr";
    $contrasennaSalida = "Noahhermoso5";

    $mail = new PHPMailer(true);
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
    } catch (Exception $e) {
        return false;
    }
}
?>