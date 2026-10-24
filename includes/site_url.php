<?php
/**
 * includes/site_url.php
 * -----------------------------------------------------------------
 * Resolución de la URL base pública del sitio.
 *
 * - En producción (APP_ENV=production) se usa APP_URL del archivo .env.
 * - En desarrollo (APP_ENV=local) se autodetecta a partir de la petición
 *   (host, puerto y subcarpeta), para que las imágenes y enlaces apunten
 *   al servidor local aunque APP_URL tenga el dominio de producción.
 * -----------------------------------------------------------------
 */

if (!function_exists('detect_site_url')) {

    /**
     * Detecta la URL base a partir de la petición HTTP actual.
     */
    function detect_site_url(string $project_root): string
    {
        $https = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? '') == 443)
            || (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');

        $scheme = $https ? 'https' : 'http';

        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

        // Subcarpeta del proyecto respecto al DocumentRoot (ej: /cabanaslaslengas.cl)
        $base = '';

        $doc_root = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $root = realpath($project_root) ?: $project_root;

        if ($doc_root) {
            $doc_root = rtrim(str_replace('\\', '/', $doc_root), '/');
            $root_norm = str_replace('\\', '/', $root);

            if ($root_norm === $doc_root) {
                $base = '';
            } elseif (str_starts_with($root_norm, $doc_root)) {
                $base = rtrim(substr($root_norm, strlen($doc_root)), '/');
            }
        }

        // Respaldo: deducir la subcarpeta desde el script en ejecución.
        if ($base === '') {
            $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');

            if (preg_match('#^(.*?)/(?:admin|includes)/#', $script, $m)) {
                $base = rtrim($m[1], '/');
            } elseif (preg_match('#^(.*)/[^/]+\.php$#', $script, $m)) {
                $base = rtrim($m[1], '/');
            }
        }

        return $scheme . '://' . $host . $base . '/';
    }
}
