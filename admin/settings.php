<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// ============================================================
// PROCESAR SUBIDA DE PDF (se ejecuta ANTES que el resto)
// ============================================================
if (isset($_POST['upload_pdf']) && isset($_FILES['pdf_file'])) {
    $lang = $_POST['pdf_lang'] ?? 'es';
    $setting_key = 'tourism_pdf_' . $lang;
    $upload_dir = __DIR__ . '/uploads/pdfs/';
    $error_msg = '';
    $success = false;
    
    // Verificar que la carpeta existe
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['pdf_file'];
        $tmp_name = $file['tmp_name'];
        $file_size = $file['size'] / 1024 / 1024; // MB
        $file_type = $file['type'];
        
        // Validar que sea PDF
        $is_pdf = false;
        if ($file_type === 'application/pdf') {
            $is_pdf = true;
        }
        // Verificar por extensión
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $is_pdf = true;
        }
        // Verificar por contenido
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);
            if ($mime === 'application/pdf') {
                $is_pdf = true;
            }
        }
        
        if (!$is_pdf) {
            $error_msg = 'El archivo no es un PDF válido.';
        } elseif ($file_size > 5) {
            $error_msg = 'El archivo es demasiado grande. Máximo 5MB.';
        } else {
            // Generar nombre único
            $filename = 'tourism_' . $lang . '_' . time() . '.pdf';
            $target_path = $upload_dir . $filename;
            
            // Mover archivo
            if (move_uploaded_file($tmp_name, $target_path)) {
                // Eliminar archivo anterior
                $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
                $stmt->execute([$setting_key]);
                $old_file = $stmt->fetch(PDO::FETCH_COLUMN);
                
                if ($old_file && file_exists($upload_dir . $old_file)) {
                    unlink($upload_dir . $old_file);
                }
                
                // Actualizar BD
                $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                if ($stmt->execute([$filename, $setting_key])) {
                    $success = true;
                } else {
                    $error_msg = 'Error al guardar en la base de datos.';
                }
            } else {
                $error_msg = 'Error al mover el archivo.';
            }
        }
    } else {
        $error_msg = 'Error al subir el archivo. Código: ' . $_FILES['pdf_file']['error'];
    }
    
    // Redirigir con mensaje
    $redirect = 'settings.php';
    if ($success) {
        $redirect .= '?pdf=ok&lang=' . $lang;
    } else {
        $redirect .= '?pdf=error&msg=' . urlencode($error_msg);
    }
    header('Location: ' . $redirect);
    exit;
}

// ============================================================
// PROCESAR GUARDAR CONFIGURACIONES DE TEXTO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = substr($key, 8);
            $setting_value = trim($value);
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$setting_value, $setting_key]);
        }
    }
    header('Location: settings.php?msg=saved');
    exit;
}

// ============================================================
// OBTENER CONFIGURACIONES
// ============================================================
$stmt = $pdo->query("SELECT * FROM site_settings ORDER BY id ASC");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener PDFs actuales
$pdf_es = getSetting('tourism_pdf_es');
$pdf_en = getSetting('tourism_pdf_en');

// Mensajes de estado
$pdf_message = '';
$pdf_type = '';

if (isset($_GET['pdf'])) {
    if ($_GET['pdf'] == 'ok') {
        $lang = $_GET['lang'] ?? 'es';
        $pdf_message = 'PDF en ' . ($lang == 'es' ? 'español' : 'inglés') . ' actualizado correctamente.';
        $pdf_type = 'success';
    } elseif ($_GET['pdf'] == 'error') {
        $pdf_message = 'Error: ' . ($_GET['msg'] ?? 'No se pudo subir el archivo.');
        $pdf_type = 'danger';
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2><i class="bi bi-gear-fill me-2"></i>Configuración General</h2>
            <p class="text-muted">Administra los textos, datos y archivos del sitio web</p>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i> Configuración guardada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($pdf_message): ?>
                <div class="alert alert-<?= $pdf_type ?> alert-dismissible fade show">
                    <i class="bi <?= $pdf_type == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i>
                    <?= $pdf_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- ============================================================
                    FORMULARIO DE TEXTO
                    ============================================================ -->
                    <form method="POST" action="">
                        <h5 class="mb-3"><i class="bi bi-gear me-2 text-primary"></i>Configuración General</h5>
                        <div class="row">
                            <?php 
                            $excluded_keys = ['tourism_pdf_es', 'tourism_pdf_en'];
                            foreach ($settings as $setting): 
                                if (in_array($setting['setting_key'], $excluded_keys)) continue;
                            ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <?= htmlspecialchars($setting['description'] ?: $setting['setting_key']) ?>
                                    </label>
                                    <?php if ($setting['setting_type'] == 'textarea'): ?>
                                        <textarea name="setting_<?= $setting['setting_key'] ?>" 
                                                  class="form-control" 
                                                  rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                    <?php elseif ($setting['setting_type'] == 'number'): ?>
                                        <input type="number" name="setting_<?= $setting['setting_key'] ?>" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php else: ?>
                                        <input type="text" name="setting_<?= $setting['setting_key'] ?>" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                    <small class="text-muted">Clave: <?= $setting['setting_key'] ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" name="save_settings" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Guardar Configuración
                        </button>
                    </form>

                    <hr class="my-4">

                    <!-- ============================================================
                    SECCIÓN DE PDFS (Formulario separado)
                    ============================================================ -->
                    <h5 class="mb-3"><i class="bi bi-file-pdf me-2 text-danger"></i>Archivos PDF - Guía de Turismo</h5>
                    <p class="text-muted">Sube los archivos PDF que se mostrarán en la sección de Turismo.</p>
                    
                    <div class="row">
                        <!-- PDF Español -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6><i class="bi bi-flag-fill me-2 text-primary"></i>PDF en Español</h6>
                                    
                                    <?php if ($pdf_es): ?>
                                        <?php 
                                        $pdf_path = __DIR__ . '/uploads/pdfs/' . $pdf_es;
                                        $file_exists = file_exists($pdf_path);
                                        ?>
                                        <div class="alert <?= $file_exists ? 'alert-success' : 'alert-warning' ?>">
                                            <i class="bi <?= $file_exists ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i>
                                            Archivo: <strong><?= htmlspecialchars($pdf_es) ?></strong>
                                            <?php if (!$file_exists): ?>
                                                <br><span class="text-danger">⚠️ El archivo no existe en el servidor</span>
                                            <?php endif; ?>
                                            <br>
                                            <div class="mt-2">
                                                <?php if ($file_exists): ?>
                                                    <a href="<?= SITE_URL ?>admin/uploads/pdfs/<?= $pdf_es ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i> Ver PDF
                                                    </a>
                                                    <a href="<?= SITE_URL ?>admin/uploads/pdfs/<?= $pdf_es ?>" 
                                                       download 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1"></i> Descargar
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">El archivo no está disponible</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            No hay archivo PDF cargado para español.
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- FORMULARIO DE SUBIDA PDF - ESPAÑOL -->
                                    <form method="POST" action="" enctype="multipart/form-data" class="mt-3">
                                        <div class="input-group">
                                            <input type="file" name="pdf_file" 
                                                   class="form-control" 
                                                   accept=".pdf" 
                                                   id="pdf_file_es"
                                                   required>
                                            <input type="hidden" name="pdf_lang" value="es">
                                            <button type="submit" name="upload_pdf" class="btn btn-primary">
                                                <i class="bi bi-cloud-upload me-1"></i> Subir
                                            </button>
                                        </div>
                                        <small class="text-muted">Solo archivos PDF (máx. 5MB)</small>
                                        <div id="pdf_status_es" class="mt-2"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Inglés -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6><i class="bi bi-flag-fill me-2 text-primary"></i>PDF en Inglés</h6>
                                    
                                    <?php if ($pdf_en): ?>
                                        <?php 
                                        $pdf_path = __DIR__ . '/uploads/pdfs/' . $pdf_en;
                                        $file_exists = file_exists($pdf_path);
                                        ?>
                                        <div class="alert <?= $file_exists ? 'alert-success' : 'alert-warning' ?>">
                                            <i class="bi <?= $file_exists ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i>
                                            Current file: <strong><?= htmlspecialchars($pdf_en) ?></strong>
                                            <?php if (!$file_exists): ?>
                                                <br><span class="text-danger">⚠️ The file does not exist on the server</span>
                                            <?php endif; ?>
                                            <br>
                                            <div class="mt-2">
                                                <?php if ($file_exists): ?>
                                                    <a href="<?= SITE_URL ?>admin/uploads/pdfs/<?= $pdf_en ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i> View PDF
                                                    </a>
                                                    <a href="<?= SITE_URL ?>admin/uploads/pdfs/<?= $pdf_en ?>" 
                                                       download 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">File not available</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            No hay archivo PDF cargado para inglés.
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- FORMULARIO DE SUBIDA PDF - INGLÉS -->
                                    <form method="POST" action="" enctype="multipart/form-data" class="mt-3">
                                        <div class="input-group">
                                            <input type="file" name="pdf_file" 
                                                   class="form-control" 
                                                   accept=".pdf" 
                                                   id="pdf_file_en"
                                                   required>
                                            <input type="hidden" name="pdf_lang" value="en">
                                            <button type="submit" name="upload_pdf" class="btn btn-primary">
                                                <i class="bi bi-cloud-upload me-1"></i> Upload
                                            </button>
                                        </div>
                                        <small class="text-muted">Only PDF files (max. 5MB)</small>
                                        <div id="pdf_status_en" class="mt-2"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================
                    INFORMACIÓN DEL SISTEMA
                    ============================================================ -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-info-circle me-2"></i>Información del sistema</h6>
                                    <div class="row small">
                                        <div class="col-md-3">
                                            <strong>Carpeta PDFs:</strong>
                                            <span class="text-muted"><?= __DIR__ . '/uploads/pdfs/' ?></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Permisos:</strong>
                                            <span class="text-muted">
                                                <?php 
                                                $folder = __DIR__ . '/uploads/pdfs/';
                                                if (file_exists($folder)) {
                                                    echo substr(sprintf('%o', fileperms($folder)), -4);
                                                    echo is_writable($folder) ? ' (escribible ✅)' : ' (no escribible ❌)';
                                                } else {
                                                    echo 'Carpeta no existe';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>PDF Español:</strong>
                                            <span class="text-muted"><?= $pdf_es ?: 'No configurado' ?></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>PDF Inglés:</strong>
                                            <span class="text-muted"><?= $pdf_en ?: 'No configurado' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validar PDF antes de subir
document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var fileInput = this.querySelector('input[type="file"]');
        var statusDiv = this.querySelector('[id^="pdf_status_"]');
        
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            if (statusDiv) {
                statusDiv.innerHTML = '<div class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Por favor, selecciona un archivo.</div>';
            }
            return false;
        }
        
        var file = fileInput.files[0];
        var fileName = file.name;
        var fileSize = file.size / 1024 / 1024;
        var fileExt = fileName.split('.').pop().toLowerCase();
        
        // Validar extensión
        if (fileExt !== 'pdf') {
            e.preventDefault();
            if (statusDiv) {
                statusDiv.innerHTML = '<div class="text-danger"><i class="bi bi-x-circle me-1"></i> El archivo debe tener extensión .pdf (tiene .' + fileExt + ').</div>';
            }
            return false;
        }
        
        // Validar tamaño
        if (fileSize > 5) {
            e.preventDefault();
            if (statusDiv) {
                statusDiv.innerHTML = '<div class="text-danger"><i class="bi bi-x-circle me-1"></i> El archivo es demasiado grande. Máximo 5MB (actual: ' + fileSize.toFixed(2) + ' MB).</div>';
            }
            return false;
        }
        
        // Mostrar confirmación
        if (statusDiv) {
            statusDiv.innerHTML = '<div class="text-success"><i class="bi bi-check-circle me-1"></i> Subiendo <strong>' + file.name + '</strong> (' + fileSize.toFixed(2) + ' MB)...</div>';
        }
        
        return true;
    });
});

// Mostrar información del archivo seleccionado
document.querySelectorAll('input[type="file"][name="pdf_file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        var statusDiv = this.closest('form').querySelector('[id^="pdf_status_"]');
        if (this.files.length > 0 && statusDiv) {
            var file = this.files[0];
            var size = (file.size / 1024 / 1024).toFixed(2);
            statusDiv.innerHTML = '<div class="text-info"><i class="bi bi-info-circle me-1"></i> Archivo seleccionado: <strong>' + file.name + '</strong> (' + size + ' MB)</div>';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>