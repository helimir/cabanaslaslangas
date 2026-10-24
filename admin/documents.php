<?php
// admin/documents.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Procesar subida de PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_pdf'])) {
    $lang_doc = $_POST['lang_doc'] ?? 'es';
    $setting_key = 'tourism_pdf_' . $lang_doc;
    
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['pdf_file'];
        
        if ($file['type'] === 'application/pdf' && $file['size'] <= 5242880) { // 5MB
            $upload_dir = __DIR__ . '/uploads/pdfs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = 'tourism_' . $lang_doc . '_' . time() . '.pdf';
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Guardar en la BD
                $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$filename, $setting_key]);
                
                header('Location: documents.php?msg=uploaded');
                exit;
            }
        }
    }
    header('Location: documents.php?error=upload_failed');
    exit;
}

// Eliminar PDF
if (isset($_GET['delete']) && isset($_GET['lang'])) {
    $lang_doc = $_GET['lang'];
    $setting_key = 'tourism_pdf_' . $lang_doc;
    
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$setting_key]);
    $file = $stmt->fetch(PDO::FETCH_COLUMN);
    
    if ($file && file_exists(__DIR__ . '/uploads/pdfs/' . $file)) {
        unlink(__DIR__ . '/uploads/pdfs/' . $file);
    }
    
    $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = NULL WHERE setting_key = ?");
    $stmt->execute([$setting_key]);
    
    header('Location: documents.php?msg=deleted');
    exit;
}

// Obtener PDFs actuales
$pdf_es = getSetting('tourism_pdf_es');
$pdf_en = getSetting('tourism_pdf_en');

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div class="me-3 p-3 bg-danger bg-opacity-10 rounded-circle">
                    <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h2 class="mb-0">Gestión de Documentos</h2>
                    <p class="text-muted mb-0">Sube y administra los PDFs de turismo y otros documentos</p>
                </div>
            </div>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'uploaded'): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i> PDF subido correctamente.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i> PDF eliminado correctamente.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] == 'upload_failed'): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i> Error al subir el PDF. Verifica que sea un archivo válido (máx 5MB).
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- PDF Español -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-flag-fill me-2 text-primary"></i>PDF en Español</h5>
                </div>
                <div class="card-body">
                    <?php if ($pdf_es): ?>
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                                <div class="ms-3 flex-grow-1">
                                    <strong><?= htmlspecialchars($pdf_es) ?></strong>
                                    <div class="small text-muted">Archivo actual</div>
                                </div>
                                <a href="<?= SITE_URL ?>uploads/pdfs/<?= $pdf_es ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="?delete=1&lang=es" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este PDF?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No hay archivo PDF cargado para español.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="lang_doc" value="es">
                        <div class="input-group">
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                            <button type="submit" name="upload_pdf" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i> Subir
                            </button>
                        </div>
                        <small class="text-muted">Solo archivos PDF (máx. 5MB)</small>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- PDF Inglés -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-flag-fill me-2 text-primary"></i>PDF en Inglés</h5>
                </div>
                <div class="card-body">
                    <?php if ($pdf_en): ?>
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                                <div class="ms-3 flex-grow-1">
                                    <strong><?= htmlspecialchars($pdf_en) ?></strong>
                                    <div class="small text-muted">Current file</div>
                                </div>
                                <a href="<?= SITE_URL ?>uploads/pdfs/<?= $pdf_en ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="?delete=1&lang=en" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este PDF?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No hay archivo PDF cargado para inglés.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="lang_doc" value="en">
                        <div class="input-group">
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                            <button type="submit" name="upload_pdf" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i> Upload
                            </button>
                        </div>
                        <small class="text-muted">Only PDF files (max. 5MB)</small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Consejos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body">
                    <h6><i class="bi bi-lightbulb me-2"></i>Consejos para tus documentos</h6>
                    <ul class="small mb-0">
                        <li>Los PDFs se mostrarán en la sección "Turismo" del sitio web</li>
                        <li>Sube archivos en formato PDF con un tamaño máximo de 5MB</li>
                        <li>Los nombres de los archivos se generan automáticamente para evitar duplicados</li>
                        <li>Al subir un nuevo PDF, el anterior se reemplaza automáticamente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>