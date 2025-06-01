<?php
// require_once 'PHPMailer/src/SMTP.php';
// require_once 'PHPMailer/src/Exception.php';
// require_once 'PHPMailer/src/PHPMailer.php';

require_once RUTA_APP . '/librerias/PHPMailer/src/SMTP.php';
require_once RUTA_APP . '/librerias/PHPMailer/src/Exception.php';
require_once RUTA_APP . '/librerias/PHPMailer/src/PHPMailer.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Enviar correo de confirmación de alquiler al cliente.
 *
 * @param array $datos Datos del alquiler y cliente.
 * @param string $destinatario Dirección de correo electrónico del cliente.
 * @return bool|string True si el correo se envía correctamente, mensaje de error en caso contrario.
 */
if (!function_exists('enviarCorreoFactura')) {
    function enviarCorreoFactura($datos, string $destinatario)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'phpprueba3000@gmail.com';
            $mail->Password = 'dpeb zwzm fpla ilpx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('phpprueba3000@gmail.com', 'Concesionario de Vehículos');

            // Codificación del mensaje
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Destinatario
            $mail->addAddress($destinatario);

            // Obtener el idioma de las traducciones 
            $idioma = $datos['idioma'] ?? 'espanol';
            $traducciones = $datos['traducciones'] ?? [];

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $traducciones[55] ?? 'Confirmación de Alquiler de Vehículo'; // Título del correo

            $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    h2 { color: #4CAF50; }
                    p { font-size: 16px; line-height: 1.5; }
                    .invoice-section { margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                    .invoice-section h4 { color: #4CAF50; font-size: 18px; }
                    .invoice-price { font-size: 20px; font-weight: bold; color: #4CAF50; }
                    .footer { margin-top: 30px; text-align: center; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='invoice-header'>
                    <h2>" . ($traducciones[55] ?? 'Factura de Alquiler') . "</h2>
                </div>
                
                <div class='invoice-section'>
                    <h4>" . ($traducciones[58] ?? 'Datos del Cliente') . "</h4>
                    <p><strong>" . ($traducciones[59] ?? 'Nombre') . ":</strong> " . htmlspecialchars($datos['cliente']->nombre) . " " . htmlspecialchars($datos['cliente']->apellidos) . "</p>
                    <p><strong>" . ($traducciones[60] ?? 'DNI') . ":</strong> " . htmlspecialchars($datos['cliente']->documento_identidad) . "</p>
                    <p><strong>" . ($traducciones[61] ?? 'Email') . ":</strong> " . htmlspecialchars($datos['cliente']->email) . "</p>
                    <p><strong>" . ($traducciones[62] ?? 'Teléfono') . ":</strong> " . htmlspecialchars($datos['cliente']->telefono) . "</p>
                    <p><strong>" . ($traducciones[63] ?? 'Dirección') . ":</strong> " . htmlspecialchars($datos['cliente']->direccion) . "</p>
                </div>

                <div class='invoice-section'>
                    <h4>" . ($traducciones[64] ?? 'Detalles del Vehículo') . "</h4>
                    <p><strong>" . ($traducciones[65] ?? 'Matrícula') . ":</strong> " . htmlspecialchars($datos['vehiculo']->matricula) . "</p>
                    <p><strong>" . ($traducciones[66] ?? 'Marca') . ":</strong> " . htmlspecialchars($datos['vehiculo']->marca) . "</p>
                    <p><strong>" . ($traducciones[67] ?? 'Modelo') . ":</strong> " . htmlspecialchars($datos['vehiculo']->modelo) . "</p>
                    <p><strong>" . ($traducciones[68] ?? 'Potencia') . ":</strong> " . htmlspecialchars($datos['vehiculo']->potencia) . " CV</p>
                    <p><strong>" . ($traducciones[69] ?? 'Velocidad Máxima') . ":</strong> " . htmlspecialchars($datos['vehiculo']->velocidad_maxima) . " km/h</p>
                </div>

                <div class='invoice-section'>
                    <h4>" . ($traducciones[70] ?? 'Detalles del Alquiler') . "</h4>
                    <p><strong>" . ($traducciones[71] ?? 'Fecha de Inicio') . ":</strong> " . htmlspecialchars($datos['fecha_inicio']) . "</p>
                    <p><strong>" . ($traducciones[72] ?? 'Fecha de Fin') . ":</strong> " . htmlspecialchars($datos['fecha_fin']) . "</p>
                    <p><strong>" . ($traducciones[73] ?? 'Número de Días') . ":</strong> " . (strtotime($datos['fecha_fin']) - strtotime($datos['fecha_inicio'])) / (60 * 60 * 24) . "</p>
                </div>

                <div class='invoice-price'>
                    <strong>" . ($traducciones[74] ?? 'Precio Total') . ":</strong> " . number_format($datos['precio_total'], 2) . " €
                </div>

                <div class='footer'>
                    <p>" . ($traducciones[75] ?? 'Gracias por confiar en') . " <strong>Alquileres Javier</strong>. " . ($traducciones[76] ?? 'Esperamos que disfrute su experiencia.') . "</p>
                </div>
            </body>
            </html>
            ";

            // Asignar el cuerpo al correo
            $mail->Body = $body;

            // Enviar correo
            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }
}

