<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estados de Cuenta - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                    <h3 class="fw-bold mb-0 text-primary-var">Estados de Cuenta</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card-neo p-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
                        <form class="d-flex gap-3 align-items-center" method="GET" action="index.php">
                            <input type="hidden" name="action" value="reportes">

                            <select name="mes" class="form-select bg-surface-2 border-theme"
                                style="border-radius: 12px; font-weight: 600;">
                                <?php
                                $meses = [
                                    '01' => 'Enero',
                                    '02' => 'Febrero',
                                    '03' => 'Marzo',
                                    '04' => 'Abril',
                                    '05' => 'Mayo',
                                    '06' => 'Junio',
                                    '07' => 'Julio',
                                    '08' => 'Agosto',
                                    '09' => 'Septiembre',
                                    '10' => 'Octubre',
                                    '11' => 'Noviembre',
                                    '12' => 'Diciembre'
                                ];
                                $mes_actual = $_GET['mes'] ?? date('m');
                                foreach ($meses as $num => $nombre):
                                    ?>
                                    <option value="<?= $num ?>" <?= $mes_actual == $num ? 'selected' : '' ?>><?= $nombre ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="anio" class="form-select bg-surface-2 border-theme"
                                style="border-radius: 12px; font-weight: 600;">
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                            </select>

                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="white-space: nowrap;">
                                <i class="bi bi-search me-2"></i> Filtrar
                            </button>
                        </form>

                        <button onclick="generarPDF()" class="btn btn-danger px-4 shadow-sm">
                            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Descargar PDF
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card-neo" id="documento-pdf">
                        <div
                            class="d-flex justify-content-between align-items-center border-bottom border-theme pb-4 mb-4">
                            <div>
                                <h2 class="fw-bold m-0 text-primary-var"><span
                                        style="color:var(--accent-primary)">Neo</span>Bank
                                </h2>
                                <small class="text-secondary"
                                    style="letter-spacing: 1px; text-transform: uppercase;">Estado de Cuenta
                                    Oficial</small>
                            </div>
                            <div class="text-end">
                                <h5 class="fw-bold mb-0 text-primary-var">Periodo: <?= $meses[$mes_actual] ?>
                                    <?= $anio ?? date('Y') ?>
                                </h5>
                                <small class="text-secondary font-monospace">Gen: <?= date('d/m/Y') ?></small>
                            </div>
                        </div>

                        <div class="row mb-4 p-3 rounded-3 mx-0 bg-surface-2 border-theme border">
                            <div class="col-6">
                                <small class="text-uppercase text-secondary fw-bold"
                                    style="font-size: 0.70rem; letter-spacing: 1px;">Titular</small>
                                <div class="fw-bold text-primary-var mt-1" style="font-size: 1.1rem;">
                                    <?= $_SESSION['nombre'] ?>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-uppercase text-secondary fw-bold"
                                    style="font-size: 0.70rem; letter-spacing: 1px;">Cuenta CLABE</small>
                                <div class="fw-bold text-accent font-monospace mt-1" style="font-size: 1.1rem;">
                                    <?= $cuenta['numero_cuenta'] ?>
                                </div>
                            </div>
                        </div>

                        <table class="table-neo w-100 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-2">Fecha</th>
                                    <th>Concepto / Ref</th>
                                    <th class="text-end pe-2">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($movimientos) > 0): ?>
                                    <?php foreach ($movimientos as $m):
                                        $es_deposito = ($m['tipo_transaccion'] == 'deposito' || $m['cuenta_destino_id'] == $cuenta['id']);
                                        $signo = $es_deposito ? '+' : '-';
                                        $color = $es_deposito ? 'text-success' : 'text-primary-var';
                                        ?>
                                        <tr>
                                            <td class="font-monospace text-secondary ps-2" style="font-size: 0.85rem;">
                                                <?= date('d/m/Y', strtotime($m['fecha_transaccion'])) ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary-var mb-1"
                                                    style="font-size: 0.95rem; text-transform: uppercase;">
                                                    <?= htmlspecialchars($m['tipo_transaccion']) ?>
                                                </div>
                                                <small class="text-secondary"
                                                    style="font-size: 0.8rem;"><?= htmlspecialchars($m['descripcion']) ?? 'OPERACIÓN DIGITAL' ?></small>
                                            </td>
                                            <td class="text-end fw-bold <?= $color ?> font-monospace pe-2"
                                                style="font-size: 1.1rem;">
                                                <?= $signo ?>$<?= number_format($m['monto'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-secondary border-0">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                            Sin movimientos en la red durante este periodo.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="mt-5 pt-4 text-center border-top border-theme"
                            style="border-top-style: dashed !important;">
                            <small class="text-secondary" style="font-size: 0.70rem; letter-spacing: 0.5px;">
                                <i class="bi bi-shield-check text-accent me-1"></i>
                                Documento digital oficial emitido por la red central de NeoBank.<br>
                                Cifrado SHA-256 v.1.0.9 - neobank.io/auth/verify
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
            const btn = document.querySelector('button[onclick="generarPDF()"]');
            const originalText = btn.innerHTML;

            const opciones = {
                margin: 10,
                filename: 'NeoBank_Statement.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    backgroundColor: '#FFFFFF', // Forzar fondo blanco para el PDF siempre (legibilidad)
                    onclone: function (clonedDoc) {
                        // Forzar tema light en el canvas si está en dark
                        const cloneElement = clonedDoc.getElementById('documento-pdf');
                        clonedDoc.documentElement.setAttribute('data-theme', 'light');
                        // Asegurar colores oscuros para texto en el wrapper clonado
                        cloneElement.style.color = '#0F172A';
                        const texts = cloneElement.querySelectorAll('.text-primary-var');
                        texts.forEach(t => t.style.color = '#0F172A');
                    }
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            btn.innerHTML = '<i class="bi bi-arrow-repeat me-2 font-monospace"></i>PROCESANDO...';
            btn.disabled = true;

            html2pdf().set(opciones).from(elemento).save().then(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>