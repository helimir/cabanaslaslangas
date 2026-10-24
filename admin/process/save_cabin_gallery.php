<?php
/**
 * admin/process/save_cabin_gallery.php
 * -----------------------------------------------------------------
 * Sube una o varias imágenes a la galería interna de una cabaña.
 * La categoría en gallery_images es el slug de la cabaña.
 * -----------------------------------------------------------------
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

/**
 * Vuelve a la galería de la cabaña.
 */
function cabin_gallery_redirect(int $cabin_id, string $query = ''): void
{
    header('Location: ../cabin_gallery.php?cabin=' . $cabin_id . ($query !== '' ? '&' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cabins.php');
    exit;
}

$cabin_id = isset($_POST['cabin_id']) ? (int) $_POST['cabin_id'] : 0;

$stmt = $pdo->prepare('SELECT * FROM cabins WHERE id = ?');
$stmt->execute([$cabin_id]);
$cabin = $stmt->fetch();

if (!$cabin) {
    header('Location: ../cabins.php?error=not_found');
    exit;
}

$slug = $cabin['slug'];

if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
    if (isset($_FILES['images']) && ($_FILES['images']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        // Un solo archivo: normalizar a arrays.
        $_FILES['images'] = [
            'name' => [$_FILES['images']['name']],
            'type' => [$_FILES['images']['type']],
            'tmp_name' => [$_FILES['images']['tmp_name']],
            'error' => [$_FILES['images']['error']],
            'size' => [$_FILES['images']['size']],
        ];
    } else {
        cabin_gallery_redirect($cabin_id, 'msg=nofiles');
    }
}

if (!is_dir(UPLOADS_PATH) && !@mkdir(UPLOADS_PATH, 0775, true) && !is_dir(UPLOADS_PATH)) {
    cabin_gallery_redirect($cabin_id, 'msg=uploaded&n=0&e=1');
}

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$uploaded = 0;
$errors = 0;

$total = count($_FILES['images']['name']);

for ($i = 0; $i < $total; $i++) {
    $error_code = $_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE;

    if ($error_code === UPLOAD_ERR_NO_FILE) {
        continue;
    }

    if ($error_code !== UPLOAD_ERR_OK) {
        $errors++;
        continue;
    }

    $tmp_name = $_FILES['images']['tmp_name'][$i];
    $original_name = $_FILES['images']['name'][$i];
    $size = (int) $_FILES['images']['size'][$i];

    if (!is_uploaded_file($tmp_name) || $size <= 0 || $size > UPLOADS_MAX_BYTES) {
        $errors++;
        continue;
    }

    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions, true)) {
        $errors++;
        continue;
    }

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
        if ($finfo) {
            finfo_close($finfo);
        }
        if ($mime && !in_array($mime, $allowed_mimes, true)) {
            $errors++;
            continue;
        }
    }

    $filename = 'gallery_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (!@move_uploaded_file($tmp_name, UPLOADS_PATH . $filename)) {
        $errors++;
        continue;
    }

    @chmod(UPLOADS_PATH . $filename, 0644);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO gallery_images
                (image_path, caption_es, caption_en, category, titulo, descripcion, `order`, status)
             VALUES (?, ?, ?, ?, ?, ?, 0, 1)'
        );
        $stmt->execute([$filename, '', '', $slug, '', '']);
        $uploaded++;
    } catch (PDOException $e) {
        error_log('save_cabin_gallery: ' . $e->getMessage());
        @unlink(UPLOADS_PATH . $filename);
        $errors++;
    }
}

$query = 'msg=uploaded&n=' . $uploaded;
if ($errors > 0) {
    $query .= '&e=' . $errors;
}

cabin_gallery_redirect($cabin_id, $query);
