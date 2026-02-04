<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Seguridad - NeoBank</title>
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
                <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none"><i class="bi bi-list"></i></button>
                <h4 class="ms-3 fw-bold text-danger mb-0">
                    <i class="bi bi-shield-exclamation me-2"></i>Logs de Seguridad
                </h4>
            </div>
        </nav>

        <div class="card-neo">
            <div class="alert alert-light border-start border-4 border-danger shadow-sm">
                <i class="bi bi-info-circle-fill text-danger me-2"></i>
                Este registro muestra los últimos 50 intentos de acceso al sistema. Revise periódicamente para detectar IPs sospechosas.
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Usuario / Email</th>
                        <th>IP Origen</th>
                        <th>Resultado</th>
                        <th>Detalle</th>
                    </tr>
                    </thead>
                    <tbody class="font-monospace" style="font-size: 0.9rem;">
                    <?php foreach ($logs as $log): ?>
                        <?php
                        // Estilos visuales según éxito o fallo
                        $bg_row = $log['exitoso'] ? '' : 'bg-danger bg-opacity-10';
                        $icon = $log['exitoso'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $texto_resultado = $log['exitoso'] ? 'EXITOSO' : 'FALLIDO';
                        ?>
                        <tr class="<?= $bg_row ?>">
                            <td><?= date('d/m/Y H:i:s', strtotime($log['fecha'])) ?></td>
                            <td>
                                <?php if($log['nombre']): ?>
                                    <strong><?= htmlspecialchars($log['nombre']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($log['email_intentado']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Desconocido</span><br>
                                    <small><?= htmlspecialchars($log['email_intentado']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($log['ip_address']) ?>
                                        </span>
                            </td>
                            <td>
                                <?= $icon ?> <span class="fw-bold ms-1" style="font-size:0.8rem"><?= $texto_resultado ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($log['detalle']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>