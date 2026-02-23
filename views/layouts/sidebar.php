<nav class="app-nav">
    <!-- Desktop Logo -->
    <div class="app-nav-logo d-none d-md-flex">
        <i class="bi bi-bank me-2"></i> NeoBank
    </div>

    <!-- Navigation Items -->
    <a href="index.php?action=dashboard"
        class="app-nav-item <?= (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-grid-fill"></i>
        <span>Inicio</span>
    </a>

    <?php if ($_SESSION['rol'] === 'cliente'): ?>
        <a href="index.php?action=tarjetas"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'tarjetas') ? 'active' : '' ?>">
            <i class="bi bi-credit-card-fill"></i>
            <span>Tarjetas</span>
        </a>
        <a href="index.php?action=reportes"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'reportes') ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Historial</span>
        </a>
        <a href="index.php?action=perfil"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'perfil') ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i>
            <span>Perfil</span>
        </a>
    <?php else: ?>
        <a href="index.php?action=auditoria"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'auditoria') ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i>
            <span>Auditoría</span>
        </a>
        <a href="index.php?action=admin_emails"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'admin_emails') ? 'active' : '' ?>">
            <i class="bi bi-envelope-fill"></i>
            <span>Correos</span>
        </a>
        <a href="index.php?action=admin_config"
            class="app-nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'admin_config') ? 'active' : '' ?>">
            <i class="bi bi-gear-fill"></i>
            <span>Ajustes</span>
        </a>
    <?php endif; ?>

    <!-- Bottom Actions -->
    <div class="mt-md-auto w-100 d-flex flex-md-column justify-content-center align-items-center gap-2 mb-md-3">
        <!-- Theme Toggle -->
        <button class="theme-toggle-btn" title="Alternar Apariencia">
            <i class="bi bi-sun-fill theme-icon-light"></i>
            <i class="bi bi-moon-fill theme-icon-dark"></i>
        </button>

        <!-- Logout -->
        <a href="index.php?action=logout" class="app-nav-item text-danger mt-0 w-100">
            <i class="bi bi-box-arrow-right"></i>
            <span>Salir</span>
        </a>
    </div>
</nav>