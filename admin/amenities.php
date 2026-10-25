<?php
// admin/amenities.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Procesar eliminación
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM amenities WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($item && $item['image_path'] && file_exists(UPLOADS_PATH . $item['image_path'])) {
        unlink(UPLOADS_PATH . $item['image_path']);
    }
    $stmt = $pdo->prepare("DELETE FROM amenities WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: amenities.php?msg=deleted');
    exit;
}

// Obtener todos los amenities
$stmt = $pdo->query("SELECT * FROM amenities ORDER BY `order` ASC");
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Equipamiento</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAmenityModal">
                    <i class="bi bi-plus-circle me-2"></i> Nuevo Item
                </button>
            </div>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Item eliminado correctamente.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
                <div class="alert alert-success">Item guardado correctamente.</div>
            <?php endif; ?>
            <div class="alert alert-info d-flex align-items-start">
                <i class="bi bi-info-circle me-2 mt-1"></i>
                <div>
                    Estos items son el catálogo general de equipamiento. Para elegir cuáles tiene cada cabaña,
                    entra a <a href="cabins.php" class="alert-link">Cabañas</a> → <strong>Editar</strong> y marca
                    el equipamiento correspondiente.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Icono</th>
                                    <th>Imagen</th>
                                    <th>Nombre (ES)</th>
                                    <th>Nombre (EN)</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($amenities as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= $item['icon'] ? '<i class="' . htmlspecialchars($item['icon']) . '"></i>' : '-' ?></td>
                                    <td>
                                        <?php if ($item['image_path']): ?>
                                            <img src="<?= UPLOADS_URL . $item['image_path'] ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['name_es']) ?></td>
                                    <td><?= htmlspecialchars($item['name_en']) ?></td>
                                    <td><?= $item['order'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $item['status'] == 1 ? 'success' : 'danger' ?>">
                                            <?= $item['status'] == 1 ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAmenityModal<?= $item['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este item?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="addAmenityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_amenity.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Item de Equipamiento</h5>
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
                                <label class="form-label">Icono (Bootstrap Icons)</label>
                                <input type="text" name="icon" class="form-control" placeholder="ej: bi bi-wifi">
                                <small>https://icons.getbootstrap.com/</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted"><?= htmlspecialchars(image_size_text('amenity')) ?>. <?= htmlspecialchars(image_max_size_text()) ?></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" name="order" class="form-control" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descripción (ES)</label>
                                <textarea name="description_es" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción (EN)</label>
                                <textarea name="description_en" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Editar -->
<?php foreach ($amenities as $item): ?>
<div class="modal fade" id="editAmenityModal<?= $item['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_amenity.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre (ES) *</label>
                                <input type="text" name="name_es" class="form-control" value="<?= htmlspecialchars($item['name_es']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre (EN) *</label>
                                <input type="text" name="name_en" class="form-control" value="<?= htmlspecialchars($item['name_en']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icono (Bootstrap Icons)</label>
                                <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($item['icon']) ?>" placeholder="ej: bi bi-wifi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted"><?= htmlspecialchars(image_size_text('amenity')) ?>. <?= htmlspecialchars(image_max_size_text()) ?></small>
                                <?php if ($item['image_path']): ?>
                                    <small class="text-muted">Imagen actual: <?= $item['image_path'] ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" name="order" class="form-control" value="<?= $item['order'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?= $item['status'] == 1 ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= $item['status'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descripción (ES)</label>
                                <textarea name="description_es" class="form-control" rows="2"><?= htmlspecialchars($item['description_es']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción (EN)</label>
                                <textarea name="description_en" class="form-control" rows="2"><?= htmlspecialchars($item['description_en']) ?></textarea>
                            </div>
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

<?php include 'includes/footer.php'; ?>