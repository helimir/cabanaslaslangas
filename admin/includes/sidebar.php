<!-- Sidebar -->
<nav id="sidebar" class="bg-dark text-white">
    <div class="sidebar-header">
        <h4 class="fw-bold"><i class="bi bi-house-fill text-warning"></i> Admin</h4>
        <small>Cabañas Las Lengas</small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                <i class="bi bi-grid-1x2-fill"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <!--<li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'slider.php' ? 'active' : '' ?>" href="slider.php">
                <i class="bi bi-images"></i> <span class="nav-text">Slider</span>
            </a>
        </li>-->
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cabins.php' ? 'active' : '' ?>" href="cabins.php">
                <i class="bi bi-building"></i> <span class="nav-text">Cabañas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>" href="gallery.php">
                <i class="bi bi-images"></i> <span class="nav-text">Galería</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'amenities.php' ? 'active' : '' ?>" href="amenities.php">
                <i class="bi bi-list-check"></i> <span class="nav-text">Equipamiento</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>" href="reservations.php">
                <i class="bi bi-calendar-check"></i> <span class="nav-text">Reservas</span>
                <span class="badge bg-danger rounded-pill"><?= getPendingReservationsCount() ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>" href="settings.php">
                <i class="bi bi-gear-fill"></i> <span class="nav-text">Configuración</span>
            </a>
        </li>
        <li class="nav-item mt-4 pt-3 border-top border-secondary">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manual.php' ? 'active' : '' ?>" href="manual.php">
                <i class="bi bi-book me-2"></i> <span class="nav-text">Manual</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" href="users.php">
                <i class="bi bi-people-fill"></i> <span class="nav-text">Usuarios</span>
            </a>
        </li>
        <li class="nav-item mt-4 pt-3 border-top border-secondary">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>" href="profile.php">
                <i class="bi bi-person-circle"></i> <span class="nav-text">Mi Perfil</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> <span class="nav-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Contenido principal -->
<div id="main-content">
    <!-- Top Bar -->
    <nav class="navbar navbar-light bg-white shadow-sm rounded-3 p-3 mb-4">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary" id="toggle-sidebar" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-text ms-3 fw-bold">Bienvenido, <?= getCurrentUser() ?></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye"></i> Ver Web
                </a>
                <span class="badge bg-success bg-opacity-25 text-success d-flex align-items-center px-3 py-2">
                    <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i> Online
                </span>
            </div>
        </div>
    </nav>