<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tarjetas - NeoBank</title>
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
                    <h3 class="fw-bold mb-0 text-primary-var">Gestión de Tarjetas</h3>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">

                    <div class="credit-card-wrap mb-5">
                        <div id="visual-card"
                            class="credit-card <?= ($cuenta['estado'] == 0) ? 'frozen' : 'primary' ?>">
                            
                            <!-- FROZEN ICE OVERLAY -->
                            <div class="lock-overlay"
                                style="<?= ($cuenta['estado'] == 0) ? 'display:flex;' : 'display:none;' ?>">
                                <i class="bi bi-snow2"></i>
                                <span>Congelada</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-start position-relative" style="z-index: 2;">
                                <div class="card-brand">NeoBank</div>
                                <i class="bi bi-wifi fs-4 opacity-75"></i>
                            </div>
                            
                            <div class="card-chip mt-2"></div>

                            <div class="card-number position-relative" style="z-index: 2;">
                                4815 1623 4210 <?= substr($cuenta['numero_cuenta'], -4) ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-end position-relative"
                                style="z-index: 2;">
                                <div>
                                    <div class="card-holder">Titular de la Tarjeta</div>
                                    <div class="card-name">
                                        <?= $_SESSION['nombre'] ?>
                                    </div>
                                </div>
                                <div class="card-logo"><i class="bi bi-cc-mastercard"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-neo text-center p-4">
                        <h5 class="fw-bold text-primary-var mb-4">Configuración de Seguridad</h5>

                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3 bg-surface-2 border-theme border">
                            <div class="text-start">
                                <div class="fw-bold text-primary-var">Congelar Tarjeta</div>
                                <small class="text-secondary">Bloquear compras temporalmente</small>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="switch-freeze"
                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                    <?= ($cuenta['estado'] == 0) ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="alert border-0 text-start small mt-4 badge-primary" style="border-radius: 12px;">
                            <i class="bi bi-shield-check me-2 fs-5 align-middle"></i>
                            Protección activa contra fraudes.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('switch-freeze').addEventListener('change', function () {
            const visualCard = document.getElementById('visual-card');
            const lockOverlay = document.querySelector('.lock-overlay');
            const isChecked = this.checked;

            if (isChecked) {
                visualCard.classList.remove('primary');
                visualCard.classList.add('frozen');
                lockOverlay.style.display = 'flex';
            } else {
                visualCard.classList.remove('frozen');
                visualCard.classList.add('primary');
                lockOverlay.style.display = 'none';
            }

            fetch('index.php?action=api_congelar_tarjeta')
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        console.log(data.msg);
                    } else {
                        alert('Error: ' + data.msg);
                        this.checked = !isChecked;
                        visualCard.classList.toggle('frozen');
                        visualCard.classList.toggle('primary');
                    }
                })
                .catch(err => {
                    alert('Error de conexión');
                    this.checked = !isChecked;
                    visualCard.classList.toggle('frozen');
                    visualCard.classList.toggle('primary');
                });
        });
    </script>
</body>

</html>