<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Tarjetas - NeoBank</title>
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
                <h4 class="ms-3 fw-bold text-primary mb-0">Gestión de Tarjetas</h4>
            </div>
        </nav>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="credit-card-wrap mb-5">
                    <div id="visual-card" class="credit-card <?= ($cuenta['estado'] == 0) ? 'frozen' : '' ?>">
                        <div class="lock-overlay"><i class="bi bi-lock-fill"></i></div>

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="chip"></div>
                            <span class="fst-italic opacity-75 small">NeoBank Infinite</span>
                        </div>

                        <div class="card-number">
                            **** **** **** <?= substr($cuenta['numero_cuenta'], -4) ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <div class="card-holder">Titular</div>
                                <div class="card-name"><?= $_SESSION['nombre'] ?></div>
                            </div>
                            <div class="card-logo"><i class="bi bi-visa"></i></div>
                        </div>
                    </div>
                </div>

                <div class="card-neo text-center p-4">
                    <h5 class="fw-bold text-primary mb-3">Configuración de Seguridad</h5>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3">
                        <div class="text-start">
                            <div class="fw-bold text-dark">Congelar Tarjeta</div>
                            <small class="text-muted">Bloquear compras temporalmente</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="switch-freeze" style="width: 3em; height: 1.5em; cursor: pointer;"
                                <?= ($cuenta['estado'] == 0) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <div class="alert alert-primary border-0 bg-primary bg-opacity-10 text-primary small">
                        <i class="bi bi-shield-check me-1"></i>
                        Protección activa contra fraudes.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('switch-freeze').addEventListener('change', function() {
        const visualCard = document.getElementById('visual-card');
        const isChecked = this.checked;

        if (isChecked) {
            visualCard.classList.add('frozen');
        } else {
            visualCard.classList.remove('frozen');
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
                }
            })
            .catch(err => {
                alert('Error de conexión');
                this.checked = !isChecked;
                visualCard.classList.toggle('frozen');
            });
    });

    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>