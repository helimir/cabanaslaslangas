<?php
// admin/gallery.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// ============================================================
// PROCESAR ELIMINACIÓN
// ============================================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($image && file_exists(UPLOADS_PATH . $image['image_path'])) {
        unlink(UPLOADS_PATH . $image['image_path']);
    }
    $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: gallery.php?msg=deleted');
    exit;
}

// ============================================================
// DEFINIR CATEGORÍAS CON INFORMACIÓN ÚTIL
// ============================================================
// ============================================================
// DEFINIR CATEGORÍAS - SEGÚN LA BASE DE DATOS REAL
// ============================================================
$category_info = [
    'galeria' => [
        'name_es' => 'Galería General',
        'name_en' => 'General Gallery',
        'icon' => 'bi-images',
        'color' => 'primary',
        'description_es' => 'Imágenes de la sección "Galería" en la página principal',
        'description_en' => 'Images for the "Gallery" section on the main page',
        'where_used' => 'index.php#galeria'
    ],
    'excursion' => [
        'name_es' => 'Excursiones / Turismo',
        'name_en' => 'Excursions / Tourism',
        'icon' => 'bi-compass',
        'color' => 'warning',
        'description_es' => 'Imágenes de las excursiones en la sección "Turismo"',
        'description_en' => 'Excursion images in the "Tourism" section',
        'where_used' => 'index.php#turismo'
    ],
    'coihue' => [
        'name_es' => 'Cabaña Coihue',
        'name_en' => 'Coihue Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Coihue',
        'description_en' => 'Coihue Cabin Gallery',
        'where_used' => 'cabana-coihue.php'
    ],
    'cipres' => [
        'name_es' => 'Cabaña Cipres',
        'name_en' => 'Cipres Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Cipres',
        'description_en' => 'Cipres Cabin Gallery',
        'where_used' => 'cabana-cipres.php'
    ],
    'lenga' => [
        'name_es' => 'Cabaña Lenga',
        'name_en' => 'Lenga Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Lenga',
        'description_en' => 'Lenga Cabin Gallery',
        'where_used' => 'cabana-lenga.php'
    ],
    'exteriores' => [
        'name_es' => 'Exteriores',
        'name_en' => 'Exteriors',
        'icon' => 'bi-tree',
        'color' => 'info',
        'description_es' => 'Imágenes de exteriores y paisajes',
        'description_en' => 'Exterior and landscape images',
        'where_used' => 'Varias secciones'
    ],
    'general' => [
        'name_es' => 'Sin Categoría',
        'name_en' => 'Uncategorized',
        'icon' => 'bi-folder',
        'color' => 'dark',
        'description_es' => 'Imágenes sin categoría específica',
        'description_en' => 'Images without specific category',
        'where_used' => 'N/A'
    ]
];

// ============================================================
// OBTENER IMÁGENES AGRUPADAS POR CATEGORÍA
// ============================================================
$stmt = $pdo->query("SELECT * FROM gallery_images ORDER BY category, `order` ASC, id ASC");
$all_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por categoría
$images_by_category = [];
foreach ($all_images as $image) {
    $cat = $image['category'] ?: 'general';
    if (!isset($images_by_category[$cat])) {
        $images_by_category[$cat] = [];
    }
    $images_by_category[$cat][] = $image;
}

// Obtener categorías que tienen imágenes
$active_categories = array_keys($images_by_category);

// Obtener categoría activa (desde URL o la primera con imágenes)
$active_tab = $_GET['tab'] ?? ($active_categories[0] ?? 'galeria');


// ============================================================
// DETECTAR CATEGORÍAS DINÁMICAMENTE DESDE LA BD
// ============================================================

// Categorías conocidas con su información (para íconos y colores)
$known_categories = [
    'galeria' => [
        'name_es' => 'Galería General',
        'name_en' => 'General Gallery',
        'icon' => 'bi-images',
        'color' => 'primary',
        'description_es' => 'Imágenes de la sección "Galería" en la página principal',
        'where_used' => 'index.php#galeria'
    ],
    'excursion' => [
        'name_es' => 'Excursiones / Turismo',
        'name_en' => 'Excursions / Tourism',
        'icon' => 'bi-compass',
        'color' => 'warning',
        'description_es' => 'Imágenes de las excursiones en la sección "Turismo"',
        'where_used' => 'index.php#turismo'
    ],
    'coihue' => [
        'name_es' => 'Cabaña Coihue',
        'name_en' => 'Coihue Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Coihue',
        'where_used' => 'cabana-coihue.php'
    ],
    'coihuen' => [ // Categoría con typo en la BD
        'name_es' => 'Cabaña Coihue (typo)',
        'name_en' => 'Coihue Cabin (typo)',
        'icon' => 'bi-house-door',
        'color' => 'warning',
        'description_es' => 'Categoría con error tipográfico - se recomienda corregir',
        'where_used' => 'Sin uso'
    ],
    'cipres' => [
        'name_es' => 'Cabaña Cipres',
        'name_en' => 'Cipres Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Cipres',
        'where_used' => 'cabana-cipres.php'
    ],
    'lenga' => [
        'name_es' => 'Cabaña Lenga',
        'name_en' => 'Lenga Cabin',
        'icon' => 'bi-house-door',
        'color' => 'success',
        'description_es' => 'Galería de la Cabaña Lenga',
        'where_used' => 'cabana-lenga.php'
    ],
    'exteriores' => [
        'name_es' => 'Exteriores',
        'name_en' => 'Exteriors',
        'icon' => 'bi-tree',
        'color' => 'info',
        'description_es' => 'Imágenes de exteriores y paisajes',
        'where_used' => 'Varias secciones'
    ],
    'general' => [
        'name_es' => 'Sin Categoría',
        'name_en' => 'Uncategorized',
        'icon' => 'bi-folder',
        'color' => 'dark',
        'description_es' => 'Imágenes sin categoría específica',
        'where_used' => 'N/A'
    ]
];

// ============================================================
// OBTENER CATEGORÍAS REALES DE LA BD
// ============================================================
$stmt = $pdo->query("SELECT DISTINCT category, COUNT(*) as count FROM gallery_images GROUP BY category ORDER BY count DESC");
$db_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Construir el array final de categorías
$category_info = [];
foreach ($db_categories as $row) {
    $cat = $row['category'];
    if (isset($known_categories[$cat])) {
        // Categoría conocida
        $category_info[$cat] = $known_categories[$cat];
    } else {
        // Categoría desconocida - generar info automáticamente
        $category_info[$cat] = [
            'name_es' => ucfirst($cat),
            'name_en' => ucfirst($cat),
            'icon' => 'bi-folder2-open',
            'color' => 'secondary',
            'description_es' => 'Categoría personalizada',
            'where_used' => 'N/A'
        ];
    }
}


// ============================================================
// AGREGAR LAS CABAÑAS ACTIVAS COMO CATEGORÍAS DISPONIBLES
// (así se puede subir la galería de una cabaña nueva aunque
//  todavía no tenga imágenes)
// ============================================================
$stmt = $pdo->query("SELECT name_es, name_en, slug FROM cabins WHERE status = 1 ORDER BY id ASC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $cabin_row) {
    $cat = $cabin_row['slug'];
    if (!isset($category_info[$cat])) {
        $category_info[$cat] = [
            'name_es' => $cabin_row['name_es'],
            'name_en' => $cabin_row['name_en'],
            'icon' => 'bi-house-door',
            'color' => 'success',
            'description_es' => 'Galería de ' . $cabin_row['name_es'],
            'description_en' => $cabin_row['name_en'] . ' gallery',
            'where_used' => 'cabana-' . $cat . '.php'
        ];
    }
}

// Categoría de respaldo para evitar avisos
if (!isset($category_info['general'])) {
    $category_info['general'] = $known_categories['general'];
}


// ============================================================
// ESTADÍSTICAS
// ============================================================
$total_images = count($all_images);
$total_categories = count($active_categories);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
/* Estilos para las pestañas de categorías */
.category-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 15px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.category-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 50px;
    background: #f8f9fa;
    color: #555;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
}

.category-tab:hover {
    background: #e9ecef;
    color: #333;
    transform: translateY(-2px);
}

.category-tab.active {
    background: #32925A;
    color: #fff;
    border-color: #32925A;
    box-shadow: 0 5px 15px rgba(50, 146, 90, 0.3);
}

.category-tab.active .badge {
    background: #fff !important;
    color: #32925A !important;
}

.category-tab .badge {
    font-size: 0.7rem;
    padding: 3px 8px;
    border-radius: 50px;
    background: #dee2e6;
    color: #333;
}

.category-tab i {
    font-size: 1.1rem;
}

/* Card de imagen */
.image-card {
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.image-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #32925A;
}

.image-card .card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.image-card .card-img-wrapper img {
    transition: transform 0.3s ease;
}

.image-card:hover .card-img-wrapper img {
    transform: scale(1.05);
}

.image-card .order-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

.image-card .status-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 600;
    z-index: 2;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

/* Info banner de categoría */
.category-info-banner {
    background: linear-gradient(135deg, #32925A 0%, #267a4a 100%);
    color: #fff;
    padding: 20px 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.category-info-banner .icon-wrapper {
    background: rgba(255,255,255,0.2);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    flex-shrink: 0;
}

.category-info-banner h4 {
    margin: 0 0 5px;
    font-weight: 700;
}

.category-info-banner p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.category-info-banner .where-used {
    background: rgba(255,255,255,0.15);
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.8rem;
    margin-top: 8px;
    display: inline-block;
}

.category-info-banner .where-used code {
    color: #fff;
    background: transparent;
}

@media (max-width: 768px) {
    .category-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    
    .category-tab {
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    .category-info-banner {
        flex-direction: column;
        text-align: center;
    }
}


/* ============================================================
   ESTILOS MEJORADOS PARA PESTAÑAS DE CATEGORÍAS
   ============================================================ */
.category-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.category-tab {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 10px;
    background: #f8f9fa;
    color: #555;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border: 2px solid transparent;
    transition: all 0.25s ease;
    cursor: pointer;
    position: relative;
    min-width: 160px;
}

.category-tab:hover {
    background: #e9ecef;
    color: #32925A;
    border-color: #32925A;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(50, 146, 90, 0.15);
}

.category-tab.active {
    background: #32925A;
    color: #fff;
    border-color: #32925A;
    box-shadow: 0 5px 15px rgba(50, 146, 90, 0.4);
}

.category-tab.active i,
.category-tab.active span {
    color: #fff;
}

.category-tab.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #fff !important;
}

.category-tab i {
    font-size: 1.3rem;
    flex-shrink: 0;
    color: #32925A;
}

.category-tab span:not(.badge) {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.category-tab .badge {
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 50px;
    background: #dee2e6;
    color: #333;
    font-weight: 600;
    flex-shrink: 0;
}

/* Pestaña de "Ver Todas" con estilo especial */
.category-tab.all-tab {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #fff;
    border-color: #1a1a2e;
}

.category-tab.all-tab i {
    color: #f8c146;
}

.category-tab.all-tab .badge {
    background: #f8c146;
    color: #1a1a2e;
}

.category-tab.all-tab:hover,
.category-tab.all-tab.active {
    background: linear-gradient(135deg, #0f0f1a 0%, #0a0f1a 100%);
    box-shadow: 0 5px 20px rgba(26, 26, 46, 0.4);
}

@media (max-width: 768px) {
    .category-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .category-tab {
        min-width: auto;
        padding: 10px 15px;
        font-size: 0.85rem;
    }
    
    .category-tab span:not(.badge) {
        max-width: 120px;
    }
}

/* Scrollbar horizontal para móvil */
.category-tabs::-webkit-scrollbar {
    height: 6px;
}

.category-tabs::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 10px;
}

.category-tabs::-webkit-scrollbar-thumb {
    background: #32925A;
    border-radius: 10px;
}

</style>

<div class="container-fluid">
    <!-- ============================================================
    HEADER
    ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-images me-2"></i>Gestión de Galería</h2>
                    <p class="text-muted mb-0 small">
                        <span class="badge bg-primary"><?= $total_images ?> imágenes</span>
                        <span class="badge bg-success"><?= $total_categories ?> categorías</span>
                    </p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
                    <i class="bi bi-plus-circle me-2"></i> Nueva Imagen
                </button>
            </div>
            
            <!-- Mensajes de estado -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Imagen eliminada correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Imagen agregada correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> Imagen actualizada correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================
    PESTAÑAS DE CATEGORÍAS
        ============================================================ -->
    <div class="category-tabs">
        <?php foreach ($active_categories as $cat): 
            $info = $category_info[$cat] ?? $category_info['general'];
            $count = count($images_by_category[$cat]);
            $isActive = $active_tab === $cat;
        ?>
            <a href="?tab=<?= urlencode($cat) ?>" class="category-tab <?= $isActive ? 'active' : '' ?>">
                <i class="bi <?= $info['icon'] ?>"></i>
                <span><?= htmlspecialchars($info['name_es']) ?></span>
                <span class="badge"><?= $count ?></span>
            </a>
        <?php endforeach; ?>
        
        <!-- Botón para ver todas -->
        <a href="?tab=__all__" class="category-tab all-tab <?= $active_tab === '__all__' ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>Ver Todas</span>
            <span class="badge"><?= $total_images ?></span>
        </a>
    </div>

    <!-- ============================================================
    INFORMACIÓN DE LA CATEGORÍA ACTIVA
    ============================================================ -->
    <?php if ($active_tab !== '__all__' && isset($category_info[$active_tab])): 
        $info = $category_info[$active_tab];
    ?>
        <div class="category-info-banner">
            <div class="icon-wrapper">
                <i class="bi <?= $info['icon'] ?>"></i>
            </div>
            <div class="flex-grow-1">
                <h4><?= htmlspecialchars($info['name_es']) ?></h4>
                <p><?= htmlspecialchars($info['description_es']) ?></p>
                <?php if (!empty($info['where_used'])): ?>
                    <div class="where-used">
                        <i class="bi bi-link-45deg me-1"></i>
                        Se muestra en: <code><?= htmlspecialchars($info['where_used']) ?></code>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <div class="h3 mb-0"><?= count($images_by_category[$active_tab] ?? []) ?></div>
                <small>imágenes</small>
            </div>
        </div>
    <?php endif; ?>

    <!-- ============================================================
    IMÁGENES
    ============================================================ -->
    <?php 
    // Determinar qué imágenes mostrar
    if ($active_tab === '__all__') {
        // Mostrar todas las categorías
        foreach ($images_by_category as $cat => $images): 
            $info = $category_info[$cat] ?? $category_info['general'];
    ?>
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi <?= $info['icon'] ?> text-<?= $info['color'] ?>"></i>
                    <?= htmlspecialchars($info['name_es']) ?>
                    <span class="badge bg-<?= $info['color'] ?>"><?= count($images) ?></span>
                </h5>
            </div>
            <?php foreach ($images as $image): ?>
                <?php include 'includes/image_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; 
    } else {
        // Mostrar solo la categoría activa
        $images = $images_by_category[$active_tab] ?? [];
        
        if (empty($images)): ?>
            <div class="empty-state">
                <i class="bi bi-images"></i>
                <h4>No hay imágenes en esta categoría</h4>
                <p class="text-muted">Haz clic en "Nueva Imagen" para agregar la primera imagen a esta categoría.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
                    <i class="bi bi-plus-circle me-2"></i> Agregar Imagen
                </button>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($images as $image): ?>
                    <?php include 'includes/image_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif;
    } ?>
</div>

<!-- ============================================================
MODAL AGREGAR IMAGEN
============================================================ -->
<div class="modal fade" id="addImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/save_gallery.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                           id="cat_<?= $key ?>" 
                                           value="<?= $key ?>" 
                                           class="btn-check" 
                                           <?= $key === $active_tab ? 'checked' : '' ?>
                                           required>
                                    <label class="btn btn-outline-<?= $info['color'] ?> w-100 text-start p-3" for="cat_<?= $key ?>">
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
                                       placeholder="Ej: Vista del Canal Señoret">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-flag-fill me-1"></i> Título (EN)
                                </label>
                                <input type="text" name="caption_en" class="form-control" 
                                       placeholder="Ej: View of Señoret Channel">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-image me-1"></i> Imagen *
                                </label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">
                                    Formatos: JPG, PNG, WEBP (Máx. 5MB)<br>
                                    Recomendado: 1920x1080px
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-sort-numeric-down me-1"></i> Orden
                                </label>
                                <input type="number" name="order" class="form-control" value="0">
                                <small class="text-muted">Número más bajo = aparece primero</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-toggle-on me-1"></i> Estado
                                </label>
                                <select name="status" class="form-control">
                                    <option value="1">✅ Activa (visible)</option>
                                    <option value="0">❌ Inactiva (oculta)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Subir Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>