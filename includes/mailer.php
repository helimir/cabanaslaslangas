<?php
/**
 * includes/mailer.php
 * -----------------------------------------------------------------
 * Envío de correos del sitio mediante PHPMailer y la configuración
 * SMTP definida en .env (MAIL_*).
 *
 * En un VPS (aaPanel/Hetzner) la función mail() de PHP normalmente no
 * tiene un servidor de correo local, además de que el puerto 25 suele
 * estar bloqueado. Por eso el envío debe ser por SMTP autenticado.
 * -----------------------------------------------------------------
 */

require_once __DIR__ . '/env.php';

// Cargar .env si aún no está cargado (por ejemplo en scripts CLI)
if (function_exists('dsh_env_load')) {
    dsh_env_load(dirname(__DIR__) . '/.env');
}

require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (!function_exists('site_mail_config')) {

    /**
     * Configuración de correo leída desde .env.
     *
     * @return array{driver:string,host:string,port:int,username:string,password:string,encryption:string,from_address:string,from_name:string,admin_address:string,sendgrid_api_key:string,api_url:string}
     */
    function site_mail_config(): array
    {
        return [
            'driver'       => strtolower((string) env('MAIL_DRIVER', 'smtp')),
            'host'         => trim((string) env('MAIL_HOST', '')),
            'port'         => (int) env_int('MAIL_PORT', 587),
            'username'     => (string) env('MAIL_USERNAME', ''),
            'password'     => (string) env('MAIL_PASSWORD', ''),
            'encryption'   => strtolower((string) env('MAIL_ENCRYPTION', 'tls')),
            'from_address' => trim((string) env('MAIL_FROM_ADDRESS', 'reservaciones@cabanaslaslengas.cl')),
            'from_name'    => (string) env('MAIL_FROM_NAME', 'Cabañas Las Lengas'),
            'admin_address' => trim((string) env('MAIL_ADMIN_ADDRESS', 'reservaciones@cabanaslaslengas.cl')),
            'sendgrid_api_key' => trim((string) env('SENDGRID_API_KEY', '')),
            'api_url'      => (string) env('SENDGRID_API_URL', 'https://api.sendgrid.com/v3/mail/send'),
        ];
    }

    /**
     * Indica si el driver configurado es la API de SendGrid.
     */
    function site_mail_is_sendgrid(string $driver = ''): bool
    {
        if ($driver === '') {
            $driver = site_mail_config()['driver'];
        }

        return in_array(strtolower($driver), ['sendgrid', 'sendgrid_api', 'api'], true);
    }

    /**
     * Indica si hay configuración mínima para enviar correo.
     */
    function site_mail_is_configured(): bool
    {
        $config = site_mail_config();

        if ($config['driver'] === 'mail') {
            return true;
        }

        if (site_mail_is_sendgrid($config['driver'])) {
            return $config['sendgrid_api_key'] !== ''
                && $config['from_address'] !== ''
                && (bool) filter_var($config['from_address'], FILTER_VALIDATE_EMAIL);
        }

        return $config['host'] !== ''
            && $config['from_address'] !== ''
            && (bool) filter_var($config['from_address'], FILTER_VALIDATE_EMAIL);
    }

    /**
     * Envía un correo mediante la API v3 de SendGrid (HTTPS, puerto 443).
     * No usa puertos SMTP, por lo que funciona aunque el proveedor los bloquee.
     */
    function send_sendgrid_api(
        string $to,
        string $subject,
        string $html,
        ?string $alt_text,
        ?string &$error,
        bool $debug,
        array $config,
        ?string $reply_to = null
    ): bool {
        $error = null;

        if ($config['sendgrid_api_key'] === '') {
            $error = 'Falta SENDGRID_API_KEY en .env';
            error_log('[mail] ' . $error);
            return false;
        }

        if (!function_exists('curl_init')) {
            $error = 'La extensión cURL de PHP no está disponible (necesaria para la API de SendGrid)';
            error_log('[mail] ' . $error);
            return false;
        }

        $reply_email = ($reply_to !== null && filter_var($reply_to, FILTER_VALIDATE_EMAIL))
            ? $reply_to
            : $config['from_address'];

        $payload = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $to],
                    ],
                ],
            ],
            'from' => [
                'email' => $config['from_address'],
                'name'  => $config['from_name'],
            ],
            'reply_to' => ($reply_email === $config['from_address'])
                ? ['email' => $reply_email, 'name' => $config['from_name']]
                : ['email' => $reply_email],
            'subject' => $subject,
            'content' => [
                [
                    'type'  => 'text/plain',
                    'value' => $alt_text !== null && $alt_text !== '' ? $alt_text : strip_tags($html),
                ],
                [
                    'type'  => 'text/html',
                    'value' => $html,
                ],
            ],
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            $error = 'No se pudo generar el JSON para SendGrid';
            error_log('[mail] ' . $error);
            return false;
        }

        $ch = curl_init($config['api_url']);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $config['sendgrid_api_key'],
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_USERAGENT      => 'CabanasLasLengas/1.0',
        ]);

        $response = curl_exec($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curl_error !== '') {
            $error = 'Error de conexión con SendGrid: ' . ($curl_error !== '' ? $curl_error : 'sin respuesta');
            error_log('[mail] ' . $error);
            return false;
        }

        if ($debug) {
            echo 'SendGrid API -> HTTP ' . $http_code . PHP_EOL;
            if ($response !== '') {
                echo 'Respuesta: ' . substr((string) $response, 0, 800) . PHP_EOL;
            }
        }

        if ($http_code === 202 || $http_code === 200) {
            return true;
        }

        // Extraer el mensaje de error que devuelve SendGrid.
        $detalle = '';
        $decoded = json_decode((string) $response, true);
        if (is_array($decoded) && !empty($decoded['errors'])) {
            $partes = [];
            foreach ($decoded['errors'] as $item) {
                if (is_array($item) && isset($item['message'])) {
                    $partes[] = (string) $item['message'];
                } else {
                    $partes[] = (string) json_encode($item);
                }
            }
            $detalle = implode(' | ', $partes);
        }

        if ($detalle === '') {
            $detalle = substr((string) $response, 0, 500);
        }

        $error = 'SendGrid HTTP ' . $http_code . ': ' . $detalle;
        error_log('[mail] Error al enviar a ' . $to . ': ' . $error);

        return false;
    }

    /**
     * Envía un correo HTML.
     *
     * @param string      $to       Destinatario
     * @param string      $subject  Asunto
     * @param string      $html     Cuerpo HTML
     * @param string|null $alt_text Texto alternativo (opcional)
     * @param string|null $error    Se completa con el error si falla
     * @param bool        $debug    Muestra la conversación SMTP (solo CLI/pruebas)
     * @param string|null $reply_to Correo para "Responder a" (ej: email del cliente)
     */
    function send_site_mail(
        string $to,
        string $subject,
        string $html,
        ?string $alt_text = null,
        ?string &$error = null,
        bool $debug = false,
        ?string $reply_to = null
    ): bool {
        $error = null;
        $config = site_mail_config();

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $error = 'Destinatario inválido: ' . $to;
            error_log('[mail] ' . $error);
            return false;
        }

        // ------------------------------------------------------------
        // Modo SendGrid API (recomendado en VPS/Hetzner: HTTPS puerto 443)
        // ------------------------------------------------------------
        if (site_mail_is_sendgrid($config['driver'])) {
            return send_sendgrid_api(
                $to,
                $subject,
                $html,
                ($alt_text !== null && $alt_text !== '') ? $alt_text : null,
                $error,
                $debug,
                $config,
                $reply_to
            );
        }

        // ------------------------------------------------------------
        // Modo mail() (solo si se configura MAIL_DRIVER=mail)
        // ------------------------------------------------------------
        if ($config['driver'] === 'mail') {
            $reply_address = ($reply_to !== null && filter_var($reply_to, FILTER_VALIDATE_EMAIL))
                ? $reply_to
                : $config['from_address'];

            $headers = 'From: ' . $config['from_name'] . ' <' . $config['from_address'] . ">\r\n";
            $headers .= 'Reply-To: ' . $reply_address . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $ok = @mail($to, $subject, $html, $headers);

            if (!$ok) {
                $error = 'mail() devolvió false (el servidor no tiene MTA configurado)';
                error_log('[mail] ' . $error . ' | to=' . $to . ' | subject=' . $subject);
            }

            return (bool) $ok;
        }

        // ------------------------------------------------------------
        // Modo SMTP (recomendado en VPS/aaPanel)
        // ------------------------------------------------------------
        if (!site_mail_is_configured()) {
            $error = 'Configuración SMTP incompleta: revisa MAIL_HOST y MAIL_FROM_ADDRESS en .env';
            error_log('[mail] ' . $error);
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            if ($debug) {
                $mail->SMTPDebug   = SMTP::DEBUG_SERVER;
                $mail->Debugoutput = 'echo';
            } else {
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
            }

            $mail->isSMTP();
            $mail->Host    = $config['host'];
            $mail->Port    = $config['port'];
            $mail->Timeout = 20;
            $mail->CharSet = 'UTF-8';

            $mail->SMTPAuth = $config['username'] !== '';
            if ($mail->SMTPAuth) {
                $mail->Username = $config['username'];
                $mail->Password = $config['password'];
            }

            $encryption = $config['encryption'];
            if ($encryption === 'ssl' || $encryption === 'smtps') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $encryption === 'starttls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure  = '';
                $mail->SMTPAutoTLS = false;
            }

            $mail->setFrom($config['from_address'], $config['from_name']);

            if ($reply_to !== null && filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($reply_to);
            } else {
                $mail->addReplyTo($config['from_address'], $config['from_name']);
            }

            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $alt_text !== null && $alt_text !== '' ? $alt_text : strip_tags($html);

            $mail->send();

            return true;
        } catch (Throwable $e) {
            $error = $e->getMessage();

            if (!empty($mail->ErrorInfo)) {
                $error .= ' | SMTP: ' . $mail->ErrorInfo;
            }

            error_log('[mail] Error al enviar a ' . $to . ': ' . $error);
            return false;
        }
    }
}
