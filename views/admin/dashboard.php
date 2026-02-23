<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <script>
        const theme = localStorage.getItem('neobank-theme') || 'light';
        if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</head>

<body>

    <div class="wrapper">
        <?php include 'views/layouts/sidebar.php'; ?>

        <div id="content">

            <!-- App Top Header -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h3 class="fw-bold mb-0 text-primary-var">Panel de Control</h3>
                    <span class="text-accent small font-monospace"><i class="bi bi-shield-lock-fill me-1"></i>ADMINISTRADOR</span>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card-neo bg-surface-2 border-theme border">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center p-3 me-3 badge-primary"
                                style="color: var(--accent-purple);">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                            <div>
                                <div class="text-secondary text-uppercase small fw-bold mb-1" style="letter-spacing: 1px;">Clientes Activos</div>
                                <div class="text-primary-var fw-bold" style="font-size: 2.5rem; line-height: 1;"><?= $total_usuarios ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-neo bg-surface-2 border-theme border">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center p-3 me-3 badge-primary"
                                style="color: var(--accent-primary);">
                                <i class="bi bi-bank2 fs-3"></i>
                            </div>
                            <div>
                                <div class="text-secondary text-uppercase small fw-bold mb-1" style="letter-spacing: 1px;">Capital en Reserva</div>
                                <div class="text-accent fw-bold" style="font-size: 2.5rem; line-height: 1;">$<?= number_format($total_dinero ?? 0, 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Section -->
            <div class="d-flex justify-content-between align-items-end mb-4 mt-2">
                <h5 class="fw-bold m-0 text-primary-var">Gestión de Usuarios</h5>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newUserModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Nuevo
                </button>
            </div>

            <div class="card-neo p-0">
                <div class="table-responsive">
                    <table class="table-neo w-100 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Usuario</th>
                                <th>Cuenta</th>
                                <th>Saldo</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista_usuarios as $u): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-surface-2 border-theme border"
                                                style="width:40px; height:40px; font-weight:700; color:var(--accent-primary);">
                                                <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary-var mb-1" style="font-size: 0.95rem; line-height:1;"><?= htmlspecialchars($u['nombre']) ?></div>
                                                <small class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars($u['email']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary font-monospace small"><?= $u['numero_cuenta'] ?? 'N/A' ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary-var">$ <?= number_format($u['saldo'] ?? 0, 2) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($u['bloqueado_hasta']): ?>
                                            <span class="badge rounded-pill badge-danger">
                                                <i class="bi bi-lock-fill"></i> Bloqueado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill badge-success">
                                                Activo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-dark-glass me-1 btn-deposit"
                                            data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['nombre']) ?>"
                                            title="Depositar Fondos" style="padding: 0.4rem 0.6rem;">
                                            <i class="bi bi-cash-coin text-success"></i>
                                        </button>

                                        <button class="btn btn-sm btn-dark-glass me-1 btn-edit-user"
                                            data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['nombre']) ?>"
                                            data-email="<?= htmlspecialchars($u['email']) ?>" title="Editar Usuario" style="padding: 0.4rem 0.6rem;">
                                            <i class="bi bi-pencil-square text-accent"></i>
                                        </button>

                                        <?php if ($u['bloqueado_hasta']): ?>
                                            <button class="btn btn-sm btn-dark-glass btn-toggle-block"
                                                data-id="<?= $u['id'] ?>" title="Desbloquear Usuario" style="padding: 0.4rem 0.6rem;">
                                                <i class="bi bi-unlock text-success"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-dark-glass btn-toggle-block"
                                                data-id="<?= $u['id'] ?>" title="Bloquear Acceso" style="padding: 0.4rem 0.6rem;">
                                                <i class="bi bi-slash-circle text-danger"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="newUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-theme shadow">
                <div class="modal-header px-4 pt-4 border-0">
                    <h5 class="modal-title fw-bold text-primary-var">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form id="form-new-user">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Contraseña Temporal</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary py-3">Crear Cuenta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-theme shadow">
                <div class="modal-header px-4 pt-4 border-0">
                    <h5 class="modal-title fw-bold text-primary-var">Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form id="form-edit-user">
                        <input type="hidden" name="id" id="edit-user-id">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" id="edit-user-name" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="edit-user-email" class="form-control" required>
                        </div>
                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary py-3">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="depositModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-theme shadow">
                <div class="modal-header px-4 pt-4 border-0">
                    <h5 class="modal-title fw-bold text-primary-var"><i class="bi bi-cash-stack text-success me-2"></i>Depositar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="text-center mb-4">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-1">Destinatario</p>
                        <h4 class="fw-bold text-primary-var mb-0" id="modal-user-name">Usuario</h4>
                    </div>

                    <form id="form-deposit">
                        <input type="hidden" id="deposit-user-id" name="user_id">
                        
                        <div class="mb-4">
                            <label class="form-label text-center w-100">MONTO A INGRESAR</label>
                            <input type="number" id="deposit-amount" name="monto"
                                class="form-control fw-bold text-center text-success bg-surface-2" 
                                style="font-size: 2.5rem; height: 80px;" placeholder="0.00" min="1" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-3">
                                Confirmar Acción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>