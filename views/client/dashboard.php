<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NeoBank</title>
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

            <nav class="navbar navbar-expand-lg navbar-light bg-transparent mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="text-end me-3">
                            <span class="d-block text-secondary small">Bienvenido,</span>
                            <span class="fw-bold text-primary"><?= htmlspecialchars($usuario['nombre']) ?></span>
                        </div>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                        </div>
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none">
                    <i class="bi bi-list"></i>
                </button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-2 d-none d-sm-inline">Hola,</span>
                    <span class="fw-bold text-primary"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
                    <div class="rounded-circle bg-primary text-white ms-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
                    </div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-surface-2 border-theme"
                    style="width: 48px; height: 48px; color: var(--accent-primary); font-weight: 700; font-size: 1.2rem; border: 1px solid;">
                    <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                </div>
            </div>

            <!-- Massive Hero Balance -->
            <div class="hero-balance-section">
                <div class="hero-balance-label">Balance Total</div>
                <div class="hero-balance-amount">
                    $<span id="saldo-display"><?= number_format($cuenta['saldo'], 2) ?></span>
                </div>
                <div class="text-secondary small font-monospace">
                    **** **** **** <?= substr($cuenta['numero_cuenta'], -4) ?>
                </div>

                <!-- Action Pills -->
                <div class="action-grid mt-4">
                    <a href="#" class="action-pill" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class="bi bi-send-fill"></i>
                        <span>Enviar</span>
                    </a>
                    <a href="#" class="action-pill">
                        <i class="bi bi-plus-lg"></i>
                        <span>Ingresar</span>
                    </a>
                    <a href="index.php?action=tarjetas" class="action-pill">
                        <i class="bi bi-credit-card-2-front-fill"></i>
                        <span>Tarjetas</span>
                    </a>
                    <a href="#" class="action-pill">
                        <i class="bi bi-three-dots"></i>
                        <span>Más</span>
                    </a>
                </div>
            </div>

            <!-- Transactions Edge-to-Edge List -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <h5 class="fw-bold m-0 text-primary-var">Últimos Movimientos</h5>
                    <a href="index.php?action=reportes" class="text-accent text-decoration-none small fw-bold">Ver
                        todos</a>
                </div>

                <div class="card-neo p-0">
                    <div class="list-neo">
                        <?php if (count($transacciones) > 0): ?>
                            <?php foreach ($transacciones as $t): ?>
                                <?php
                                $soy_destino = ($t['cuenta_destino_id'] == $cuenta['id']);
                                $es_deposito = ($t['tipo_transaccion'] == 'deposito');
                                $es_positivo = $soy_destino || $es_deposito;

                                $signo = $es_positivo ? '+' : '-';
                                $clase_monto = $es_positivo ? 'text-success' : 'text-primary-var';
                                $icon_dir = $es_positivo ? 'down' : 'up';
                                $icon_class = $es_positivo ? 'bi-arrow-down-left' : 'bi-arrow-up-right';

                                $titulo = ucfirst($t['tipo_transaccion']);
                                $subtitulo = date('d M, h:i a', strtotime($t['fecha_transaccion']));

                                if ($t['tipo_transaccion'] == 'transferencia') {
                                    $titulo = $es_positivo ? 'De: ' . $t['cuenta_origen'] : 'Para: ' . $t['cuenta_destino'];
                                }
                                ?>
                                <div class="list-item px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="tx-icon <?= $icon_dir ?> me-3">
                                            <i class="bi <?= $icon_class ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary-var mb-1"><?= htmlspecialchars($titulo) ?></div>
                                            <div class="text-secondary small"><?= $subtitulo ?></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="tx-amount <?= $clase_monto ?>">
                                            <?= $signo ?>$<?= number_format($t['monto'], 2) ?>
                                        </div>
                                        <div class="text-secondary small" style="font-size: 0.7rem;">Completado</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox fs-1 mb-2 d-block opacity-50"></i>
                                Sin movimientos aún
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Quick Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-theme shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary-var">Transferencia Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-transferencia">
                        <div class="mb-4">
                            <label class="form-label">DESTINATARIO (CORREO)</label>
                            <input type="email" id="destinatario" class="form-control" placeholder="usuario@correo.com"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">MONTO</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="monto" class="form-control fs-4 fw-bold text-accent"
                                    placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary">
                                Enviar Dinero
                            </button>
                        </div>
                    </form>
                </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card-neo bg-primary text-white" style="background: linear-gradient(135deg, var(--accent-color) 0%, #4facfe 100%);">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-0 opacity-75">Saldo Disponible</p>
                                <h1 class="fw-bold mb-0 display-5">$ <?= number_format($cuenta['saldo'] ?? 0, 2) ?></h1>
                            </div>
                            <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                        </div>
                        <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-end">
                            <div>
                                <small class="d-block opacity-75">Tu cuenta CLABE</small>
                                <span class="font-monospace fs-5"><?= chunk_split($cuenta['numero_cuenta'] ?? '0000', 4, ' ') ?></span>
                            </div>
                            <div>
                                <span class="badge bg-white text-primary rounded-pill px-3">Cuenta Activa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-neo">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold text-primary m-0">Últimos Movimientos</h5>

                <form action="index.php" method="GET" class="d-flex">
                    <input type="hidden" name="action" value="dashboard">
                    <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 border text-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                        <input type="text" name="q" class="form-control form-control-sm border-start-0 bg-light"
                               placeholder="Buscar..."
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

                        <?php if(!empty($_GET['q'])): ?>
                            <a href="index.php?action=dashboard" class="btn btn-sm btn-link text-danger border" title="Limpiar filtro" style="text-decoration: none;">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>

                        <button class="btn btn-sm btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-neo align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Concepto</th>
                        <th>Fecha</th>
                        <th class="text-end">Monto</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($transacciones)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt d-block fs-1 mb-2 opacity-50"></i>
                                No se encontraron movimientos<?= !empty($_GET['q']) ? ' con esa búsqueda' : '' ?>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transacciones as $t): ?>
                            <?php
                            // Lógica visual: ¿Es ingreso o egreso?
                            $es_ingreso = ($t['cuenta_destino_id'] == $cuenta['id']);
                            $color = $es_ingreso ? 'text-success' : 'text-danger';
                            $icono = $es_ingreso ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
                            $bg_icono = $es_ingreso ? 'bg-success' : 'bg-danger';
                            $signo = $es_ingreso ? '+' : '-';
                            ?>
                            <tr>
                                <td>
                                    <div class="rounded-circle <?= $bg_icono ?> bg-opacity-10 <?= $color ?> d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi <?= $icono ?>"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?= $es_ingreso ? 'Depósito / Transferencia Recibida' : 'Transferencia Enviada' ?>
                                    </div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;">
                                        <?= htmlspecialchars($t['descripcion']) ?>
                                    </small>
                                </td>
                                <td class="text-secondary small">
                                    <?= date('d M Y, h:i a', strtotime($t['fecha'])) ?>
                                </td>
                                <td class="text-end fw-bold <?= $color ?>">
                                    <?= $signo ?> $<?= number_format($t['monto'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/transferencias.js"></script>
    <script>
        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Sidebar Móvil
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>

</html>