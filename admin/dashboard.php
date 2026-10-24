<?php
// admin/dashboard.php - PANEL DE CONTROL
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// ============================================================
// OBTENER ESTADÍSTICAS
// ============================================================

// Total de reservas
$stmt = $pdo->query("SELECT COUNT(*) FROM reservations");
$totalReservations = $stmt->fetchColumn();

// Reservas pendientes
$stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
$pendingReservations = $stmt->fetchColumn();

// Reservas confirmadas
$stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'");
$confirmedReservations = $stmt->fetchColumn();

// Total cabañas activas
$stmt = $pdo->query("SELECT COUNT(*) FROM cabins WHERE status = 1");
$totalCabins = $stmt->fetchColumn();

// Total imágenes en galería
$stmt = $pdo->query("SELECT COUNT(*) FROM gallery_images WHERE status = 1");
$totalGallery = $stmt->fetchColumn();

// Total amenities
$stmt = $pdo->query("SELECT COUNT(*) FROM amenities WHERE status = 1");
$totalAmenities = $stmt->fetchColumn();

// Últimas 5 reservas
$stmt = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 5");
$latestReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reservas por mes (últimos 6 meses)
$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('M', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $monthlyStats[$monthName] = $stmt->fetchColumn();
}

// Calcular ingresos estimados (si hay campo price en reservations o se calcula desde cabins)
$stmt = $pdo->query("
    SELECT SUM(c.price) as total 
    FROM reservations r 
    LEFT JOIN cabins c ON r.cabin_id = c.id 
    WHERE r.status IN ('confirmed', 'completed')
");
$totalRevenue = $stmt->fetchColumn() ?: 0;

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="container-fluid">
    <!-- ============================================================
    TÍTULO
    ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <div class="me-3 p-3 bg-primary bg-opacity-10 rounded-circle">
                    <i class="bi bi-grid-1x2-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h2 class="mb-0">Dashboard</h2>
                    <p class="text-muted mb-0">Resumen general de tu sitio web</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    TARJETAS DE ESTADÍSTICAS
    ============================================================ -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Reservas</h6>
                            <h2 class="mb-0 fw-bold"><?= number_format($totalReservations) ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h2 class="mb-0 fw-bold text-warning"><?= number_format($pendingReservations) ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Confirmadas</h6>
                            <h2 class="mb-0 fw-bold text-success"><?= number_format($confirmedReservations) ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Cabañas Activas</h6>
                            <h2 class="mb-0 fw-bold"><?= number_format($totalCabins) ?></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    SEGUNDA FILA DE ESTADÍSTICAS
    ============================================================ -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Galería</h6>
                            <h2 class="mb-0 fw-bold"><?= number_format($totalGallery) ?></h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-images text-secondary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Equipamiento</h6>
                            <h2 class="mb-0 fw-bold"><?= number_format($totalAmenities) ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-list-check text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Ingresos Estimados</h6>
                            <h2 class="mb-0 fw-bold text-success">$<?= number_format($totalRevenue, 0, ',', '.') ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tasa de Conversión</h6>
                            <h2 class="mb-0 fw-bold">
                                <?php 
                                $conversion = $totalReservations > 0 ? round(($confirmedReservations / $totalReservations) * 100) : 0;
                                echo $conversion . '%';
                                ?>
                            </h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-graph-up-arrow text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    GRÁFICO DE RESERVAS POR MES
    ============================================================ -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Reservas por Mes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $maxValue = max($monthlyStats) ?: 1;
                        foreach ($monthlyStats as $month => $count): 
                            $percentage = ($count / $maxValue) * 100;
                            $color = $count > 0 ? 'primary' : 'secondary';
                        ?>
                            <div class="col-2 text-center">
                                <div class="mb-1 small text-muted"><?= $month ?></div>
                                <div style="height: 80px; display: flex; align-items: flex-end; justify-content: center;">
                                    <div style="width: 30px; height: <?= max(5, $percentage) ?>%; background: <?= $color == 'primary' ? '#f8c146' : '#ddd' ?>; border-radius: 4px 4px 0 0; min-height: 5px;"></div>
                                </div>
                                <div class="small fw-bold"><?= $count ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ============================================================
        ACCIONES RÁPIDAS
        ============================================================ -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2 text-warning"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="reservations.php" class="btn btn-outline-primary d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-calendar-check me-2"></i> Ver Reservas</span>
                            <span class="badge bg-primary"><?= $pendingReservations ?></span>
                        </a>
                        <a href="cabins.php" class="btn btn-outline-success d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-building me-2"></i> Gestionar Cabañas</span>
                            <span class="badge bg-success"><?= $totalCabins ?></span>
                        </a>
                        <a href="gallery.php" class="btn btn-outline-info d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-images me-2"></i> Gestionar Galería</span>
                            <span class="badge bg-info"><?= $totalGallery ?></span>
                        </a>
                        <a href="settings.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-gear-fill me-2"></i> Configuración</span>
                            <span class="badge bg-secondary"><i class="bi bi-gear"></i></span>
                        </a>
                        <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-warning d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-eye me-2"></i> Ver Sitio Web</span>
                            <span class="badge bg-warning"><i class="bi bi-box-arrow-up-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    ÚLTIMAS RESERVAS
    ============================================================ -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Últimas Reservas</h5>
                    <a href="reservations.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>Cabaña</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestReservations)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle me-2"></i> No hay reservas registradas
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($latestReservations as $reservation): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($reservation['name']) ?></td>
                                        <td><?= htmlspecialchars($reservation['email']) ?></td>
                                        <td><?= htmlspecialchars($reservation['cabin_name'] ?? 'N/A') ?></td>
                                        <td><?= date('d/m/Y', strtotime($reservation['check_in'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($reservation['check_out'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $reservation['status'] == 'pending' ? 'warning' : ($reservation['status'] == 'confirmed' ? 'success' : ($reservation['status'] == 'cancelled' ? 'danger' : 'secondary')) ?>">
                                                <?= ucfirst($reservation['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($reservation['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>