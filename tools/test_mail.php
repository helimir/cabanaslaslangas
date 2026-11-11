<?php
/**
 * tools/test_mail.php
 * -----------------------------------------------------------------
 * Prueba de envío de correo del sitio (SMTP configurado en .env).
 *
 * Uso (desde la raíz del proyecto, por terminal/SSH):
 *
 *   php tools/test_mail.php correo@destino.cl
 *
 * Si no se indica destinatario, usa MAIL_ADMIN_ADDRESS del .env.
 * Muestra la conversación SMTP completa para diagnosticar errores.
 *
 * Solo funciona por CLI (no accesible desde el navegador).
 * -----------------------------------------------------------------
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/env.php';
dsh_env_load(dirname(__DIR__) . '/.env');
require_once __DIR__ . '/../includes/mailer.php';

$config = site_mail_config();

$to = $argv[1] ?? $config['admin_address'];

echo "============================================================\n";
echo " Prueba de correo — Cabañas Las Lengas\n";
echo "============================================================\n";
echo 'Driver        : ' . $config['driver'] . PHP_EOL;
echo 'Host SMTP     : ' . ($config['host'] !== '' ? $config['host'] : '(vacío)') . PHP_EOL;
echo 'Puerto        : ' . $config['port'] . PHP_EOL;
echo 'Cifrado       : ' . ($config['encryption'] !== '' ? $config['encryption'] : '(ninguno)') . PHP_EOL;
echo 'Usuario       : ' . ($config['username'] !== '' ? $config['username'] : '(sin autenticación)') . PHP_EOL;
echo 'Contraseña    : ' . ($config['password'] !== '' ? '(configurada)' : '(vacía)') . PHP_EOL;
echo 'Remitente     : ' . $config['from_name'] . ' <' . $config['from_address'] . '>' . PHP_EOL;
echo 'Destinatario  : ' . $to . PHP_EOL;
echo 'mail() PHP    : ' . (function_exists('mail') ? 'disponible' : 'NO disponible') . PHP_EOL;
echo 'SMTP completo : ' . (site_mail_is_configured() ? 'sí' : 'NO (revisa MAIL_HOST / MAIL_FROM_ADDRESS)') . PHP_EOL;
echo "------------------------------------------------------------\n\n";

if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "ERROR: destinatario inválido.\n");
    fwrite(STDERR, "Uso: php tools/test_mail.php correo@destino.cl\n");
    exit(1);
}

$html = '<h2>Prueba de correo</h2>'
    . '<p>Si estás leyendo este mensaje, el envío SMTP del sitio funciona correctamente.</p>'
    . '<p><strong>Servidor:</strong> ' . htmlspecialchars((string) ($_SERVER['HTTP_HOST'] ?? gethostname() ?: 'VPS')) . '</p>'
    . '<p><strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '</p>'
    . '<p><em>Cabañas Las Lengas</em></p>';

$error = null;

echo "Enviando correo de prueba...\n\n";

$ok = send_site_mail(
    $to,
    'Prueba de correo — Cabañas Las Lengas',
    $html,
    null,
    $error,
    true // mostrar conversación SMTP
);

echo "\n------------------------------------------------------------\n";

if ($ok) {
    echo "✅ CORREO ENVIADO correctamente a: " . $to . PHP_EOL;
    echo "Revisa la bandeja de entrada y también la carpeta de SPAM.\n";
    echo "============================================================\n";
    exit(0);
}

echo "❌ NO SE PUDO ENVIAR EL CORREO\n";
echo "Error: " . ($error ?: 'desconocido') . PHP_EOL;
echo "\nRevisa:\n";
echo "  1. MAIL_HOST / MAIL_PORT / MAIL_USERNAME / MAIL_PASSWORD / MAIL_ENCRYPTION en .env\n";
echo "  2. Que el servidor permita salida a ese puerto (587 o 465).\n";
echo "  3. Que el proveedor no bloquee el envío (Hetzner bloquea el puerto 25).\n";
echo "============================================================\n";
exit(1);
