<?php
// 
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}


// Configurar idioma
$lang = $_GET['lan'] ?? '';
if ($lang == 'es' || $lang == '') {
    $_SESSION['lan'] = 'es';
} elseif ($lang == 'en') {
    $_SESSION['lan'] = 'en';
} else {
    $_SESSION['lan'] = 'es'; // Default
}
$current_lang = $_SESSION['lan'];

// Obtener configuraciones
$site_title = getSetting('site_title_' . $current_lang);
$about_text = getSetting('about_text_' . $current_lang);
$contact_phone = getSetting('contact_phone');
$contact_email = getSetting('contact_email');
$contact_address = getSetting('contact_address');
$video_url = getSetting('about_video_url');

// Obtener slides
$slides = getSlides();

// Obtener cabañas
$cabins = getCabins();

// Obtener amenities
$amenities = getAmenities();

// Obtener imágenes de galería
$gallery_images = getGalleryImages();

$send = $_GET['send'] ?? '';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor, ingresa usuario y contraseña.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Cabañas Las Lengas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .card-body {
            padding: 2.5rem;
        }
        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.15);
            border-color: #f8c146;
            box-shadow: 0 0 0 0.25rem rgba(248, 193, 70, 0.25);
            color: #fff;
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }
        .btn-primary {
            background: #f8c146;
            border: none;
            color: #1a1a2e;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: #e6b03a;
            color: #1a1a2e;
        }
        .logo-text {
            color: #f8c146;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-house-fill logo-text" style="font-size: 3rem;"></i>
                            <h4 class="text-light mt-2">Panel de Administración</h4>
                            <small class="text-muted">Cabañas Las Lengas</small>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label text-light">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person text-light"></i></span>
                                    <input type="text" name="username" class="form-control border-start-0" placeholder="Ingresa tu usuario" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-light">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock text-light"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0" placeholder="Ingresa tu contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mt-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>