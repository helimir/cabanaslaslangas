<?php
// admin/process/save_slider.php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title_es = trim($_POST['title_es'] ?? '');
    $title_en = trim($_POST['title_en'] ?? '');
    $order = (int)($_POST['order'] ?? 0);
    $status = (int)($_POST['status'] ?? 1);
    
    $image_path = null;
    
    // Procesar imagen si se subió
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = UPLOADS_PATH;
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'slide_' . time() . '_' . uniqid() . '.' . $ext;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $filename;
        }
    }
    
    if ($id > 0) {
        // Actualizar
        if ($image_path) {
            // Eliminar imagen anterior
            $stmt = $pdo->prepare("SELECT image_path FROM slider_images WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($old && file_exists(UPLOADS_PATH . $old['image_path'])) {
                unlink(UPLOADS_PATH . $old['image_path']);
            }
            $stmt = $pdo->prepare("UPDATE slider_images SET title_es = ?, title_en = ?, image_path = ?, `order` = ?, status = ? WHERE id = ?");
            $stmt->execute([$title_es, $title_en, $image_path, $order, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE slider_images SET title_es = ?, title_en = ?, `order` = ?, status = ? WHERE id = ?");
            $stmt->execute([$title_es, $title_en, $order, $status, $id]);
        }
        header('Location: ../slider.php?msg=updated');
    } else {
        // Insertar
        if ($image_path) {
            $stmt = $pdo->prepare("INSERT INTO slider_images (title_es, title_en, image_path, `order`, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title_es, $title_en, $image_path, $order, $status]);
            header('Location: ../slider.php?msg=added');
        } else {
            header('Location: ../slider.php?error=image_required');
        }
    }
    exit;
}