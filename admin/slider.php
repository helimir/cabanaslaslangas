<?php
// ============================================================
// ADMIN SLIDER - Gestión de imágenes del carrusel principal
// ============================================================

require_once 'includes/config.php';
require_once 'includes/auth.php';

// ============================================================
// 1. ELIMINAR SLIDE
// ============================================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // 1.1 Buscar la imagen en la BD
    $stmt = $pdo->prepare("SELECT image_path FROM slider_images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 1.2 Eliminar el archivo físico del servidor
    if ($image && file_exists(UPLOADS_PATH . $image['image_path'])) {
        unlink(UPLOADS_PATH . $image['image_path']); // Borra el archivo
    }
    
    // 1.3 Eliminar el registro de la BD
    $stmt = $pdo->prepare("DELETE FROM slider_images WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: slider.php?msg=deleted');
    exit;
}

// ============================================================
// 2. OBTENER TODOS LOS SLIDES
// ============================================================
// Ordenados por el campo 'order' (de menor a mayor)
$stmt = $pdo->query("SELECT * FROM slider_images ORDER BY `order` ASC, id ASC");
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================================================
// 3. CONTAR SLIDES ACTIVOS (para mostrar estadísticas)
// ============================================================
$activeSlides = array_filter($slides, function($s) { return $s['status'] == 1; });
$totalSlides = count($slides);
$activeCount = count($activeSlides);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <!-- ============================================================
    HEADER CON ESTADÍSTICAS
    ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2><i class="bi bi-images me-2"></i>Gestión del Slider</h2>
                    <p class="text-muted mb-0">
                        <span class="badge bg-success"><?= $activeCount ?> activos</span>
                        <span class="badge bg-secondary"><?= $totalSlides - $activeCount ?> inactivos</span>
                        <span class="badge bg-primary">Total: <?= $totalSlides ?></span>
                    </p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                    <i class="bi bi-plus-circle me-2"></i> Nuevo Slide
                </button>
            </div>
            
            <!-- Mensajes de éxito/error -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Slide eliminado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Nuevo slide agregado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Slide actualizado correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================
    LISTA DE SLIDES
    ============================================================ -->
    <div class="row">
        <?php if (empty($slides)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No hay slides disponibles</h4>
                    <p class="text-muted">Haz clic en "Nuevo Slide" para agregar tu primera imagen al carrusel.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($slides as $slide): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Imagen del slide -->
                        <img src="<?= UPLOADS_URL . $slide['image_path'] ?>" 
                             class="card-img-top" 
                             alt="Slide <?= $slide['id'] ?>" 
                             style="height: 180px; object-fit: cover; background: #f0f0f0;">
                        
                        <div class="card-body">
                            <!-- Título y estado -->
                            <h6 class="card-title text-truncate" title="<?= htmlspecialchars($slide['title_es'] ?: 'Sin título') ?>">
                                <?= htmlspecialchars($slide['title_es'] ?: 'Sin título') ?>
                            </h6>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-<?= $slide['status'] ? 'success' : 'secondary' ?>">
                                    <?= $slide['status'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                                <span class="badge bg-light text-dark">Orden: <?= $slide['order'] ?></span>
                            </div>
                            
                            <!-- Subtítulo (opcional) -->
                            <?php if ($slide['subtitle_es']): ?>
                                <p class="card-text small text-muted text-truncate">
                                    <?= htmlspecialchars($slide['subtitle_es']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSlideModal<?= $slide['id'] ?>"
                                    title="Editar slide">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <a href="?delete=<?= $slide['id'] ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este slide?\nEsta acción no se puede deshacer.')"
                               title="Eliminar slide">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- ============================================================
                MODAL EDITAR SLIDE
                ============================================================ -->
                <div class="modal fade" id="editSlideModal<?= $slide['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="process/save_slider.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-pencil me-2"></i> Editar Slide #<?= $slide['id'] ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-flag-fill me-1"></i> Título (Español)
                                                </label>
                                                <input type="text" name="title_es" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($slide['title_es']) ?>"
                                                       placeholder="Ej: Cabañas Las Lengas">
                                                <small class="text-muted">Se muestra en la versión en español</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-flag-fill me-1"></i> Título (English)
                                                </label>
                                                <input type="text" name="title_en" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($slide['title_en']) ?>"
                                                       placeholder="Ej: Cabins Las Lengas">
                                                <small class="text-muted">Se muestra en la versión en inglés</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-image me-1"></i> Imagen
                                                </label>
                                                <input type="file" name="image" 
                                                       class="form-control" 
                                                       accept="image/*">
                                                <small class="text-muted">
                                                    Dejar vacío para mantener la imagen actual<br>
                                                    Formatos: JPG, PNG, WEBP (Máx. 2MB)
                                                </small>
                                                <?php if ($slide['image_path']): ?>
                                                    <div class="mt-2">
                                                        <img src="<?= UPLOADS_URL . $slide['image_path'] ?>" 
                                                             alt="Vista previa" 
                                                             style="max-height: 80px; border-radius: 5px; border: 1px solid #ddd;">
                                                        <span class="text-muted small"><?= $slide['image_path'] ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-subtract me-1"></i> Subtítulo (Español)
                                                </label>
                                                <input type="text" name="subtitle_es" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($slide['subtitle_es'] ?? '') ?>"
                                                       placeholder="Ej: Tu hogar en la Patagonia">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-subtract me-1"></i> Subtítulo (English)
                                                </label>
                                                <input type="text" name="subtitle_en" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($slide['subtitle_en'] ?? '') ?>"
                                                       placeholder="Ej: Your home in Patagonia">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-sort-numeric-down me-1"></i> Orden
                                                </label>
                                                <input type="number" name="order" 
                                                       class="form-control" 
                                                       value="<?= $slide['order'] ?>"
                                                       min="0">
                                                <small class="text-muted">Número más bajo = aparece primero</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-link me-1"></i> Enlace (opcional)
                                                </label>
                                                <input type="text" name="link" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($slide['link'] ?? '') ?>"
                                                       placeholder="https://...">
                                                <small class="text-muted">URL a donde lleva al hacer clic</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-toggle-on me-1"></i> Estado
                                                </label>
                                                <select name="status" class="form-control">
                                                    <option value="1" <?= $slide['status'] == 1 ? 'selected' : '' ?>>
                                                        ✅ Activo (visible)
                                                    </option>
                                                    <option value="0" <?= $slide['status'] == 0 ? 'selected' : '' ?>>
                                                        ❌ Inactivo (oculto)
                                                    </option>
                                                </select>
                                                <small class="text-muted">Los slides inactivos no se muestran en la web</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
MODAL AGREGAR NUEVO SLIDE
============================================================ -->
<div class="modal fade" id="addSlideModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_slider.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i> Nuevo Slide
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-flag-fill me-1"></i> Título (Español) *
                                </label>
                                <input type="text" name="title_es" class="form-control" required>
                                <small class="text-muted">Título principal en español</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-flag-fill me-1"></i> Título (English) *
                                </label>
                                <input type="text" name="title_en" class="form-control" required>
                                <small class="text-muted">Título principal en inglés</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-image me-1"></i> Imagen *
                                </label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">
                                    Formatos: JPG, PNG, WEBP<br>
                                    Tamaño recomendado: 1920x1080px
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-subtract me-1"></i> Subtítulo (Español)
                                </label>
                                <input type="text" name="subtitle_es" class="form-control" 
                                       placeholder="Ej: Disfruta de la Patagonia">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-subtract me-1"></i> Subtítulo (English)
                                </label>
                                <input type="text" name="subtitle_en" class="form-control" 
                                       placeholder="Ej: Enjoy Patagonia">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-sort-numeric-down me-1"></i> Orden
                                </label>
                                <input type="number" name="order" class="form-control" value="0" min="0">
                                <small class="text-muted">Número más bajo = aparece primero</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-link me-1"></i> Enlace (opcional)
                                </label>
                                <input type="text" name="link" class="form-control" 
                                       placeholder="https://...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-toggle-on me-1"></i> Estado
                                </label>
                                <select name="status" class="form-control">
                                    <option value="1">✅ Activo (visible)</option>
                                    <option value="0">❌ Inactivo (oculto)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> Agregar Slide
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>