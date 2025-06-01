<?php
require_once RUTA_APP . '/librerias/PHPMailer/src/SMTP.php';
require_once RUTA_APP . '/librerias/PHPMailer/src/Exception.php';
require_once RUTA_APP . '/librerias/PHPMailer/src/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Correo
{
    public static function enviarBonito($destinatario, $asunto, $contenido, $botonTexto = null, $botonEnlace = null)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'phpprueba3000@gmail.com';
            $mail->Password = 'dpeb zwzm fpla ilpx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('phpprueba3000@gmail.com', 'IntraLink');
            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $asunto;

            // Construcción del cuerpo HTML
            $html = "
            <html>
            <head>
              <style>
                body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; color: #333; }
                .card { background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                .titulo { font-size: 20px; color: #0d6efd; margin-bottom: 10px; }
                .contenido { font-size: 16px; line-height: 1.5; margin-bottom: 20px; }
                .boton {
                    display: inline-block;
                    padding: 10px 20px;
                    background: #0d6efd;
                    color: #fff !important;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    }
                .footer { font-size: 13px; color: #777; margin-top: 20px; text-align: center; }
              </style>
            </head>
            <body>
              <div class='card'>
                <div class='titulo'>{$asunto}</div>
                <div class='contenido'>{$contenido}</div>";

            if ($botonTexto && $botonEnlace) {
                $html .= "<div><a class='boton' href='{$botonEnlace}' target='_blank'>{$botonTexto}</a></div>";
            }

            $html .= "<div class='footer'>Este correo fue enviado automáticamente desde IntraLink.</div>
              </div>
            </body>
            </html>";

            $mail->Body = $html;

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar correo: {$mail->ErrorInfo}");
            return false;
        }
    }
}
