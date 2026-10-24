<?php
// admin/reservations.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Cambiar estado de reserva
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $validStatus = ['pending', 'confirmed', 'cancelled', 'completed'];
    if (in_array($status, $validStatus)) {
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        header('Location: reservations.php?msg=updated');
        exit;
    }
}

// Eliminar reserva
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: reservations.php?msg=deleted');
    exit;
}

// Obtener todas las reservas
$stmt = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Reservas</h2>
                <div>
                    <span class="badge bg-warning me-2">Pendientes: <?= getPendingReservationsCount() ?></span>
                    <span class="badge bg-success">Confirmadas: <?php 
                        $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'");
                        echo $stmt->fetchColumn();
                    ?></span>
                </div>
            </div>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success">Estado de reserva actualizado correctamente.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Reserva eliminada correctamente.</div>
            <?php endif; ?>
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
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Cabaña</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Personas</th>
                                    <th>Estado</th>
                                    <th>Fecha Reserva</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?= $reservation['id'] ?></td>
                                    <td><?= htmlspecialchars($reservation['name']) ?></td>
                                    <td><?= htmlspecialchars($reservation['email']) ?></td>
                                    <td><?= htmlspecialchars($reservation['phone']) ?></td>
                                    <td><?= htmlspecialchars($reservation['cabin_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($reservation['check_in'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($reservation['check_out'])) ?></td>
                                    <td><?= $reservation['people'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $reservation['status'] == 'pending' ? 'warning' : ($reservation['status'] == 'confirmed' ? 'success' : ($reservation['status'] == 'cancelled' ? 'danger' : 'secondary')) ?>">
                                            <?= ucfirst($reservation['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($reservation['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="?status=confirmed&id=<?= $reservation['id'] ?>"><i class="bi bi-check-circle text-success"></i> Confirmar</a></li>
                                                <li><a class="dropdown-item" href="?status=cancelled&id=<?= $reservation['id'] ?>"><i class="bi bi-x-circle text-danger"></i> Cancelar</a></li>
                                                <li><a class="dropdown-item" href="?status=completed&id=<?= $reservation['id'] ?>"><i class="bi bi-check2-all text-info"></i> Completar</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="?delete=<?= $reservation['id'] ?>" onclick="return confirm('¿Eliminar esta reserva?')"><i class="bi bi-trash"></i> Eliminar</a></li>
                                            </ul>
                                        </div>
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

<?php include 'includes/footer.php'; ?>