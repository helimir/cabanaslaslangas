<?php
require_once(__DIR__ . '/../../PHPMailer/Exception.php');
require_once(__DIR__ . '/../../PHPMailer/PHPMailer.php');
require_once(__DIR__ . '/../../PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $isHTML = true) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP (valores en .env)
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST', 'localhost');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME', '');
        $mail->Password   = env('MAIL_PASSWORD', '');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls') === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = env_int('MAIL_PORT', 587);
        
        // Remitente
        $from_address = env('MAIL_FROM_ADDRESS', 'reservaciones@cabanaslaslengas.cl');
        $from_name    = env('MAIL_FROM_NAME', 'Cabañas Las Lengas');
        $mail->setFrom($from_address, $from_name);
        $mail->addReplyTo($from_address, 'Reservaciones');
        $mail->addAddress($to);
        
        // Contenido
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $e->getMessage());
        return false;
    }
}

// Función para generar el email de confirmación al cliente
function getConfirmationEmailClient($data) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Confirmación de Reserva</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background: #f8f9fa; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { text-align: center; border-bottom: 2px solid #f8c146; padding-bottom: 20px; }
            .header h1 { color: #f8c146; margin: 0; }
            .content { padding: 20px 0; }
            .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .info strong { color: #f8c146; }
            .footer { text-align: center; padding-top: 20px; border-top: 1px solid #ddd; color: #888; font-size: 12px; }
            .btn { display: inline-block; background: #f8c146; color: #fff; padding: 10px 25px; border-radius: 5px; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="' . SITE_URL . 'assets/img/logolengas.png" alt="Logo" style="max-height: 60px;">
                <h1>¡Gracias por tu reserva!</h1>
            </div>
            <div class="content">
                <p>Hola <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
                <p>Hemos recibido tu solicitud de reserva en <strong>Cabañas Las Lengas</strong>. A continuación los detalles:</p>
                <div class="info">
                    <p><strong>Cabaña:</strong> ' . htmlspecialchars($data['cabin_name']) . '</p>
                    <p><strong>Check-in:</strong> ' . date('d/m/Y', strtotime($data['check_in'])) . '</p>
                    <p><strong>Check-out:</strong> ' . date('d/m/Y', strtotime($data['check_out'])) . '</p>
                    <p><strong>Personas:</strong> ' . $data['people'] . '</p>
                </div>
                <p>Pronto nos pondremos en contacto contigo para confirmar la disponibilidad y los detalles del pago.</p>
                <p style="text-align: center; margin-top: 30px;">
                    <a href="' . SITE_URL . '" class="btn">Visitar Sitio Web</a>
                </p>
            </div>
            <div class="footer">
                <p>Cabañas Las Lengas - Gabriel Silva (Camino 1) 132, Puerto Natales</p>
                <p>Tel: (+56) 9 9349 9820 | Email: reservaciones@cabanaslaslengas.cl</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}

// Función para generar el email de notificación al admin
function getNotificationEmailAdmin($data) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Nueva Reserva</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background: #f8f9fa; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { text-align: center; border-bottom: 2px solid #f8c146; padding-bottom: 20px; }
            .header h1 { color: #f8c146; margin: 0; }
            .content { padding: 20px 0; }
            .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .info strong { color: #f8c146; }
            .footer { text-align: center; padding-top: 20px; border-top: 1px solid #ddd; color: #888; font-size: 12px; }
            .badge { display: inline-block; background: #f8c146; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🔔 Nueva Reserva</h1>
            </div>
            <div class="content">
                <p><strong>Se ha recibido una nueva solicitud de reserva</strong></p>
                <div class="info">
                    <p><strong>Cliente:</strong> ' . htmlspecialchars($data['name']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($data['email']) . '</p>
                    <p><strong>Teléfono:</strong> ' . htmlspecialchars($data['phone']) . '</p>
                    <p><strong>Cabaña:</strong> ' . htmlspecialchars($data['cabin_name']) . '</p>
                    <p><strong>Check-in:</strong> ' . date('d/m/Y', strtotime($data['check_in'])) . '</p>
                    <p><strong>Check-out:</strong> ' . date('d/m/Y', strtotime($data['check_out'])) . '</p>
                    <p><strong>Personas:</strong> ' . $data['people'] . '</p>
                    <p><strong>Mensaje:</strong> ' . htmlspecialchars($data['message'] ?: 'N/A') . '</p>
                </div>
                <p style="text-align: center; margin-top: 20px;">
                    <a href="' . ADMIN_URL . 'reservations.php" class="btn" style="display: inline-block; background: #f8c146; color: #fff; padding: 10px 25px; border-radius: 5px; text-decoration: none;">Ver en Admin</a>
                </p>
            </div>
            <div class="footer">
                <p>Cabañas Las Lengas - Sistema de Reservas</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}
?>