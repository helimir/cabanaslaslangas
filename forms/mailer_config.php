<?php
// forms/mailer_config.php
require_once('PHPMailer/Exception.php');
require_once('PHPMailer/PHPMailer.php');
require_once('PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Definir SITE_URL si no está definido
if (!defined('SITE_URL')) {
    // Intentar obtener la URL base automáticamente
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
    define('SITE_URL', $protocol . $host . $path);
}

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
        
        // Configuración de timeout (evita que se cuelgue)
        $mail->Timeout = 30;
        
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
        $mail->CharSet = 'UTF-8';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo a $to: " . $e->getMessage());
        return false;
    }
}

// Función para generar el email de confirmación al cliente
function getConfirmationEmailClient($data) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmación de Reserva</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background: #f8f9fa; padding: 20px; margin: 0; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { text-align: center; border-bottom: 2px solid #f8c146; padding-bottom: 20px; }
            .header h1 { color: #f8c146; margin: 10px 0 0 0; font-size: 28px; }
            .header img { max-height: 70px; }
            .content { padding: 20px 0; }
            .content p { line-height: 1.6; }
            .info { background: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #f8c146; }
            .info p { margin: 8px 0; }
            .info strong { color: #f8c146; }
            .footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px; }
            .btn { display: inline-block; background: #f8c146; color: #1a1a2e !important; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; }
            .btn:hover { background: #e6b03a; }
            .reservation-id { background: #1a1a2e; color: #f8c146; padding: 4px 12px; border-radius: 20px; font-size: 14px; display: inline-block; }
            @media (max-width: 480px) {
                .container { padding: 20px; }
                .header h1 { font-size: 22px; }
                .info { padding: 12px 15px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="' . SITE_URL . 'assets/img/logolengas.png" alt="Cabañas Las Lengas">
                <h1>¡Gracias por tu reserva!</h1>
            </div>
            <div class="content">
                <p>Hola <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
                <p>Hemos recibido tu solicitud de reserva en <strong>Cabañas Las Lengas</strong>.</p>
                <p style="text-align: center;">
                    <span class="reservation-id">Reserva #' . str_pad($data['reservation_id'], 6, '0', STR_PAD_LEFT) . '</span>
                </p>
                <div class="info">
                    <p><strong>🏠 Cabaña:</strong> ' . htmlspecialchars($data['cabin_name']) . '</p>
                    <p><strong>📅 Check-in:</strong> ' . date('d/m/Y', strtotime($data['check_in'])) . '</p>
                    <p><strong>📅 Check-out:</strong> ' . date('d/m/Y', strtotime($data['check_out'])) . '</p>
                    <p><strong>👤 Personas:</strong> ' . $data['people'] . '</p>
                </div>
                <p>Pronto nos pondremos en contacto contigo para confirmar la disponibilidad y los detalles del pago.</p>
                <p style="text-align: center; margin-top: 30px;">
                    <a href="' . SITE_URL . '" class="btn">🌐 Visitar Sitio Web</a>
                </p>
            </div>
            <div class="footer">
                <p><strong>Cabañas Las Lengas</strong></p>
                <p>Gabriel Silva (Camino 1) 132, Puerto Natales</p>
                <p>Tel: (+56) 9 9349 9820 | Email: reservaciones@cabanaslaslengas.cl</p>
                <p style="margin-top: 10px; color: #aaa;">Este es un mensaje automático, por favor no responder a este correo.</p>
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nueva Reserva</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background: #f8f9fa; padding: 20px; margin: 0; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { text-align: center; border-bottom: 2px solid #f8c146; padding-bottom: 20px; }
            .header h1 { color: #f8c146; margin: 0; font-size: 28px; }
            .content { padding: 20px 0; }
            .info { background: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #f8c146; }
            .info p { margin: 8px 0; }
            .info strong { color: #f8c146; }
            .footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; color: #888; font-size: 12px; }
            .btn { display: inline-block; background: #f8c146; color: #1a1a2e !important; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; }
            .btn:hover { background: #e6b03a; }
            .badge { display: inline-block; background: #f8c146; color: #1a1a2e; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            .reservation-id { background: #dc3545; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 14px; display: inline-block; }
            @media (max-width: 480px) {
                .container { padding: 20px; }
                .header h1 { font-size: 22px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🔔 Nueva Reserva</h1>
                <p style="color: #666; margin: 5px 0 0 0;">Se ha recibido una nueva solicitud</p>
            </div>
            <div class="content">
                <p style="text-align: center;">
                    <span class="reservation-id">Reserva #' . str_pad($data['reservation_id'], 6, '0', STR_PAD_LEFT) . '</span>
                </p>
                <div class="info">
                    <p><strong>👤 Cliente:</strong> ' . htmlspecialchars($data['name']) . '</p>
                    <p><strong>📧 Email:</strong> <a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></p>
                    <p><strong>📱 Teléfono:</strong> ' . htmlspecialchars($data['phone']) . '</p>
                    <p><strong>🏠 Cabaña:</strong> ' . htmlspecialchars($data['cabin_name']) . '</p>
                    <p><strong>📅 Check-in:</strong> ' . date('d/m/Y', strtotime($data['check_in'])) . '</p>
                    <p><strong>📅 Check-out:</strong> ' . date('d/m/Y', strtotime($data['check_out'])) . '</p>
                    <p><strong>👤 Personas:</strong> ' . $data['people'] . '</p>
                    <p><strong>💬 Mensaje:</strong> ' . htmlspecialchars($data['message'] ?: 'N/A') . '</p>
                </div>
                <p style="text-align: center; margin-top: 25px;">
                    <a href="' . ADMIN_URL . 'reservations.php" class="btn">📋 Ver en el Panel de Administración</a>
                </p>
            </div>
            <div class="footer">
                <p><strong>Cabañas Las Lengas</strong> - Sistema de Reservas</p>
                <p>Este es un mensaje automático del sistema.</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}
?>