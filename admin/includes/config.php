<?php
/**
 * admin/includes/config.php
 * -----------------------------------------------------------------
 * Configuración central del sitio.
 *
 * Las credenciales y datos de entorno se leen desde el archivo .env
 * ubicado en la raíz del proyecto (ver .env.example).
 * -----------------------------------------------------------------
 */

require_once __DIR__ . '/../../includes/env.php';
require_once __DIR__ . '/../../includes/site_url.php';

// Raíz del proyecto = dos niveles arriba de admin/includes
dsh_env_load(dirname(__DIR__, 2) . '/.env');

// ------------------------------------------------------------
// Entorno
// ------------------------------------------------------------
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env_bool('APP_DEBUG', false));
define('APP_TIMEZONE', env('APP_TIMEZONE', 'America/Santiago'));

date_default_timezone_set(APP_TIMEZONE);

// La sesión es necesaria en todo el panel (auth.php la usa en cada página).
// En CLI (cron / scripts) no hay sesión que iniciar.
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
// En desarrollo (APP_ENV=local) la URL se autodetecta desde la petición
// para que las imágenes apunten al servidor local. En producción se usa
// el valor de APP_URL definido en el .env.
// ------------------------------------------------------------
if (APP_ENV === 'local' && PHP_SAPI !== 'cli') {
    define('SITE_URL', detect_site_url(dirname(__DIR__, 2)));
} else {
    define('SITE_URL', rtrim(env('APP_URL', 'https://cabanaslaslengas.cl'), '/') . '/');
}
define('ADMIN_URL', SITE_URL . 'admin/');
define('UPLOADS_URL', SITE_URL . 'admin/assets/uploads/');
define('UPLOADS_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOADS_MAX_BYTES', 5 * 1024 * 1024); // 5 MB

// Ruta a PHPMailer (raíz del proyecto)
define('PHPMailer_PATH', __DIR__ . '/../../PHPMailer/');

// ------------------------------------------------------------
// Conexión a la base de datos
// ------------------------------------------------------------
if (DB_NAME === '' || DB_USER === '') {
    http_response_code(500);
    if (APP_DEBUG) {
        die('Configuración incompleta: falta DB_NAME o DB_USER. Copia .env.example como .env y completa los valores.');
    }
    die('Error de configuración del sitio. Contacta al administrador.');
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    if (APP_DEBUG) {
        die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
    }
    die('Error de conexión a la base de datos. Contacta al administrador.');
}

// ------------------------------------------------------------
// Funciones de configuración / sesión
// ------------------------------------------------------------

function getSetting($key)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return null;
    }
}

function isLoggedIn()
{
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function getCurrentUser()
{
    return $_SESSION['admin_username'] ?? 'Admin';
}

function getPendingReservationsCount()
{
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

// ============================================================
// EQUIPAMIENTO (amenities)
// Cada cabaña puede tener un subconjunto del equipamiento general.
// ============================================================

/**
 * Lista completa de equipamiento (por defecto incluye inactivos, útil en el admin).
 */
function getAllAmenities(bool $only_active = false): array
{
    global $pdo;

    $sql = 'SELECT * FROM amenities';
    if ($only_active) {
        $sql .= ' WHERE status = 1';
    }
    $sql .= ' ORDER BY `order` ASC, id ASC';

    try {
        return $pdo->query($sql)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * IDs de equipamiento asignados a una cabaña (incluye inactivos).
 */
function getCabinAmenityIds(int $cabin_id): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT amenity_id FROM cabin_amenities WHERE cabin_id = ?');
        $stmt->execute([$cabin_id]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    } catch (Exception $e) {
        error_log('getCabinAmenityIds: ' . $e->getMessage());
        return [];
    }
}

/**
 * Equipamiento activo asignado a una cabaña (para la página pública).
 * Si aún no existe la tabla de relación devuelve todo el equipamiento activo.
 */
function getCabinAmenities(int $cabin_id): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare(
            'SELECT a.* FROM amenities a
             INNER JOIN cabin_amenities ca ON ca.amenity_id = a.id
             WHERE ca.cabin_id = ? AND a.status = 1
             ORDER BY a.`order` ASC, a.id ASC'
        );
        $stmt->execute([$cabin_id]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('getCabinAmenities: ' . $e->getMessage());
        return getAllAmenities(true);
    }
}

/**
 * Reemplaza el equipamiento asignado a una cabaña.
 */
function saveCabinAmenities(int $cabin_id, array $amenity_ids): bool
{
    global $pdo;

    $ids = [];
    foreach ($amenity_ids as $id) {
        if (is_numeric($id) && (int) $id > 0) {
            $ids[] = (int) $id;
        }
    }
    $ids = array_values(array_unique($ids));

    try {
        $pdo->prepare('DELETE FROM cabin_amenities WHERE cabin_id = ?')->execute([$cabin_id]);

        if (!$ids) {
            return true;
        }

        // Solo IDs que existan realmente.
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id FROM amenities WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $valid = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        $insert = $pdo->prepare('INSERT INTO cabin_amenities (cabin_id, amenity_id) VALUES (?, ?)');
        foreach ($valid as $amenity_id) {
            $insert->execute([$cabin_id, $amenity_id]);
        }

        return true;
    } catch (Exception $e) {
        error_log('saveCabinAmenities: ' . $e->getMessage());
        return false;
    }
}

// ============================================================
// ARCHIVOS FÍSICOS DE CABAÑAS
// Cada cabaña tiene un archivo cabana-{slug}.php en la raíz que
// carga el modelo informativo común (includes/cabin_page.php).
// ============================================================

/**
 * Ruta absoluta de la raíz del proyecto.
 */
function cabinRootPath(): string
{
    return dirname(__DIR__, 2);
}

/**
 * Ruta absoluta del archivo público de una cabaña.
 */
function cabinFilePath(string $slug): string
{
    return cabinRootPath() . '/cabana-' . $slug . '.php';
}

/**
 * Valida que un slug sea seguro para usarse como nombre de archivo/URL.
 */
function isValidCabinSlug(string $slug): bool
{
    return (bool) preg_match('/^[a-z0-9][a-z0-9-]{0,88}[a-z0-9]$|^[a-z0-9]$/', $slug);
}

/**
 * Convierte un texto libre en un slug válido.
 */
function normalizeCabinSlug(string $text): string
{
    $text = trim($text);

    // Transliteración de caracteres frecuentes en español.
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N',
    ]);

    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        if ($converted !== false) {
            $text = $converted;
        }
    }

    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim(preg_replace('/-+/', '-', $text), '-');

    return substr($text, 0, 90);
}

/**
 * Indica si un archivo de cabaña fue generado automáticamente por el panel
 * (para no sobreescribir páginas hechas a mano).
 */
function isAutoGeneratedCabinFile(string $path): bool
{
    if (!is_file($path)) {
        return false;
    }

    $head = (string) file_get_contents($path, false, null, 0, 2048);

    return str_contains($head, 'generado automáticamente por el panel');
}

/**
 * Crea (o repara) el archivo físico cabana-{slug}.php.
 *
 * @param string      $slug   Slug validado de la cabaña.
 * @param string|null $status Resultado: created | exists_custom | invalid_slug | write_error
 * @return bool true si la página queda disponible.
 */
function generateCabinFile(string $slug, ?string &$status = null): bool
{
    $status = 'write_error';

    if (!isValidCabinSlug($slug)) {
        $status = 'invalid_slug';
        error_log('generateCabinFile: slug inválido "' . $slug . '"');
        return false;
    }

    $file_path = cabinFilePath($slug);

    // No sobreescribir una página hecha a mano (páginas antiguas del sitio).
    if (is_file($file_path) && !isAutoGeneratedCabinFile($file_path)) {
        $status = 'exists_custom';
        return true;
    }

    $content = "<?php\n"
        . "/* ============================================================\n"
        . " * Archivo generado automáticamente por el panel de administración.\n"
        . " * Cabaña: {$slug}\n"
        . " * Generado: " . date('Y-m-d H:i:s') . "\n"
        . " * Modelo informativo: includes/cabin_page.php\n"
        . " * Los datos (nombre, precio, fotos, etc.) se leen de la base de datos.\n"
        . " * NO EDITAR A MANO.\n"
        . " * ============================================================ */\n\n"
        . '$slug = ' . var_export($slug, true) . ";\n"
        . "require __DIR__ . '/includes/cabin_page.php';\n";

    $written = @file_put_contents($file_path, $content, LOCK_EX);

    if ($written === false) {
        $status = 'write_error';
        error_log('generateCabinFile: no se pudo escribir ' . $file_path);
        return false;
    }

    @chmod($file_path, 0644);
    $status = 'created';

    return true;
}

/**
 * Elimina el archivo físico cabana-{slug}.php.
 */
function deleteCabinFile(string $slug): bool
{
    if (!isValidCabinSlug($slug)) {
        return false;
    }

    $file_path = cabinFilePath($slug);

    if (is_file($file_path)) {
        return @unlink($file_path);
    }

    return false;
}

/**
 * URL de una imagen de cabaña, con respaldo si el archivo no existe.
 */
function getCabinImageUrl(?string $file, ?string $fallback = null): string
{
    $fallback = $fallback ?? SITE_URL . 'assets/img/logolengas.png';

    if (empty($file)) {
        return $fallback;
    }

    $name = basename($file);

    if (is_file(UPLOADS_PATH . $name)) {
        return UPLOADS_URL . $name;
    }

    if (is_file(cabinRootPath() . '/assets/img/' . $name)) {
        return SITE_URL . 'assets/img/' . $name;
    }

    return $fallback;
}

/**
 * Imagen de portada de una cabaña para tarjetas/listados.
 * Usa image_path (portada) y, si falta, image_main.
 */
function getCabinCover(array $cabin, ?string $fallback = null): string
{
    $fallback = $fallback ?? SITE_URL . 'assets/img/logolengas.png';

    foreach (['image_path', 'image_main'] as $field) {
        if (!empty($cabin[$field])) {
            $url = getCabinImageUrl($cabin[$field], '');
            if ($url !== '') {
                return $url;
            }
        }
    }

    return $fallback;
}
