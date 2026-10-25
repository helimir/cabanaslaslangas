<?php
/**
 * admin/cabin_gallery.php
 * -----------------------------------------------------------------
 * Galería interna de una cabaña.
 *
 * Las imágenes se guardan en gallery_images con category = slug de la
 * cabaña y se muestran en el slider de su página pública.
 * -----------------------------------------------------------------
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

$cabin_id = isset($_GET['cabin']) ? (int) $_GET['cabin'] : 0;

$stmt = $pdo->prepare('SELECT * FROM cabins WHERE id = ?');
$stmt->execute([$cabin_id]);
$cabin = $stmt->fetch();

if (!$cabin) {
    header('Location: cabins.php?error=not_found');
    exit;
}

$slug = $cabin['slug'];

// ============================================================
// ELIMINAR IMAGEN
// ============================================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $image_id = (int) $_GET['delete'];

    $stmt = $pdo->prepare('SELECT image_path FROM gallery_images WHERE id = ? AND category = ?');
    $stmt->execute([$image_id, $slug]);
    $image = $stmt->fetch();

    if ($image) {
        $file = UPLOADS_PATH . basename($image['image_path']);
        if (is_file($file)) {
            @unlink($file);
        }
        $pdo->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$image_id]);
    }

    header('Location: cabin_gallery.php?cabin=' . $cabin_id . '&msg=deleted');
    exit;
}

// ============================================================
// IMÁGENES ACTUALES
// ============================================================
$stmt = $pdo->prepare('SELECT * FROM gallery_images WHERE category = ? ORDER BY `order` ASC, id ASC');
$stmt->execute([$slug]);
$images = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-images me-2"></i>Galería de la cabaña
                    </h2>
                    <p class="text-muted mb-0">
                        <strong><?= htmlspecialchars($cabin['name_es']) ?></strong>
                        <span class="badge bg-secondary ms-2">/cabana-<?= htmlspecialchars($slug) ?>.php</span>
                        <span class="badge bg-primary ms-1"><?= count($images) ?> imágenes</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="cabins.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver a Cabañas
                    </a>
                    <?php if (is_file(cabinFilePath($slug))): ?>
                        <a href="<?= SITE_URL ?>cabana-<?= urlencode($slug) ?>.php" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Ver página
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_GET['created'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>¡Cabaña creada!</strong> Ahora sube aquí las fotos que aparecerán en el
                    slider de su página pública. La primera imagen también se puede usar como portada.
                </div>
                <?php if (isset($_GET['custom'])): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Ya existía un archivo <code>cabana-<?= htmlspecialchars($slug) ?>.php</code> hecho a mano,
                        por lo que no se sobreescribió. Las fotos se guardan igualmente en la base de datos.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'uploaded'): ?>
                    <?php $n = (int) ($_GET['n'] ?? 0); $e = (int) ($_GET['e'] ?? 0); ?>
                    <div class="alert alert-<?= $e > 0 ? 'warning' : 'success' ?>">
                        <i class="bi bi-check-circle me-2"></i>
                        Se subieron <strong><?= $n ?></strong> imagen(es).
                        <?php if ($e > 0): ?>
                            <span class="text-danger"><?= $e ?> archivo(s) no se pudieron subir (formato o tamaño inválido).</span>
                        <?php endif; ?>
                    </div>
                <?php elseif ($_GET['msg'] === 'deleted'): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i> Imagen eliminada correctamente.
                    </div>
                <?php elseif ($_GET['msg'] === 'nofiles'): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i> No seleccionaste ninguna imagen.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="row">
                <!-- Subida -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-cloud-arrow-up me-2"></i>Subir imágenes</h5>
                        </div>
                        <div class="card-body">
                            <form action="process/save_cabin_gallery.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="cabin_id" value="<?= $cabin_id ?>">
                                <div class="mb-3">
                                    <label class="form-label">Fotos del slider interno *</label>
                                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                                    <small class="text-muted d-block mt-1">
                                        Puedes seleccionar varias a la vez.<br>
                                        <strong>Tamaño recomendado:</strong> <?= htmlspecialchars(image_size_text('cabin_gallery')) ?>.<br>
                                        Formatos: JPG, PNG, GIF, WEBP. <?= htmlspecialchars(image_max_size_text()) ?>
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-upload me-1"></i> Subir imágenes
                                </button>
                            </form>

                            <hr>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Estas imágenes se muestran en el slider de la página
                                <code>cabana-<?= htmlspecialchars($slug) ?>.php</code>.
                                Si no subes ninguna, se usa la Imagen Principal de la cabaña.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Imágenes actuales -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Imágenes actuales</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($images)): ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-images" style="font-size: 3rem; opacity: .3;"></i>
                                    <p class="mt-3 mb-0">Esta cabaña todavía no tiene imágenes en su galería.</p>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($images as $image): ?>
                                        <div class="col-sm-6 col-xl-4">
                                            <div class="border rounded-3 overflow-hidden h-100">
                                                <img src="<?= htmlspecialchars(UPLOADS_URL . basename($image['image_path'])) ?>"
                                                     alt="<?= htmlspecialchars((string) $image['caption_es']) ?>"
                                                     style="width: 100%; height: 140px; object-fit: cover;">
                                                <div class="p-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-muted text-truncate" title="<?= htmlspecialchars($image['caption_es'] ?: 'Sin título') ?>">
                                                        #<?= $image['id'] ?>
                                                    </small>
                                                    <a href="cabin_gallery.php?cabin=<?= $cabin_id ?>&delete=<?= $image['id'] ?>"
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('¿Eliminar esta imagen?')"
                                                       title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
