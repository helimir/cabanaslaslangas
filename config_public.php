<?php
/**
 * config_public.php
 * -----------------------------------------------------------------
 * Configuración para las páginas públicas que usan el modelo
 * includes/cabin_page.php (cabañas generadas desde el panel).
 *
 * Lee las mismas variables .env que admin/includes/config.php.
 * -----------------------------------------------------------------
 */

require_once __DIR__ . '/includes/env.php';

dsh_env_load(__DIR__ . '/.env');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------
// Entorno
// ------------------------------------------------------------
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env_bool('APP_DEBUG', false));
define('APP_TIMEZONE', env('APP_TIMEZONE', 'America/Santiago'));

date_default_timezone_set(APP_TIMEZONE);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

// ------------------------------------------------------------
// Base de datos
// ------------------------------------------------------------
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env_int('DB_PORT', 3306));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));

// ------------------------------------------------------------
// Rutas y URLs
// ------------------------------------------------------------
define('SITE_URL', rtrim(env('APP_URL', 'https://cabanaslaslengas.cl'), '/') . '/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('UPLOADS_URL', SITE_URL . 'admin/assets/uploads/');
define('UPLOADS_PATH', __DIR__ . '/admin/assets/uploads/');
define('UPLOADS_MAX_BYTES', 5 * 1024 * 1024);

try {
    if (DB_NAME === '' || DB_USER === '') {
        throw new RuntimeException('Falta configuración de base de datos en .env');
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (Throwable $e) {
    error_log('config_public: ' . $e->getMessage());
    http_response_code(500);
    die(APP_DEBUG ? 'Error de conexión: ' . htmlspecialchars($e->getMessage()) : 'Error de conexión');
}

// ------------------------------------------------------------
// Idioma
// ------------------------------------------------------------
$lang = $_GET['lan'] ?? $_SESSION['lan'] ?? 'es';
if (!in_array($lang, ['es', 'en'], true)) {
    $lang = 'es';
}
$_SESSION['lan'] = $lang;

// ------------------------------------------------------------
// Funciones públicas
// ------------------------------------------------------------

function getSetting($key)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        return $stmt->fetchColumn() ?: '';
    } catch (Exception $e) {
        return '';
    }
}

function getCabins()
{
    global $pdo;

    $stmt = $pdo->query('SELECT * FROM cabins WHERE status = 1 ORDER BY id ASC');
    return $stmt->fetchAll();
}

function getCabinBySlug($slug)
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM cabins WHERE slug = ? AND status = 1');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getGalleryByCategory($category)
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT * FROM gallery_images WHERE category = ? AND status = 1 ORDER BY `order` ASC, id ASC');
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

function getAmenities()
{
    global $pdo;

    $stmt = $pdo->query('SELECT * FROM amenities WHERE status = 1 ORDER BY `order` ASC');
    return $stmt->fetchAll();
}

function splitPipe($str)
{
    if (empty($str)) {
        return [];
    }

    return array_filter(array_map('trim', explode('|', $str)));
}

/**
 * URL de una imagen subida o del assets/img del proyecto.
 */
function publicImageUrl(?string $image_name, ?string $fallback = null): string
{
    $fallback = $fallback ?? SITE_URL . 'assets/img/logolengas.png';

    if (empty($image_name)) {
        return $fallback;
    }

    $name = basename($image_name);

    if (is_file(UPLOADS_PATH . $name)) {
        return UPLOADS_URL . $name;
    }

    $paths = ['assets/img/', 'assets/img/gallery/', 'assets/img/cabana/', 'assets/img/coihue/', 'assets/img/cipres/', 'assets/img/lenga/'];
    foreach ($paths as $path) {
        if (is_file(__DIR__ . '/' . $path . $name)) {
            return SITE_URL . $path . $name;
        }
    }

    return $fallback;
}

/**
 * Compatibilidad con el modelo includes/cabin_page.php.
 */
function getCabinImage($image_name)
{
    return publicImageUrl($image_name);
}

function getGalleryImage($image_name)
{
    return publicImageUrl($image_name);
}
