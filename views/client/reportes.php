<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estados de Cuenta - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
<div class="wrapper">
    <?php include 'views/layouts/sidebar.php'; ?>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary d-md-none"><i class="bi bi-list"></i></button>
                <h4 class="ms-3 fw-bold text-primary mb-0">Estados de Cuenta</h4>
            </div>
        </nav>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card-neo p-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <form class="d-flex gap-3 align-items-center" method="GET" action="index.php">
                        <input type="hidden" name="action" value="reportes">

                        <select name="mes" class="form-select rounded-pill border-0 bg-light fw-bold text-primary">
                            <?php
                            $meses = ['01'=>'Enero', '02'=>'Febrero', '03'=>'Marzo', '04'=>'Abril', '05'=>'Mayo', '06'=>'Junio',
                                '07'=>'Julio', '08'=>'Agosto', '09'=>'Septiembre', '10'=>'Octubre', '11'=>'Noviembre', '12'=>'Diciembre'];
                            $mes_actual = $_GET['mes'] ?? date('m');
                            foreach($meses as $num => $nombre):
                                ?>
                                <option value="<?= $num ?>" <?= $mes_actual == $num ? 'selected' : '' ?>><?= $nombre ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select name="anio" class="form-select rounded-pill border-0 bg-light fw-bold text-primary">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-search me-2"></i> Filtrar
                        </button>
                    </form>

                    <button onclick="generarPDF()" class="btn btn-danger rounded-pill px-4 shadow-sm">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Descargar PDF
                    </button>
                </div>
            </div>

            <div class="col-12">
                <div class="card-neo" id="documento-pdf">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
                        <div>
                            <h2 class="fw-bold text-primary m-0"><span style="color:var(--accent-color)">Neo</span>Bank</h2>
                            <small class="text-muted">Estado de Cuenta Oficial</small>
                        </div>
                        <div class="text-end">
                            <h5 class="fw-bold mb-0">Periodo: <?= $meses[$mes_actual] ?> <?= $anio ?? date('Y') ?></h5>
                            <small class="text-secondary">Generado el: <?= date('d/m/Y') ?></small>
                        </div>
                    </div>

                    <div class="row mb-4 p-3 bg-light rounded-3 mx-0">
                        <div class="col-6">
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Titular</small>
                            <div class="fw-bold text-dark"><?= $_SESSION['nombre'] ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Cuenta / CLABE</small>
                            <div class="fw-bold text-dark font-monospace"><?= $cuenta['numero_cuenta'] ?></div>
                        </div>
                    </div>

                    <table class="table table-neo align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto / Referencia</th>
                            <th class="text-end">Monto</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(count($movimientos) > 0): ?>
                            <?php foreach($movimientos as $m):
                                $es_deposito = ($m['tipo'] == 'deposito' || $m['cuenta_destino_id'] == $cuenta['id']);
                                $signo = $es_deposito ? '+' : '-';
                                $color = $es_deposito ? 'text-success' : 'text-danger';
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= ucfirst($m['tipo']) ?></div>
                                        <small class="text-muted"><?= $m['descripcion'] ?? 'Sin descripción' ?></small>
                                    </td>
                                    <td class="text-end fw-bold <?= $color ?>">
                                        <?= $signo ?> $<?= number_format($m['monto'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">No hay movimientos en este periodo.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-5 pt-3 border-top text-center">
                        <small class="text-muted" style="font-size: 0.7rem;">
                            Este documento es un comprobante digital emitido por NeoBank S.A. de C.V. <br>
                            Para aclaraciones, contacte a soporte@neobank.com
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Función para imprimir PDF
    function generarPDF() {
        const elemento = document.getElementById('documento-pdf');
        const opciones = {
            margin:       10,
            filename:     'EstadoCuenta_NeoBank.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 }, // Escala para mejor calidad
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Botón feedback
        const btn = document.querySelector('button[onclick="generarPDF()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Generando...';
        btn.disabled = true;

        html2pdf().set(opciones).from(elemento).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>