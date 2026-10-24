<?php
// admin/profile.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$user_id = $_SESSION['admin_id'];
$message = '';

// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Verificar contraseña actual
    $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($current_password, $user['password_hash'])) {
        if ($new_password === $confirm_password && strlen($new_password) >= 6) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user_id]);
            $message = '<div class="alert alert-success">Contraseña actualizada correctamente.</div>';
        } else {
            $message = '<div class="alert alert-danger">Las contraseñas no coinciden o son muy cortas (mínimo 6 caracteres).</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Contraseña actual incorrecta.</div>';
    }
}

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT username, email, role FROM admin_users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2>Mi Perfil</h2>
            <p class="text-muted">Gestiona tu información personal</p>
            <?= $message ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">Información Personal</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Usuario</th>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                        </tr>
                        <tr>
                            <th>Rol</th>
                            <td><span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'secondary' ?>"><?= ucfirst($user['role']) ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">Cambiar Contraseña</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña *</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña *</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="bi bi-key me-2"></i> Actualizar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>