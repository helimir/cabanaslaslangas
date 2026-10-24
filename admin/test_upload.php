<?php
// admin/test_upload.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

echo "<h1>Prueba de Subida de PDF</h1>";

$upload_dir = __DIR__ . '/uploads/pdfs/';

echo "<h2>Información de la carpeta:</h2>";
echo "<p>Ruta: <code>$upload_dir</code></p>";

if (file_exists($upload_dir)) {
    echo "<p style='color:green'>✅ La carpeta existe</p>";
    echo "<p>Permisos: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "</p>";
    echo "<p>Escribible: " . (is_writable($upload_dir) ? '✅ Sí' : '❌ No') . "</p>";
    
    echo "<h3>Archivos en la carpeta:</h3>";
    $files = scandir($upload_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $size = filesize($upload_dir . $file) / 1024 / 1024;
            echo "<li>$file (" . round($size, 2) . " MB)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>❌ La carpeta NO existe</p>";
}

// Probar subida simulada
echo "<h2>Prueba de subida:</h2>";
echo "<form method='POST' enctype='multipart/form-data' action='test_upload.php'>";
echo "<input type='file' name='test_pdf' accept='.pdf'>";
echo "<button type='submit' name='test_upload'>Probar Subida</button>";
echo "</form>";

if (isset($_POST['test_upload']) && isset($_FILES['test_pdf'])) {
    $file = $_FILES['test_pdf'];
    echo "<h3>Resultado:</h3>";
    echo "<pre>";
    print_r($file);
    echo "</pre>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $target = $upload_dir . 'test_' . time() . '.pdf';
        if (move_uploaded_file($file['tmp_name'], $target)) {
            echo "<p style='color:green'>✅ Archivo guardado: " . basename($target) . "</p>";
        } else {
            echo "<p style='color:red'>❌ Error al mover el archivo</p>";
        }
    } else {
        echo "<p style='color:red'>❌ Error en la subida: " . $file['error'] . "</p>";
    }
}
?>