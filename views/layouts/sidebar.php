<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="fw-bold text-primary">
            <span style="color: var(--accent-color)">Neo</span>Bank
        </h3>
    </div>

    <ul class="list-unstyled components">
        <li class="mb-2">
            <a href="index.php?action=dashboard" class="<?= (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <?php if($_SESSION['rol'] === 'cliente'): ?>
            <li class="mb-2">
                <a href="index.php?action=dashboard" class="<?= (isset($_GET['action']) && $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i> Transferir
                </a>
            </li>
            <li class="mb-2">
                <a href="index.php?action=tarjetas" class="<?= (isset($_GET['action']) && $_GET['action'] == 'tarjetas') ? 'active' : '' ?>">
                    <i class="bi bi-credit-card"></i> Mis Tarjetas
                </a>
            </li>
            <li class="mb-2">
                <a href="index.php?action=reportes" class="<?= (isset($_GET['action']) && $_GET['action'] == 'reportes') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i> Estados de Cuenta
                </a>
            </li>
        <li class="mt-4 border-top pt-3">
            <a href="index.php?action=perfil" class="<?= (isset($_GET['action']) && $_GET['action'] == 'perfil') ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i> Mi Perfil
            </a>
        </li>

        <li class="mt-2">
            <a href="index.php?action=logout" class="text-danger">
                <?php else: ?>
                    <li class="mb-2">
                        <a href="index.php?action=dashboard" class="<?= (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="index.php?action=auditoria" class="<?= (isset($_GET['action']) && $_GET['action'] == 'auditoria') ? 'active' : '' ?>">
                            <i class="bi bi-shield-check"></i> Auditoría
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="index.php?action=admin_emails" class="<?= (isset($_GET['action']) && $_GET['action'] == 'admin_emails') ? 'active' : '' ?>">
                            <i class="bi bi-envelope"></i> Correos Enviados
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="index.php?action=admin_config" class="<?= (isset($_GET['action']) && $_GET['action'] == 'admin_config') ? 'active' : '' ?>">
                            <i class="bi bi-gear"></i> Configuración
                        </a>
                    </li>
                <?php endif; ?>

        <li class="mt-5">
            <a href="index.php?action=logout" class="text-danger">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</nav>