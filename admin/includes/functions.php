<?php
require_once __DIR__ . '/config.php';

function getSlides() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM slider_images WHERE status = 1 ORDER BY `order` ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCabins() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM cabins WHERE status = 1 ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCabinBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM cabins WHERE slug = ? AND status = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAmenities() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM amenities WHERE status = 1 ORDER BY `order` ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGalleryImages($category = 'general') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE category = ? AND status = 1 ORDER BY `order` ASC");
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//function getPendingReservationsCount() {
//    global $pdo;
//    $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
//    return (int)$stmt->fetchColumn();
//}

//function getSetting($key) {
//    global $pdo;
//    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
//    $stmt->execute([$key]);
//    return $stmt->fetch(PDO::FETCH_COLUMN);
//}

function getPrice($price) {
    return number_format($price, 0, ',', '.');
}

function getStatusBadge($status) {
    $colors = [
        'pending' => 'warning',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'completed' => 'secondary'
    ];
    $color = $colors[$status] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . ucfirst($status) . '</span>';
}

// Obtener imágenes de galería por categoría (slug de la cabaña)
function getGalleryImagesByCategory($category) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE category = ? AND status = 1 ORDER BY `order` ASC");
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>