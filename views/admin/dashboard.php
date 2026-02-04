<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <?php include 'views/layouts/sidebar.php'; ?>

    <div id="content">

        <nav class="navbar navbar-expand-lg navbar-light bg-transparent mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
                    <i class="bi bi-list"></i>
                </button>
                <div class="ms-auto">
                        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi bi-shield-lock me-1"></i> MODO ADMINISTRADOR
                        </span>
                </div>
            </div>
        </nav>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card-neo" style="border-left: 5px solid var(--accent-color);">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center p-3 me-3">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="card-title-neo mb-0">Clientes Activos</div>
                            <div class="card-value-neo"><?= $total_usuarios ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-neo" style="border-left: 5px solid #05CD99;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center p-3 me-3">
                            <i class="bi bi-bank2 fs-3"></i>
                        </div>
                        <div>
                            <div class="card-title-neo mb-0">Capital en Reserva</div>
                            <div class="card-value-neo">$ <?= number_format($total_dinero ?? 0, 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-neo">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-primary m-0">Gestión de Usuarios</h5>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newUserModal">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Cliente
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-neo align-middle">
                    <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Cuenta Principal</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lista_usuarios as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width:40px; height:40px; font-weight:bold; color:var(--text-secondary);">
                                        <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($u['nombre']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary font-monospace"><?= $u['numero_cuenta'] ?? 'N/A' ?></span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">$ <?= number_format($u['saldo'] ?? 0, 2) ?></span>
                            </td>
                            <td>
                                <?php if($u['bloqueado_hasta']): ?>
                                    <span class="badge-status badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                            <i class="bi bi-lock-fill"></i> Bloqueado
                                        </span>
                                <?php else: ?>
                                    <span class="badge-status badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                            Activo
                                        </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-success text-white border me-1 btn-deposit"
                                        data-id="<?= $u['id'] ?>"
                                        data-name="<?= htmlspecialchars($u['nombre']) ?>"
                                        title="Depositar Fondos">
                                    <i class="bi bi-cash-coin"></i>
                                </button>

                                <button class="btn btn-sm btn-light text-primary border me-1 btn-edit-user"
                                        data-id="<?= $u['id'] ?>"
                                        data-name="<?= htmlspecialchars($u['nombre']) ?>"
                                        data-email="<?= htmlspecialchars($u['email']) ?>"
                                        title="Editar Usuario">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <?php if($u['bloqueado_hasta']): ?>
                                    <button class="btn btn-sm btn-light text-success border btn-toggle-block" data-id="<?= $u['id'] ?>" title="Desbloquear Usuario">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light text-danger border btn-toggle-block" data-id="<?= $u['id'] ?>" title="Bloquear Acceso">
                                        <i class="bi bi-slash-circle"></i>
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

<div class="modal fade" id="newUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-new-user">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NOMBRE COMPLETO</label>
                        <input type="text" name="nombre" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">CORREO ELECTRÓNICO</label>
                        <input type="email" name="email" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">CONTRASEÑA TEMPORAL</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required minlength="6">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">Crear Usuario y Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-edit-user">
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NOMBRE COMPLETO</label>
                        <input type="text" name="nombre" id="edit-user-name" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">CORREO ELECTRÓNICO</label>
                        <input type="email" name="email" id="edit-user-email" class="form-control bg-light border-0" required>
                    </div>
                    <div class="alert alert-info small border-0 bg-info bg-opacity-10">
                        <i class="bi bi-info-circle me-1"></i> Para cambiar la contraseña o bloquear, usa las otras opciones del panel.
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning rounded-pill py-2 fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>Depositar Fondos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary">Estás depositando a la cuenta de:</p>
                <h4 class="fw-bold text-dark mb-4" id="modal-user-name">Usuario</h4>

                <form id="form-deposit">
                    <input type="hidden" id="deposit-user-id" name="user_id">

                    <label class="form-label text-muted small fw-bold">MONTO A INGRESAR</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light border-0 fw-bold text-success">$</span>
                        <input type="number" id="deposit-amount" name="monto" class="form-control fw-bold fs-4 text-success border-0 bg-light" placeholder="0.00" min="1" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success py-3 fw-bold rounded-pill">
                            Confirmar Depósito
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Sidebar Móvil
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
<script src="assets/js/admin.js"></script>
</body>
</html>