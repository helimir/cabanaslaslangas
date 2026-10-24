<?php
require_once '../includes/config.php';
require_once 'mailer_config.php';

// Verificar reCAPTCHA
$recaptcha_secret = env('RECAPTCHA_SECRET_KEY', ''); // definido en .env
$recaptcha_response = $_POST['recaptcha_response'] ?? '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$recaptcha_result = curl_exec($ch);
curl_close($ch);
$recaptcha_data = json_decode($recaptcha_result, true);

if (!$recaptcha_data['success'] || $recaptcha_data['score'] < 0.5) {
    header('Location: ../index.php?send=false#book-a-table');
    exit;
}

// Recoger datos del formulario
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$people = (int)($_POST['people'] ?? 0);
$check_in = $_POST['date1'] ?? '';
$check_out = $_POST['date2'] ?? '';
$message = trim($_POST['message'] ?? '');
$cabin_name = trim($_POST['cab'] ?? '');

// Validaciones básicas
$errors = [];
if (empty($name)) $errors[] = 'Nombre requerido';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
if (empty($phone)) $errors[] = 'Teléfono requerido';
if (empty($check_in) || empty($check_out)) $errors[] = 'Fechas requeridas';
if ($check_in >= $check_out) $errors[] = 'Check-out debe ser mayor a Check-in';

if (!empty($errors)) {
    header('Location: ../index.php?send=false#book-a-table');
    exit;
}

// Guardar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, cabin_name, check_in, check_out, people, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$name, $email, $phone, $cabin_name, $check_in, $check_out, $people, $message]);
    $reservation_id = $pdo->lastInsertId();
    
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'cabin_name' => $cabin_name,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'people' => $people,
        'message' => $message
    ];
    
    // 1. Enviar email de confirmación al cliente
    $client_subject = 'Confirmación de Reserva - Cabañas Las Lengas';
    $client_body = getConfirmationEmailClient($data);
    sendEmail($email, $client_subject, $client_body);
    
    // 2. Enviar notificación al admin
    $admin_subject = 'Nueva Reserva - Cabañas Las Lengas';
    $admin_body = getNotificationEmailAdmin($data);
    sendEmail('cabanaslaslengas@gmail.com', $admin_subject, $admin_body);
    // Si quieres enviar a más admins, agrega más emails aquí
    
    header('Location: ../index.php?send=true#book-a-table');
    
} catch (PDOException $e) {
    error_log("Error al guardar reserva: " . $e->getMessage());
    header('Location: ../index.php?send=false#book-a-table');
}