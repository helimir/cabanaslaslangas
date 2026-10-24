<?php
// admin/includes/image_card.php
// Este archivo espera tener disponible la variable $image y $active_tab
?>
<div class="col-lg-3 col-md-4 col-6 mb-4">
    <div class="card h-100 image-card">
        <div class="card-img-wrapper">
            <!-- Badge de orden -->
            <span class="order-badge">
                <i class="bi bi-hash"></i><?= $image['order'] ?>
            </span>
            
            <!-- Badge de estado -->
            <span class="status-badge bg-<?= $image['status'] ? 'success' : 'secondary' ?>">
                <?= $image['status'] ? '✓' : '✗' ?>
            </span>
            
            <!-- Imagen -->
            <img src="<?= UPLOADS_URL . $image['image_path'] ?>" 
                 class="card-img-top" 
                 alt="<?= htmlspecialchars($image['caption_es'] ?: 'Imagen') ?>"
                 style="height: 180px; object-fit: cover; cursor: pointer;"
                 onclick="window.open('<?= UPLOADS_URL . $image['image_path'] ?>', '_blank')"
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNlZWVlZWUiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1zaXplPSIxMiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM5OTk5OTkiPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg=='">
        </div>
        
        <div class="card-body p-2">
            <h6 class="card-title mb-1 text-truncate" style="font-size: 0.85rem;"
                title="<?= htmlspecialchars($image['caption_es'] ?: 'Sin título') ?>">
                <?= htmlspecialchars($image['caption_es'] ?: 'Sin título') ?>
            </h6>
            <?php if (!empty($image['caption_en'])): ?>
                <p class="card-text small text-muted mb-0 text-truncate" style="font-size: 0.75rem;">
                    <?= htmlspecialchars($image['caption_en']) ?>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="card-footer bg-transparent p-2 d-flex justify-content-between">
            <button class="btn btn-sm btn-outline-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editImageModal<?= $image['id'] ?>"
                    title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <a href="?delete=<?= $image['id'] ?>&tab=<?= urlencode($active_tab) ?>" 
               class="btn btn-sm btn-outline-danger" 
               onclick="return confirm('¿Estás seguro de eliminar esta imagen?\n\n<?= htmlspecialchars(addslashes($image['caption_es'] ?: 'Imagen')) ?>')"
               title="Eliminar">
                <i class="bi bi-trash"></i>
            </a>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editImageModal<?= $image['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_gallery.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Imagen #<?= $image['id'] ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $image['id'] ?>">
                    <input type="hidden" name="return_tab" value="<?= htmlspecialchars($active_tab) ?>">
                    
                    <!-- Selector visual de categoría -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-folder-fill me-1 text-primary"></i>
                            Categoría *
                        </label>
                        <div class="row g-2">
                            <?php foreach ($category_info as $key => $info): ?>
                                <div class="col-md-4 col-6">
                                    <input type="radio" name="category" 
                                           id="edit_cat_<?= $image['id'] ?>_<?= $key ?>" 
                                           value="<?= $key ?>" 
                                           class="btn-check" 
                                           <?= $image['category'] === $key ? 'checked' : '' ?>
                                           required>
                                    <label class="btn btn-outline-<?= $info['color'] ?> w-100 text-start p-3" 
                                           for="edit_cat_<?= $image['id'] ?>_<?= $key ?>">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi <?= $info['icon'] ?>" style="font-size: 1.5rem;"></i>
                                            <div style="font-size: 0.8rem; line-height: 1.2;">
                                                <strong><?= htmlspecialchars($info['name_es']) ?></strong>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-flag-fill me-1"></i> Título (ES)
                                </label>
                                <input type="text" name="caption_es" class="form-control" 
                                       value="<?= htmlspecialchars($image['caption_es']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-flag-fill me-1"></i> Título (EN)
                                </label>
                                <input type="text" name="caption_en" class="form-control" 
                                       value="<?= htmlspecialchars($image['caption_en']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-image me-1"></i> Cambiar Imagen
                                </label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                                
                                <!-- Vista previa -->
                                <div class="mt-2 d-flex align-items-center gap-3 p-2 bg-light rounded">
                                    <img src="<?= UPLOADS_URL . $image['image_path'] ?>" 
                                         alt="Vista previa" 
                                         style="width: 100px; height: 70px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNlZWVlZWUiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1zaXplPSIxMiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM5OTk5OTkiPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                                    <div class="small">
                                        <div class="text-muted">Imagen actual:</div>
                                        <code class="small"><?= htmlspecialchars($image['image_path']) ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-sort-numeric-down me-1"></i> Orden
                                </label>
                                <input type="number" name="order" class="form-control" 
                                       value="<?= $image['order'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-toggle-on me-1"></i> Estado
                                </label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= $image['status'] == 1 ? 'selected' : '' ?>>
                                        ✅ Activa (visible)
                                    </option>
                                    <option value="0" <?= $image['status'] == 0 ? 'selected' : '' ?>>
                                        ❌ Inactiva (oculta)
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>