<?php
// admin/process/save_gallery.php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gallery.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$caption_es = trim($_POST['caption_es'] ?? '');
$caption_en = trim($_POST['caption_en'] ?? '');
$category = trim($_POST['category'] ?? 'general');
$order = (int)($_POST['order'] ?? 0);
$status = (int)($_POST['status'] ?? 1);
$return_tab = $_POST['return_tab'] ?? $category;

$image_path = null;
$upload_dir = UPLOADS_PATH;

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Procesar imagen si se subió
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        header("Location: ../gallery.php?tab=" . urlencode($return_tab) . "&error=invalid_extension");
        exit;
    }
    
    if ($file['size'] > 5242880) { // 5MB
        header("Location: ../gallery.php?tab=" . urlencode($return_tab) . "&error=too_large");
        exit;
    }
    
    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
    $target_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $image_path = $filename;
    }
}

try {
    if ($id > 0) {
        // Actualizar
        if ($image_path) {
            // Eliminar imagen anterior
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($old && file_exists(UPLOADS_PATH . $old['image_path'])) {
                unlink(UPLOADS_PATH . $old['image_path']);
            }
            
            $stmt = $pdo->prepare("UPDATE gallery_images SET 
                caption_es = ?, caption_en = ?, category = ?, 
                image_path = ?, `order` = ?, status = ? 
                WHERE id = ?");
            $stmt->execute([$caption_es, $caption_en, $category, $image_path, $order, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE gallery_images SET 
                caption_es = ?, caption_en = ?, category = ?, 
                `order` = ?, status = ? 
                WHERE id = ?");
            $stmt->execute([$caption_es, $caption_en, $category, $order, $status, $id]);
        }
        
        header("Location: ../gallery.php?tab=" . urlencode($return_tab) . "&msg=updated");
        
    } else {
        // Insertar
        if (!$image_path) {
            header("Location: ../gallery.php?tab=" . urlencode($return_tab) . "&error=image_required");
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO gallery_images 
            (caption_es, caption_en, category, image_path, titulo, descripcion, `order`, status) 
            VALUES (?, ?, ?, ?, '', '', ?, ?)");
        $stmt->execute([$caption_es, $caption_en, $category, $image_path, $order, $status]);
        
        header("Location: ../gallery.php?tab=" . urlencode($category) . "&msg=added");
    }
    
} catch (PDOException $e) {
    error_log("Error al guardar imagen: " . $e->getMessage());
    header("Location: ../gallery.php?tab=" . urlencode($return_tab) . "&error=database");
}
exit;
?>