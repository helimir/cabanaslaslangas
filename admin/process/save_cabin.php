<?php
/**
 * admin/process/save_cabin.php
 * -----------------------------------------------------------------
 * Crear / editar cabañas.
 *
 * Además de guardar los datos en la base de datos, garantiza que
 * exista el archivo físico cabana-{slug}.php que carga el modelo
 * informativo común (includes/cabin_page.php).
 * -----------------------------------------------------------------
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';

/**
 * Redirige de vuelta al listado de cabañas.
 */
function cabin_redirect(string $query = ''): void
{
    header('Location: ../cabins.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    cabin_redirect();
}

/**
 * Procesa la subida de una imagen de cabaña.
 *
 * @return string|null Nombre del archivo guardado, o null si no se subió nada.
 */
function cabin_upload_image(string $field, ?string &$error): ?string
{
    $error = null;

    if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        $error = 'error=upload';
        return null;
    }

    if ($file['size'] > UPLOADS_MAX_BYTES) {
        $error = 'error=upload_size';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        $error = 'error=upload_type';
        return null;
    }

    // Verificación real del tipo de archivo cuando esté disponible.
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }
        $valid_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if ($mime && !in_array($mime, $valid_mimes, true)) {
            $error = 'error=upload_type';
            return null;
        }
    }

    if (!is_dir(UPLOADS_PATH) && !@mkdir(UPLOADS_PATH, 0775, true) && !is_dir(UPLOADS_PATH)) {
        $error = 'error=upload_dir';
        return null;
    }

    // Nombre corto (la columna image_path es varchar(50)).
    $filename = 'cabin_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (!@move_uploaded_file($file['tmp_name'], UPLOADS_PATH . $filename)) {
        $error = 'error=upload';
        return null;
    }

    @chmod(UPLOADS_PATH . $filename, 0644);

    return $filename;
}

// ============================================================
// DATOS DEL FORMULARIO
// ============================================================
$id              = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name_es         = trim($_POST['name_es'] ?? '');
$name_en         = trim($_POST['name_en'] ?? '');
$slug_input      = trim($_POST['slug'] ?? '');
$description_es  = trim($_POST['description_es'] ?? '');
$description_en  = trim($_POST['description_en'] ?? '');
$price           = (float) ($_POST['price'] ?? 0);
$dolar           = (float) ($_POST['dolar'] ?? 0);
$max_people      = (int) ($_POST['max_people'] ?? 2);
$bed_config_es   = trim($_POST['bed_config_es'] ?? '');
$bed_config_en   = trim($_POST['bed_config_en'] ?? '');
$status          = ((int) ($_POST['status'] ?? 1)) === 1 ? 1 : 0;

$slug = normalizeCabinSlug($slug_input);

// ============================================================
// VALIDACIONES
// ============================================================
if ($name_es === '' || $name_en === '' || $price <= 0) {
    cabin_redirect('error=validation');
}

if (!isValidCabinSlug($slug)) {
    cabin_redirect('error=slug_invalid');
}

if ($max_people < 1) {
    $max_people = 1;
}

// ============================================================
// SLUG ÚNICO
// ============================================================
try {
    $stmt = $pdo->prepare('SELECT id FROM cabins WHERE slug = ? AND id <> ?');
    $stmt->execute([$slug, $id]);
    if ($stmt->fetchColumn()) {
        cabin_redirect('error=slug_taken');
    }
} catch (PDOException $e) {
    error_log('save_cabin (validar slug): ' . $e->getMessage());
    cabin_redirect('error=db_error');
}

// ============================================================
// IMÁGENES
// ============================================================
$upload_error = null;
$new_cover = cabin_upload_image('image_path', $upload_error);
if ($upload_error !== null) {
    cabin_redirect($upload_error);
}
$new_main = cabin_upload_image('image_main', $upload_error);
if ($upload_error !== null) {
    // Si la portada ya se había subido, se elimina para no dejar basura.
    if ($new_cover) {
        @unlink(UPLOADS_PATH . $new_cover);
    }
    cabin_redirect($upload_error);
}

// ============================================================
// GUARDAR
// ============================================================
try {
    if ($id > 0) {
        // --------------------------------------------
        // EDITAR
        // --------------------------------------------
        $stmt = $pdo->prepare('SELECT * FROM cabins WHERE id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if (!$old) {
            cabin_redirect('error=not_found');
        }

        $sets = [
            'name_es = ?', 'name_en = ?', 'slug = ?',
            'description_es = ?', 'description_en = ?',
            'price = ?', 'dolar = ?', 'max_people = ?',
            'bed_config_es = ?', 'bed_config_en = ?', 'status = ?',
        ];
        $params = [
            $name_es, $name_en, $slug,
            $description_es, $description_en,
            $price, $dolar, $max_people,
            $bed_config_es, $bed_config_en, $status,
        ];

        if ($new_cover !== null) {
            $sets[] = 'image_path = ?';
            $params[] = $new_cover;
        }
        if ($new_main !== null) {
            $sets[] = 'image_main = ?';
            $params[] = $new_main;
        }

        $params[] = $id;

        $stmt = $pdo->prepare('UPDATE cabins SET ' . implode(', ', $sets) . ' WHERE id = ?');
        $stmt->execute($params);

        // Eliminar archivos antiguos reemplazados.
        if ($new_cover !== null && !empty($old['image_path']) && $old['image_path'] !== $new_cover) {
            $old_cover = UPLOADS_PATH . basename($old['image_path']);
            if (is_file($old_cover)) {
                @unlink($old_cover);
            }
        }
        if ($new_main !== null && !empty($old['image_main']) && $old['image_main'] !== $new_main) {
            $old_main = UPLOADS_PATH . basename($old['image_main']);
            if (is_file($old_main)) {
                @unlink($old_main);
            }
        }

        // --------------------------------------------
        // ARCHIVO FÍSICO DE LA CABAÑA
        // --------------------------------------------
        $old_slug = (string) $old['slug'];
        $file_status = 'created';

        if ($old_slug !== $slug) {
            deleteCabinFile($old_slug);
            $file_ok = generateCabinFile($slug, $file_status);
        } elseif (!is_file(cabinFilePath($slug))) {
            $file_ok = generateCabinFile($slug, $file_status);
        } else {
            // El archivo ya existe (puede ser una página hecha a mano): no se toca.
            $file_ok = true;
            $file_status = 'exists';
        }

        if (!$file_ok) {
            error_log('save_cabin: no se pudo generar el archivo de la cabaña ' . $slug);
            cabin_redirect('msg=saved_no_file');
        }

        cabin_redirect($file_status === 'exists_custom' ? 'msg=saved_file_exists' : 'msg=saved');

    } else {
        // --------------------------------------------
        // CREAR
        // --------------------------------------------
        if (!is_dir(UPLOADS_PATH) && !@mkdir(UPLOADS_PATH, 0775, true) && !is_dir(UPLOADS_PATH)) {
            if ($new_cover) {
                @unlink(UPLOADS_PATH . $new_cover);
            }
            if ($new_main) {
                @unlink(UPLOADS_PATH . $new_main);
            }
            cabin_redirect('error=upload_dir');
        }

        $stmt = $pdo->prepare('INSERT INTO cabins
            (name_es, name_en, slug, description_es, description_en,
             price, dolar, max_people, bed_config_es, bed_config_en,
             image_main, image_path, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $name_es, $name_en, $slug,
            $description_es, $description_en,
            $price, $dolar, $max_people,
            $bed_config_es, $bed_config_en,
            $new_main, $new_cover ?? '', $status,
        ]);

        // --------------------------------------------
        // ARCHIVO FÍSICO DE LA CABAÑA
        // --------------------------------------------
        $file_status = 'created';
        $file_ok = generateCabinFile($slug, $file_status);

        if (!$file_ok) {
            error_log('save_cabin: cabaña "' . $slug . '" creada en BD, pero no se pudo crear cabana-' . $slug . '.php');
            cabin_redirect('msg=added_no_file');
        }

        if ($file_status === 'exists_custom') {
            cabin_redirect('msg=added_file_exists');
        }

        cabin_redirect('msg=added');
    }
} catch (PDOException $e) {
    error_log('save_cabin: ' . $e->getMessage());

    // Limpiar imágenes recién subidas si la operación falló.
    if (!empty($new_cover)) {
        @unlink(UPLOADS_PATH . $new_cover);
    }
    if (!empty($new_main)) {
        @unlink(UPLOADS_PATH . $new_main);
    }

    cabin_redirect('error=db_error');
}
