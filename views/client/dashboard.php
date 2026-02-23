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

            <!-- App Top Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="d-block text-secondary small text-uppercase" style="letter-spacing: 1px;">Hola de
                        nuevo,</span>
                    <h4 class="fw-bold mb-0 text-primary-var"><?= htmlspecialchars($usuario['nombre']) ?></h4>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-surface-2 border-theme"
                    style="width: 48px; height: 48px; color: var(--accent-primary); font-weight: 700; font-size: 1.2rem; border: 1px solid;">
                    <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/transferencias.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>