<?php
// forms/contacto.php
require_once '../admin/includes/config.php';

// ============================================================
// RECIBIR DATOS DEL FORMULARIO
// ============================================================
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$people = (int)($_POST['people'] ?? 0);
$check_in = $_POST['date1'] ?? '';
$check_out = $_POST['date2'] ?? '';
$message = trim($_POST['message'] ?? '');
$lang = $_POST['lang'] ?? 'es'; // <-- RECIBIR EL IDIOMA

// ============================================================
// OBTENER NOMBRE DE LA CABAÑA (desde la base de datos)
// ============================================================
$cabin_id = isset($_POST['cabin']) && is_numeric($_POST['cabin']) ? (int)$_POST['cabin'] : null;
$cabin_name = $lang == 'es' ? 'No especificada' : 'Not specified';

if ($cabin_id) {
    try {
        $stmt = $pdo->prepare("SELECT name_es, name_en FROM cabins WHERE id = ?");
        $stmt->execute([$cabin_id]);
        $cabin_row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cabin_row) {
            $cabin_name = $lang == 'es' ? $cabin_row['name_es'] : $cabin_row['name_en'];
        }
    } catch (PDOException $e) {
        error_log("Error al obtener cabaña: " . $e->getMessage());
    }
}

// ============================================================
// VALIDACIONES
// ============================================================
$errors = [];
if (empty($name)) $errors[] = $lang == 'es' ? 'Nombre requerido' : 'Name required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = $lang == 'es' ? 'Email inválido' : 'Invalid email';
if (empty($phone)) $errors[] = $lang == 'es' ? 'Teléfono requerido' : 'Phone required';
if (empty($check_in) || empty($check_out)) $errors[] = $lang == 'es' ? 'Fechas requeridas' : 'Dates required';
if ($check_in >= $check_out) $errors[] = $lang == 'es' ? 'Check-out debe ser mayor a Check-in' : 'Check-out must be after Check-in';

if (!empty($errors)) {
    header('Location: ../index.php?send=false#contacto');
    exit;
}

// ============================================================
// GUARDAR EN BASE DE DATOS
// ============================================================
try {
    $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, cabin_name, cabin_id, check_in, check_out, people, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$name, $email, $phone, $cabin_name, $cabin_id, $check_in, $check_out, $people, $message]);
    $reservation_id = $pdo->lastInsertId();
    
    // Datos para el email
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'cabin_name' => $cabin_name,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'people' => $people,
        'message' => $message,
        'reservation_id' => $reservation_id,
        'lang' => $lang // <-- PASAR EL IDIOMA
    ];
    
    // ============================================================
    // ENVIAR EMAIL AL CLIENTE (EN EL IDIOMA SELECCIONADO)
    // ============================================================
    if ($lang == 'en') {
        $client_subject = 'Reservation Confirmation - Cabañas Las Lengas';
        $client_body = getConfirmationEmailClientEn($data);
    } else {
        $client_subject = 'Confirmación de Reserva - Cabañas Las Lengas';
        $client_body = getConfirmationEmailClientEs($data);
    }
    sendEmail($email, $client_subject, $client_body);
    
    // ============================================================
    // ENVIAR EMAIL AL ADMIN (SIEMPRE EN ESPAÑOL)
    // ============================================================
    $admin_subject = 'Nueva Reserva - Cabañas Las Lengas';
    $admin_body = getNotificationEmailAdmin($data);
    sendEmail(env('MAIL_ADMIN_ADDRESS', 'reservaciones@cabanaslaslengas.cl'), $admin_subject, $admin_body);
    
    // Redirigir según idioma
    $redirect_anchor = $lang == 'en' ? '#contacto' : '#contacto';
    header('Location: ../index.php?send=true' . $redirect_anchor);
    
} catch (PDOException $e) {
    error_log("Error al guardar reserva: " . $e->getMessage());
    header('Location: ../index.php?send=false#contacto');
}

// ============================================================
// FUNCIÓN PARA ENVIAR EMAIL
// ============================================================
function sendEmail($to, $subject, $body, $isHTML = true) {
    $from = env('MAIL_FROM_ADDRESS', 'reservaciones@cabanaslaslengas.cl');
    $from_name = env('MAIL_FROM_NAME', 'Cabañas Las Lengas');
    $headers = "From: " . $from_name . " <" . $from . ">\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to, $subject, $body, $headers);
}

// ============================================================
// EMAIL CLIENTE - ESPAÑOL
// ============================================================
function getConfirmationEmailClientEs($data) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmación de Reserva</title>
        <style>
            body, html { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f7fa; }
            .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
            .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px 30px 25px; text-align: center; border-bottom: 4px solid #f8c146; }
            .header img { max-height: 60px; margin-bottom: 10px; }
            .header h1 { color: #f8c146; font-size: 26px; margin: 10px 0 0; font-weight: 700; letter-spacing: 1px; }
            .header .subtitle { color: rgba(255,255,255,0.7); font-size: 14px; margin: 5px 0 0; }
            .content { padding: 30px; }
            .content p { color: #444; line-height: 1.7; font-size: 15px; margin: 0 0 15px; }
            .content .greeting { font-size: 17px; font-weight: 600; color: #1a1a2e; }
            .content .greeting strong { color: #f8c146; }
            .reservation-badge { display: inline-block; background: #1a1a2e; color: #f8c146; padding: 6px 18px; border-radius: 50px; font-size: 14px; font-weight: 600; letter-spacing: 0.5px; margin: 10px 0 15px; }
            .info-card { background: #f8f9fa; border-radius: 12px; padding: 20px 25px; margin: 20px 0; border-left: 4px solid #f8c146; }
            .info-card p { margin: 8px 0; font-size: 15px; color: #333; }
            .info-card .label { font-weight: 600; color: #1a1a2e; display: inline-block; min-width: 100px; }
            .info-card .value { color: #555; }
            .btn { display: inline-block; background: #f8c146; color: #1a1a2e !important; text-decoration: none !important; padding: 14px 35px; border-radius: 50px; font-weight: 700; font-size: 16px; transition: all 0.3s ease; border: none; cursor: pointer; margin: 15px 0 5px; }
            .btn:hover { background: #e6b03a; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(248,193,70,0.3); }
            .btn-container { text-align: center; }
            .divider { border: none; border-top: 2px solid #eee; margin: 25px 0; }
            .footer { background: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #eee; }
            .footer p { margin: 5px 0; font-size: 13px; color: #888; line-height: 1.6; }
            .footer .brand { font-weight: 700; color: #1a1a2e; }
            .footer .brand span { color: #f8c146; }
            .footer .social-links { margin: 10px 0 0; }
            .footer .social-links a { display: inline-block; margin: 0 6px; color: #888; text-decoration: none; font-size: 18px; transition: color 0.3s; }
            .footer .social-links a:hover { color: #f8c146; }
            .footer .disclaimer { font-size: 11px; color: #aaa; margin-top: 12px; }
            @media (max-width: 480px) {
                .email-container { margin: 10px; border-radius: 12px; }
                .header { padding: 20px; }
                .header h1 { font-size: 22px; }
                .content { padding: 20px; }
                .info-card { padding: 15px; }
                .info-card .label { min-width: 80px; font-size: 13px; }
                .btn { padding: 12px 25px; font-size: 14px; }
                .footer { padding: 20px; }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <img src="https://cabanaslaslengas.cl/assets/img/logolengas.png" alt="Cabañas Las Lengas">
                <h1>¡Gracias por tu reserva!</h1>
                <p class="subtitle">Hemos recibido tu solicitud correctamente</p>
            </div>
            <div class="content">
                <p class="greeting">Hola <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
                <p>Nos alegra que hayas elegido <strong>Cabañas Las Lengas</strong> para tu estadía en la Patagonia Chilena. Tu solicitud de reserva ha sido recibida y nuestro equipo la revisará a la brevedad.</p>
                <div style="text-align: center;">
                    <span class="reservation-badge">🔑 Reserva #' . str_pad($data['reservation_id'], 6, '0', STR_PAD_LEFT) . '</span>
                </div>
                <div class="info-card">
                    <p><span class="label">🏠 Cabaña:</span> <span class="value">' . htmlspecialchars($data['cabin_name']) . '</span></p>
                    <p><span class="label">📅 Check-in:</span> <span class="value">' . date('d/m/Y', strtotime($data['check_in'])) . '</span></p>
                    <p><span class="label">📅 Check-out:</span> <span class="value">' . date('d/m/Y', strtotime($data['check_out'])) . '</span></p>
                    <p><span class="label">👤 Personas:</span> <span class="value">' . $data['people'] . '</span></p>
                </div>
                <p><strong>¿Qué sigue?</strong></p>
                <p>En las próximas <strong>24 horas</strong> nos pondremos en contacto contigo para confirmar la disponibilidad y coordinar los detalles de tu estadía.</p>
                <hr class="divider">
                <div class="btn-container">
                    <a href="https://cabanaslaslengas.cl" class="btn">🌐 Visitar Sitio Web</a>
                </div>
                <p style="text-align: center; font-size: 13px; color: #999; margin-top: 15px;">Si tienes alguna pregunta, no dudes en contactarnos.</p>
            </div>
            <div class="footer">
                <p class="brand"><span>Cabañas</span> Las Lengas</p>
                <p>Gabriel Silva (Camino 1) 132, Puerto Natales</p>
                <p>📞 (+56) 9 9349 9820 | ✉️ reservaciones@cabanaslaslengas.cl</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/Caba%C3%B1aslaslengas-102356472311320" target="_blank">📘</a>
                    <a href="https://www.instagram.com/cabanaslaslengas/" target="_blank">📸</a>
                </div>
                <p class="disclaimer">Este es un mensaje automático, por favor no responder a este correo.</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}

// ============================================================
// EMAIL CLIENTE - INGLÉS
// ============================================================
function getConfirmationEmailClientEn($data) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reservation Confirmation</title>
        <style>
            body, html { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f7fa; }
            .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
            .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px 30px 25px; text-align: center; border-bottom: 4px solid #f8c146; }
            .header img { max-height: 60px; margin-bottom: 10px; }
            .header h1 { color: #f8c146; font-size: 26px; margin: 10px 0 0; font-weight: 700; letter-spacing: 1px; }
            .header .subtitle { color: rgba(255,255,255,0.7); font-size: 14px; margin: 5px 0 0; }
            .content { padding: 30px; }
            .content p { color: #444; line-height: 1.7; font-size: 15px; margin: 0 0 15px; }
            .content .greeting { font-size: 17px; font-weight: 600; color: #1a1a2e; }
            .content .greeting strong { color: #f8c146; }
            .reservation-badge { display: inline-block; background: #1a1a2e; color: #f8c146; padding: 6px 18px; border-radius: 50px; font-size: 14px; font-weight: 600; letter-spacing: 0.5px; margin: 10px 0 15px; }
            .info-card { background: #f8f9fa; border-radius: 12px; padding: 20px 25px; margin: 20px 0; border-left: 4px solid #f8c146; }
            .info-card p { margin: 8px 0; font-size: 15px; color: #333; }
            .info-card .label { font-weight: 600; color: #1a1a2e; display: inline-block; min-width: 100px; }
            .info-card .value { color: #555; }
            .btn { display: inline-block; background: #f8c146; color: #1a1a2e !important; text-decoration: none !important; padding: 14px 35px; border-radius: 50px; font-weight: 700; font-size: 16px; transition: all 0.3s ease; border: none; cursor: pointer; margin: 15px 0 5px; }
            .btn:hover { background: #e6b03a; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(248,193,70,0.3); }
            .btn-container { text-align: center; }
            .divider { border: none; border-top: 2px solid #eee; margin: 25px 0; }
            .footer { background: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #eee; }
            .footer p { margin: 5px 0; font-size: 13px; color: #888; line-height: 1.6; }
            .footer .brand { font-weight: 700; color: #1a1a2e; }
            .footer .brand span { color: #f8c146; }
            .footer .social-links { margin: 10px 0 0; }
            .footer .social-links a { display: inline-block; margin: 0 6px; color: #888; text-decoration: none; font-size: 18px; transition: color 0.3s; }
            .footer .social-links a:hover { color: #f8c146; }
            .footer .disclaimer { font-size: 11px; color: #aaa; margin-top: 12px; }
            @media (max-width: 480px) {
                .email-container { margin: 10px; border-radius: 12px; }
                .header { padding: 20px; }
                .header h1 { font-size: 22px; }
                .content { padding: 20px; }
                .info-card { padding: 15px; }
                .info-card .label { min-width: 80px; font-size: 13px; }
                .btn { padding: 12px 25px; font-size: 14px; }
                .footer { padding: 20px; }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <img src="https://cabanaslaslengas.cl/assets/img/logolengas.png" alt="Cabañas Las Lengas">
                <h1>Thank you for your reservation!</h1>
                <p class="subtitle">We have received your request successfully</p>
            </div>
            <div class="content">
                <p class="greeting">Hello <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
                <p>We are glad you chose <strong>Cabañas Las Lengas</strong> for your stay in Chilean Patagonia. Your reservation request has been received and our team will review it shortly.</p>
                <div style="text-align: center;">
                    <span class="reservation-badge">🔑 Reservation #' . str_pad($data['reservation_id'], 6, '0', STR_PAD_LEFT) . '</span>
                </div>
                <div class="info-card">
                    <p><span class="label">🏠 Cabin:</span> <span class="value">' . htmlspecialchars($data['cabin_name']) . '</span></p>
                    <p><span class="label">📅 Check-in:</span> <span class="value">' . date('m/d/Y', strtotime($data['check_in'])) . '</span></p>
                    <p><span class="label">📅 Check-out:</span> <span class="value">' . date('m/d/Y', strtotime($data['check_out'])) . '</span></p>
                    <p><span class="label">👤 People:</span> <span class="value">' . $data['people'] . '</span></p>
                </div>
                <p><strong>What\'s next?</strong></p>
                <p>Within the next <strong>24 hours</strong> we will contact you to confirm availability and coordinate the details of your stay.</p>
                <hr class="divider">
                <div class="btn-container">
                    <a href="https://cabanaslaslengas.cl" class="btn">🌐 Visit Website</a>
                </div>
                <p style="text-align: center; font-size: 13px; color: #999; margin-top: 15px;">If you have any questions, please do not hesitate to contact us.</p>
            </div>
            <div class="footer">
                <p class="brand"><span>Cabañas</span> Las Lengas</p>
                <p>Gabriel Silva (Camino 1) 132, Puerto Natales</p>
                <p>📞 (+56) 9 9349 9820 | ✉️ reservaciones@cabanaslaslengas.cl</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/Caba%C3%B1aslaslengas-102356472311320" target="_blank">📘</a>
                    <a href="https://www.instagram.com/cabanaslaslengas/" target="_blank">📸</a>
                </div>
                <p class="disclaimer">This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}

// ============================================================
// EMAIL NOTIFICACIÓN ADMIN (SIEMPRE EN ESPAÑOL)
// ============================================================
function getNotificationEmailAdmin($data) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nueva Reserva</title>
        <style>
            body, html { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f7fa; }
            .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
            .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 25px 30px; text-align: center; border-bottom: 4px solid #f8c146; }
            .header h1 { color: #f8c146; font-size: 24px; margin: 0; font-weight: 700; }
            .header .subtitle { color: rgba(255,255,255,0.7); font-size: 14px; margin: 5px 0 0; }
            .content { padding: 30px; }
            .content p { color: #444; line-height: 1.7; font-size: 15px; }
            .alert-badge { display: inline-block; background: #dc3545; color: #fff; padding: 6px 18px; border-radius: 50px; font-size: 14px; font-weight: 600; }
            .info-card { background: #f8f9fa; border-radius: 12px; padding: 20px 25px; margin: 20px 0; border-left: 4px solid #dc3545; }
            .info-card p { margin: 8px 0; font-size: 15px; color: #333; }
            .info-card .label { font-weight: 600; color: #1a1a2e; display: inline-block; min-width: 100px; }
            .info-card .value { color: #555; }
            .btn { display: inline-block; background: #f8c146; color: #1a1a2e !important; text-decoration: none !important; padding: 14px 35px; border-radius: 50px; font-weight: 700; font-size: 16px; transition: all 0.3s ease; }
            .btn:hover { background: #e6b03a; transform: translateY(-2px); }
            .btn-container { text-align: center; margin: 20px 0 10px; }
            .divider { border: none; border-top: 2px solid #eee; margin: 25px 0; }
            .footer { background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #eee; }
            .footer p { margin: 5px 0; font-size: 13px; color: #888; }
            .footer .brand { font-weight: 700; color: #1a1a2e; }
            .footer .brand span { color: #f8c146; }
            @media (max-width: 480px) {
                .email-container { margin: 10px; }
                .header { padding: 20px; }
                .header h1 { font-size: 20px; }
                .content { padding: 20px; }
                .info-card { padding: 15px; }
                .info-card .label { min-width: 80px; font-size: 13px; }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <h1>🔔 Nueva Reserva</h1>
                <p class="subtitle">Se ha recibido una nueva solicitud de reserva</p>
            </div>
            <div class="content">
                <div style="text-align: center; margin-bottom: 15px;">
                    <span class="alert-badge">Reserva #' . str_pad($data['reservation_id'], 6, '0', STR_PAD_LEFT) . '</span>
                </div>
                <div class="info-card">
                    <p><span class="label">👤 Cliente:</span> <span class="value">' . htmlspecialchars($data['name']) . '</span></p>
                    <p><span class="label">📧 Email:</span> <span class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '" style="color: #f8c146; text-decoration: none;">' . htmlspecialchars($data['email']) . '</a></span></p>
                    <p><span class="label">📱 Teléfono:</span> <span class="value">' . htmlspecialchars($data['phone']) . '</span></p>
                    <p><span class="label">🏠 Cabaña:</span> <span class="value">' . htmlspecialchars($data['cabin_name']) . '</span></p>
                    <p><span class="label">📅 Check-in:</span> <span class="value">' . date('d/m/Y', strtotime($data['check_in'])) . '</span></p>
                    <p><span class="label">📅 Check-out:</span> <span class="value">' . date('d/m/Y', strtotime($data['check_out'])) . '</span></p>
                    <p><span class="label">👤 Personas:</span> <span class="value">' . $data['people'] . '</span></p>
                    <p><span class="label">💬 Mensaje:</span> <span class="value">' . htmlspecialchars($data['message'] ?: 'N/A') . '</span></p>
                    <p><span class="label">🌐 Idioma:</span> <span class="value">' . strtoupper($data['lang']) . '</span></p>
                </div>
                <hr class="divider">
                <div class="btn-container">
                    <a href="https://cabanaslaslengas.cl/admin/reservations.php" class="btn">📋 Ver en Admin</a>
                </div>
                <p style="text-align: center; font-size: 13px; color: #999;">Esta reserva está pendiente de confirmación.</p>
            </div>
            <div class="footer">
                <p class="brand"><span>Cabañas</span> Las Lengas</p>
                <p>Sistema de Reservas</p>
                <p style="font-size: 11px; color: #aaa;">Este es un mensaje automático del sistema.</p>
            </div>
        </div>
    </body>
    </html>';
    return $html;
}
?>