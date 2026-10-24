<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Procesar eliminación
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Obtener slug e imágenes
    $stmt = $pdo->prepare("SELECT slug, image_main, image_path FROM cabins WHERE id = ?");
    $stmt->execute([$id]);
    $cabin = $stmt->fetch();

    if ($cabin) {
        // Borrar imágenes asociadas (solo dentro de la carpeta de uploads)
        foreach (['image_main', 'image_path'] as $field) {
            if (!empty($cabin[$field])) {
                $file = UPLOADS_PATH . basename($cabin[$field]);
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        // Borrar galería interna de la cabaña (category = slug)
        $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE category = ?");
        $stmt->execute([$cabin['slug']]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $gallery_image) {
            if (!empty($gallery_image['image_path'])) {
                $file = UPLOADS_PATH . basename($gallery_image['image_path']);
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
        $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE category = ?");
        $stmt->execute([$cabin['slug']]);

        // Borrar archivo físico cabana-{slug}.php
        deleteCabinFile($cabin['slug']);

        // Borrar de la BD (las relaciones de equipamiento se eliminan en cascada)
        $stmt = $pdo->prepare("DELETE FROM cabins WHERE id = ?");
        $stmt->execute([$id]);
    }

    header('Location: cabins.php?msg=deleted');
    exit;
}

// Obtener todas las cabañas
$stmt = $pdo->query("SELECT * FROM cabins ORDER BY id ASC");
$cabins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Equipamiento disponible para asignar a cada cabaña
$all_amenities = getAllAmenities();

// Cantidad de imágenes de galería por categoría (slug de cabaña)
$gallery_counts = [];
try {
    $stmt = $pdo->query("SELECT category, COUNT(*) AS total FROM gallery_images GROUP BY category");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $gallery_counts[$row['category']] = (int) $row['total'];
    }
} catch (Exception $e) {
    $gallery_counts = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <h2 class="mb-2 mb-sm-0"><i class="bi bi-building me-2"></i>Gestión de Cabañas</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCabinModal">
                    <i class="bi bi-plus-circle me-2"></i> Nueva Cabaña
                </button>
            </div>
            
            <?php
            // ============================================================
            // MENSAJES DE ESTADO
            // ============================================================
            $flash_message = '';
            $flash_type = 'success';
            $flash_icon = 'bi-check-circle';

            $messages = [
                'deleted'            => ['Cabaña eliminada correctamente.', 'success', 'bi-check-circle'],
                'added'              => ['Cabaña creada correctamente. Se generó su página pública.', 'success', 'bi-check-circle'],
                'saved'              => ['Cabaña actualizada correctamente.', 'success', 'bi-check-circle'],
                'added_no_file'      => ['Cabaña creada en la base de datos, pero NO se pudo generar el archivo cabana-{slug}.php. Revisa los permisos de escritura de la raíz del proyecto.', 'warning', 'bi-exclamation-triangle'],
                'saved_no_file'      => ['Cabaña actualizada, pero NO se pudo generar el archivo cabana-{slug}.php. Revisa los permisos de escritura de la raíz del proyecto.', 'warning', 'bi-exclamation-triangle'],
                'added_file_exists'  => ['Cabaña creada en la base de datos. Ya existía un archivo cabana-{slug}.php hecho a mano, por lo que NO se sobreescribió.', 'warning', 'bi-exclamation-triangle'],
                'saved_file_exists'  => ['Cabaña actualizada. Ya existía un archivo cabana-{slug}.php hecho a mano, por lo que NO se sobreescribió.', 'warning', 'bi-exclamation-triangle'],
            ];

            $errors = [
                'validation'  => 'Faltan datos obligatorios (nombre en español/inglés y precio).',
                'slug_invalid' => 'El slug no es válido. Usa solo letras minúsculas, números y guiones.',
                'slug_taken'  => 'Ya existe otra cabaña con ese slug. Usa un identificador diferente.',
                'upload'      => 'No se pudo subir la imagen. Intenta nuevamente.',
                'upload_type' => 'La imagen debe ser JPG, PNG, GIF o WEBP.',
                'upload_size' => 'La imagen supera el máximo permitido de 5 MB.',
                'upload_dir'  => 'La carpeta de imágenes no existe o no tiene permisos de escritura.',
                'db_error'    => 'Ocurrió un error al guardar en la base de datos. Intenta nuevamente.',
                'not_found'   => 'La cabaña que intentas editar ya no existe.',
            ];

            if (isset($_GET['msg']) && isset($messages[$_GET['msg']])) {
                [$flash_message, $flash_type, $flash_icon] = $messages[$_GET['msg']];
            } elseif (isset($_GET['error']) && isset($errors[$_GET['error']])) {
                $flash_message = $errors[$_GET['error']];
                $flash_type = 'danger';
                $flash_icon = 'bi-x-circle';
            }

            if ($flash_message !== ''):
            ?>
                <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show">
                    <i class="bi <?= $flash_icon ?> me-2"></i> <?= htmlspecialchars($flash_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table class="table table-hover mb-0" style="min-width: 700px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px; min-width: 50px;">ID</th>
                                    <th style="width: 100px; min-width: 50px;">Imagen</th>
                                    <th style="min-width: 120px;">Nombre (ES)</th>
                                    <th style="min-width: 120px;">Nombre (EN)</th>
                                    <th style="width: 100px; min-width: 100px;">Precio</th>
                                    <th style="width: 100px; min-width: 100px;">Dolar</th>
                                    <th style="width: 70px; min-width: 70px;">Pers.</th>
                                    <th style="width: 80px; min-width: 80px;">Estado</th>
                                    <th style="width: 110px; min-width: 110px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cabins)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle me-2"></i> No hay cabañas registradas
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cabins as $cabin): ?>
                                    <tr>
                                        <td class="fw-bold"><?= $cabin['id'] ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars(getCabinCover($cabin)) ?>"
                                                alt="Cabaña <?= htmlspecialchars($cabin['name_es']) ?>"
                                                class="img-thumbnail-sm"
                                                style="width: 50px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e0e0e0;">
                                        </td>
                                        <td class="fw-medium"><?= htmlspecialchars($cabin['name_es']) ?></td>
                                        <td><?= htmlspecialchars($cabin['name_en']) ?></td>
                                        <td class="fw-bold text-success">$<?= number_format($cabin['price'], 0, ',', '.') ?></td>
                                        <td class="fw-bold text-success">$<?= number_format($cabin['dolar'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= $cabin['max_people'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $cabin['status'] == 1 ? 'success' : 'danger' ?>">
                                                <?= $cabin['status'] == 1 ? 'Activa' : 'Inactiva' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="cabin_gallery.php?cabin=<?= $cabin['id'] ?>"
                                                   class="btn btn-outline-success"
                                                   title="Galería de imágenes de la cabaña">
                                                    <i class="bi bi-images"></i>
                                                    <?php $gcount = $gallery_counts[$cabin['slug']] ?? 0; ?>
                                                    <?php if ($gcount > 0): ?>
                                                        <span class="badge bg-success ms-1"><?= $gcount ?></span>
                                                    <?php endif; ?>
                                                </a>
                                                <?php if (is_file(cabinFilePath($cabin['slug']))): ?>
                                                    <a href="<?= SITE_URL ?>cabana-<?= urlencode($cabin['slug']) ?>.php"
                                                       class="btn btn-outline-secondary"
                                                       target="_blank"
                                                       title="Ver página pública">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCabinModal<?= $cabin['id'] ?>"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="?delete=<?= $cabin['id'] ?>" 
                                                   class="btn btn-outline-danger" 
                                                   onclick="return confirm('¿Eliminar esta cabaña? Se eliminará también su archivo cabana-<?= htmlspecialchars($cabin['slug']) ?>.php, sus imágenes de galería y sus fotos.')"
                                                   title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Cabaña -->
<div class="modal fade" id="addCabinModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_cabin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Cabaña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre (ES) *</label>
                                <input type="text" name="name_es" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre (EN) *</label>
                                <input type="text" name="name_en" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" name="slug" class="form-control" placeholder="ej: coigue" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio (CLP) *</label>
                                <input type="number" name="price" class="form-control" required>
                            </div>                     
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Máx. Personas *</label>
                                <input type="number" name="max_people" class="form-control" value="2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Configuración Camas (ES)</label>
                                <input type="text" name="bed_config_es" class="form-control" placeholder="ej: 1 Cama King Size">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Configuración Camas (EN)</label>
                                <input type="text" name="bed_config_en" class="form-control" placeholder="ej: 1 King size bed">
                            </div>                            
                            <div class="mb-3">
                                <label class="form-label">Precio (Dolar) *</label>
                                <input type="number" name="dolar" class="form-control" required>
                            </div>                            
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descripción (ES)</label>
                                <textarea name="description_es" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción (EN)</label>
                                <textarea name="description_en" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Equipamiento de esta cabaña -->
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">
                                    <i class="bi bi-list-check me-1"></i> Equipamiento de esta cabaña
                                </label>
                                <a href="amenities.php" class="small" target="_blank">
                                    Agregar / editar items <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                            <?php if (empty($all_amenities)): ?>
                                <div class="alert alert-warning py-2 mb-0">
                                    No hay items de equipamiento.
                                    <a href="amenities.php">Crea el primero aquí</a>.
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="amenities_present" value="1">
                                <div class="row g-2">
                                    <?php foreach ($all_amenities as $amenity): ?>
                                        <div class="col-md-4 col-6">
                                            <div class="form-check border rounded p-2 ps-4">
                                                <input class="form-check-input" type="checkbox"
                                                       name="amenities[]" value="<?= (int) $amenity['id'] ?>"
                                                       id="new_amenity_<?= (int) $amenity['id'] ?>" checked>
                                                <label class="form-check-label small" for="new_amenity_<?= (int) $amenity['id'] ?>">
                                                    <?= htmlspecialchars($amenity['name_es']) ?>
                                                    <?php if (!$amenity['status']): ?><span class="text-muted">(inactivo)</span><?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Imagen de Portada (para el index) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-image me-1"></i> Imagen de Portada
                                <small class="text-muted">(Se muestra en la tarjeta del index)</small>
                            </label>
                            <input type="file" name="image_path" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-1">
                                Si no subes portada, se usará la Imagen Principal.
                            </small>
                        </div>

                        <!-- Imagen Principal (para el slider interno de la cabaña) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-images me-1"></i> Imagen Principal
                                <small class="text-muted">(Para el slider de la cabaña)</small>
                            </label>
                            <input type="file" name="image_main" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Al guardar se crea automáticamente la página pública
                                <code>cabana-{slug}.php</code> y te llevará a subir las fotos
                                de la galería (slider interno de la cabaña).
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cabaña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Editar para cada cabaña -->
<?php foreach ($cabins as $cabin): ?>
<?php $cabin_amenity_ids = getCabinAmenityIds((int) $cabin['id']); ?>
<div class="modal fade" id="editCabinModal<?= $cabin['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_cabin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar: <?= htmlspecialchars($cabin['name_es']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $cabin['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre (ES) *</label>
                                <input type="text" name="name_es" class="form-control" value="<?= htmlspecialchars($cabin['name_es']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre (EN) *</label>
                                <input type="text" name="name_en" class="form-control" value="<?= htmlspecialchars($cabin['name_en']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($cabin['slug']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio (CLP) *</label>
                                <input type="number" name="price" class="form-control" value="<?= $cabin['price'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Máx. Personas *</label>
                                <input type="number" name="max_people" class="form-control" value="<?= $cabin['max_people'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Configuración Camas (ES)</label>
                                <input type="text" name="bed_config_es" class="form-control" value="<?= htmlspecialchars($cabin['bed_config_es']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Configuración Camas (EN)</label>
                                <input type="text" name="bed_config_en" class="form-control" value="<?= htmlspecialchars($cabin['bed_config_en']) ?>">
                            </div>                           
                            <div class="mb-3">
                                <label class="form-label">Precio Dolar *</label>
                                <input type="number" name="dolar" class="form-control" value="<?= $cabin['dolar'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= $cabin['status'] == 1 ? 'selected' : '' ?>>Activa</option>
                                    <option value="0" <?= $cabin['status'] == 0 ? 'selected' : '' ?>>Inactiva</option>
                                </select>
                            </div>
                            
                        </div>   
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descripción (ES)</label>
                                <textarea name="description_es" class="form-control" rows="3"><?= htmlspecialchars($cabin['description_es']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción (EN)</label>
                                <textarea name="description_en" class="form-control" rows="3"><?= htmlspecialchars($cabin['description_en']) ?></textarea>
                            </div>
                        </div>

                        <!-- Equipamiento de esta cabaña -->
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">
                                    <i class="bi bi-list-check me-1"></i> Equipamiento de esta cabaña
                                </label>
                                <a href="amenities.php" class="small" target="_blank">
                                    Agregar / editar items <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                            <?php if (empty($all_amenities)): ?>
                                <div class="alert alert-warning py-2 mb-0">
                                    No hay items de equipamiento.
                                    <a href="amenities.php">Crea el primero aquí</a>.
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="amenities_present" value="1">
                                <div class="row g-2">
                                    <?php foreach ($all_amenities as $amenity): ?>
                                        <?php $checked = in_array((int) $amenity['id'], $cabin_amenity_ids, true); ?>
                                        <div class="col-md-4 col-6">
                                            <div class="form-check border rounded p-2 ps-4">
                                                <input class="form-check-input" type="checkbox"
                                                       name="amenities[]" value="<?= (int) $amenity['id'] ?>"
                                                       id="edit_amenity_<?= (int) $cabin['id'] ?>_<?= (int) $amenity['id'] ?>"
                                                       <?= $checked ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="edit_amenity_<?= (int) $cabin['id'] ?>_<?= (int) $amenity['id'] ?>">
                                                    <?= htmlspecialchars($amenity['name_es']) ?>
                                                    <?php if (!$amenity['status']): ?><span class="text-muted">(inactivo)</span><?php endif; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <a href="cabin_gallery.php?cabin=<?= (int) $cabin['id'] ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-images me-1"></i> Administrar galería de esta cabaña
                                </a>
                            </div>
                        </div>

                        <!-- Imagen de Portada (para el index) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-image me-1"></i> Imagen de Portada
                                <small class="text-muted">(Se muestra en el index)</small>
                            </label>
                            <input type="file" name="image_path" class="form-control" accept="image/*">
                            <?php if (!empty($cabin['image_path'])): ?>
                                <small class="text-muted d-block mt-1">
                                    Actual: <code><?= htmlspecialchars($cabin['image_path']) ?></code>
                                </small>
                            <?php endif; ?>
                        </div>

                        <!-- Imagen Principal (para el slider interno de la cabaña) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-images me-1"></i> Imagen Principal
                                <small class="text-muted">(Para el slider de la cabaña)</small>
                            </label>
                            <input type="file" name="image_main" class="form-control" accept="image/*">
                            <?php if (!empty($cabin['image_main'])): ?>
                                <small class="text-muted d-block mt-1">
                                    Actual: <code><?= htmlspecialchars($cabin['image_main']) ?></code>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
// Genera automáticamente el slug a partir del nombre (solo al crear).
(function () {
    var modal = document.getElementById('addCabinModal');
    if (!modal) { return; }

    var nameInput = modal.querySelector('input[name="name_es"]');
    var slugInput = modal.querySelector('input[name="slug"]');
    if (!nameInput || !slugInput) { return; }

    var slugTouched = false;

    slugInput.addEventListener('input', function () { slugTouched = true; });

    nameInput.addEventListener('input', function () {
        if (slugTouched) { return; }

        slugInput.value = nameInput.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
})();
</script>

<?php include 'includes/footer.php'; ?>