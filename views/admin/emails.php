<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Servidor de Correos - NeoBank</title>
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
                    <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none"><i
                            class="bi bi-list"></i></button>
                    <h4 class="ms-3 fw-bold text-primary-var mb-0">
                        <i class="bi bi-envelope-paper me-2"></i>Historial de Correos Enviados
                    </h4>
                </div>
            </nav>

            <div class="card-neo bg-surface-1 border-theme border p-0">
                <div class="table-responsive">
                    <table class="table-neo w-100 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 5%">Estado</th>
                                <th style="width: 20%">Destinatario</th>
                                <th style="width: 55%">Asunto y Mensaje</th>
                                <th style="width: 20%" class="text-end pe-4">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($emails as $email): ?>
                                <tr>
                                    <td class="text-center ps-4 py-3">
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="Enviado"></i>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary-var mb-1">
                                            <?= htmlspecialchars($email['destinatario']) ?>
                                        </div>
                                        <small class="text-secondary">SMTP Local</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary-var mb-1"><?= htmlspecialchars($email['asunto']) ?>
                                        </div>
                                        <small class="text-secondary text-truncate d-block" style="max-width: 400px;">
                                            <?= strip_tags($email['cuerpo']) ?>
                                        </small>
                                    </td>
                                    <td class="text-end text-secondary pe-4">
                                        <?= date('d M, H:i', strtotime($email['fecha_envio'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($emails)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-secondary">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        No se han enviado correos aún.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>