<?php
// admin/process/save_slider.php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name_es = trim($_POST['name_es'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $description_es = trim($_POST['description_es'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $order = (int)($_POST['order'] ?? 0);
    $status = (int)($_POST['status'] ?? 1);
    
    if (empty($name_es) || empty($name_en)) {
        header('Location: ../amenities.php?error=validation');
        exit;
    }
    
    $image_path = null;
    
    // Procesar imagen si se subió
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = UPLOADS_PATH . 'amenities/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'amenity_' . time() . '_' . uniqid() . '.' . $ext;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = 'amenities/' . $filename;
        }
    }
    
    if ($id > 0) {
        // Actualizar
        if ($image_path) {
            $stmt = $pdo->prepare("SELECT image_path FROM amenities WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($old && $old['image_path'] && file_exists(UPLOADS_PATH . $old['image_path'])) {
                unlink(UPLOADS_PATH . $old['image_path']);
            }
            $stmt = $pdo->prepare("UPDATE amenities SET name_es = ?, name_en = ?, description_es = ?, description_en = ?, icon = ?, image_path = ?, `order` = ?, status = ? WHERE id = ?");
            $stmt->execute([$name_es, $name_en, $description_es, $description_en, $icon, $image_path, $order, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE amenities SET name_es = ?, name_en = ?, description_es = ?, description_en = ?, icon = ?, `order` = ?, status = ? WHERE id = ?");
            $stmt->execute([$name_es, $name_en, $description_es, $description_en, $icon, $order, $status, $id]);
        }
        header('Location: ../amenities.php?msg=saved');
    } else {
        // Insertar
        $stmt = $pdo->prepare("INSERT INTO amenities (name_es, name_en, description_es, description_en, icon, image_path, `order`, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name_es, $name_en, $description_es, $description_en, $icon, $image_path, $order, $status]);
        header('Location: ../amenities.php?msg=saved');
    }
    exit;
}