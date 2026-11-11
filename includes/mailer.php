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
     * @return array{driver:string,host:string,port:int,username:string,password:string,encryption:string,from_address:string,from_name:string,admin_address:string}
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
        ];
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

        return $config['host'] !== ''
            && $config['from_address'] !== ''
            && (bool) filter_var($config['from_address'], FILTER_VALIDATE_EMAIL);
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
     */
    function send_site_mail(
        string $to,
        string $subject,
        string $html,
        ?string $alt_text = null,
        ?string &$error = null,
        bool $debug = false
    ): bool {
        $error = null;
        $config = site_mail_config();

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $error = 'Destinatario inválido: ' . $to;
            error_log('[mail] ' . $error);
            return false;
        }

        // ------------------------------------------------------------
        // Modo mail() (solo si se configura MAIL_DRIVER=mail)
        // ------------------------------------------------------------
        if ($config['driver'] === 'mail') {
            $headers = 'From: ' . $config['from_name'] . ' <' . $config['from_address'] . ">\r\n";
            $headers .= 'Reply-To: ' . $config['from_address'] . "\r\n";
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
            $mail->addReplyTo($config['from_address'], $config['from_name']);
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
