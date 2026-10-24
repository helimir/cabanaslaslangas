<?php
// Verificar autenticación
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    // Ruta absoluta desde admin/process/ o admin/ según corresponda
    $login_url = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/process/') ? '../index.php' : 'index.php';
    header('Location: ' . $login_url);
    exit;
}
?>