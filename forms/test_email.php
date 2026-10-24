<?php
// forms/test_email.php
require_once('PHPMailer/Exception.php');
require_once('PHPMailer/PHPMailer.php');
require_once('PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once 'mailer_config.php';

// Configurar constantes si no están definidas
if (!defined('SITE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
    define('SITE_URL', $protocol . $host . $path);
}
if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', SITE_URL . 'admin/');
}

$test_data = [
    'name' => 'Cliente Test',
    'email' => 'helimirlopez@gmail.com',
    'phone' => '+56 9 1234 5678',
    'cabin_name' => 'Cabaña Coihue',
    'check_in' => date('Y-m-d', strtotime('+7 days')),
    'check_out' => date('Y-m-d', strtotime('+9 days')),
    'people' => 2,
    'message' => 'Mensaje de prueba',
    'reservation_id' => 999
];

echo "<h1>Prueba de Envío de Email</h1>";

// Probar email al cliente
echo "<h2>Email al Cliente</h2>";
$client_subject = 'Prueba - Confirmación de Reserva';
$client_body = getConfirmationEmailClient($test_data);
$result_client = sendEmail('helimirlopez@gmail.com', $client_subject, $client_body);
echo $result_client ? '<p style="color:green;">✅ Email enviado correctamente</p>' : '<p style="color:red;">❌ Error al enviar email</p>';

// Probar email al admin
echo "<h2>Email al Admin</h2>";
$admin_subject = 'Prueba - Nueva Reserva';
$admin_body = getNotificationEmailAdmin($test_data);
$result_admin = sendEmail('cabanaslaslengas@gmail.com', $admin_subject, $admin_body);
echo $result_admin ? '<p style="color:green;">✅ Email enviado correctamente</p>' : '<p style="color:red;">❌ Error al enviar email</p>';

// Mostrar errores de PHPMailer
$mail = new PHPMailer(true);
echo "<h2>Configuración SMTP</h2>";
echo "<pre>";
echo "Host: mail.cabanaslaslengas.cl\n";
echo "Username: reservaciones@cabanaslaslengas.cl\n";
echo "Port: 587\n";
echo "Secure: STARTTLS\n";
echo "</pre>";
?>