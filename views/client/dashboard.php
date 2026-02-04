<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NeoBank</title>
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
                <div class="ms-auto d-flex align-items-center">
                    <div class="text-end me-3">
                        <span class="d-block text-secondary small">Bienvenido,</span>
                        <span class="fw-bold text-primary"><?= htmlspecialchars($usuario['nombre']) ?></span>
                    </div>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                    </div>
                </div>
            </div>
        </nav>

        <div class="row">

            <div class="col-lg-4 col-md-12">
                <div class="card-neo card-balance">
                    <div class="d-flex justify-content-between">
                        <div class="card-title-neo text-white">Balance Total</div>
                        <i class="bi bi-wallet2 text-white opacity-50 fs-4"></i>
                    </div>
                    <div class="card-value-neo mt-2">
                        $ <span id="saldo-display"><?= number_format($cuenta['saldo'], 2) ?></span>
                    </div>
                    <small class="opacity-75">Cuenta: **** <?= substr($cuenta['numero_cuenta'], -4) ?></small>
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <button class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-3">
                            <i class="bi bi-plus-circle me-1"></i> Ingresar Dinero
                        </button>
                    </div>
                </div>

                <div class="card-neo">
                    <h5 class="card-title-neo mb-4 text-primary fw-bold">
                        <i class="bi bi-send me-2"></i>Transferencia Rápida
                    </h5>
                    <form id="form-transferencia">
                        <div class="mb-3">
                            <label class="text-secondary small mb-1 fw-bold">DESTINATARIO</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="destinatario" class="form-control bg-light border-0" placeholder="correo@usuario.com" style="height: 45px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-secondary small mb-1 fw-bold">MONTO</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light fw-bold text-primary">$</span>
                                <input type="number" id="monto" class="form-control bg-light border-0 fw-bold text-primary" placeholder="0.00" style="height: 45px;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" style="background: var(--accent-color); border:none;">
                            Enviar Dinero Ahora
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-md-12">
                <div class="card-neo h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-primary m-0">Movimientos recientes</h5>
                        <button class="btn btn-sm btn-light text-secondary rounded-pill">
                            Ver todos <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-neo align-middle">
                            <thead>
                            <tr>
                                <th style="width: 40%">Transacción</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (count($transacciones) > 0): ?>
                                <?php foreach ($transacciones as $t): ?>
                                    <?php
                                    $soy_destino = ($t['cuenta_destino_id'] == $cuenta['id']);
                                    $es_deposito = ($t['tipo'] == 'deposito');
                                    $es_positivo = $soy_destino || $es_deposito;

                                    $signo = $es_positivo ? '+' : '-';
                                    $clase_monto = $es_positivo ? 'text-success' : 'text-danger';

                                    // Iconos Bootstrap
                                    $icono = $es_positivo ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
                                    $bg_icono = $es_positivo ? 'bg-success' : 'bg-danger';

                                    $titulo = ucfirst($t['tipo']);
                                    if($t['tipo'] == 'transferencia') {
                                        $titulo = $es_positivo ? 'Recibido de ' . $t['cuenta_origen'] : 'Enviado a ' . $t['cuenta_destino'];
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 rounded-circle <?= $bg_icono ?> bg-opacity-10 text-<?= $es_positivo ? 'success':'danger' ?> d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                                    <i class="bi <?= $icono ?> fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($titulo) ?></div>
                                                    <small class="text-muted" style="font-size: 0.75rem">ID: #<?= str_pad($t['id'], 8, '0', STR_PAD_LEFT) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-secondary fw-normal">
                                            <?= date('d M, h:i a', strtotime($t['fecha'])) ?>
                                        </td>
                                        <td class="<?= $clase_monto ?> fw-bold">
                                            <?= $signo ?> $<?= number_format($t['monto'], 2) ?>
                                        </td>
                                        <td>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-10">
                                                        Completado
                                                    </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-secondary">Sin movimientos aún</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/transferencias.js"></script>
<script>
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>