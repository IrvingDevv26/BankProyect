<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - NeoBank</title>
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
                <h4 class="ms-3 fw-bold text-primary mb-0">Mi Perfil</h4>
            </div>
        </nav>

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="card-neo text-center p-5">
                    <div class="mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary"><?= htmlspecialchars($_SESSION['nombre']) ?></h3>
                    <p class="text-secondary mb-4"><?= htmlspecialchars($usuario['email']) ?></p>

                    <div class="d-flex justify-content-between border-top pt-4">
                        <div class="text-start">
                            <small class="text-muted d-block">Rol</small>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Cliente</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Miembro desde</small>
                            <span class="fw-bold text-dark">Ene 2026</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card-neo">
                    <h5 class="fw-bold text-primary mb-4">Seguridad de la Cuenta</h5>

                    <div id="alert-area"></div>

                    <form id="form-password">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">CONTRASEÑA ACTUAL</label>
                            <input type="password" name="current_password" class="form-control bg-light border-0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">NUEVA CONTRASEÑA</label>
                            <input type="password" name="new_password" class="form-control bg-light border-0" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">CONFIRMAR NUEVA</label>
                            <input type="password" name="confirm_password" class="form-control bg-light border-0" required minlength="6">
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: var(--accent-color)">
                                <i class="bi bi-shield-lock me-2"></i>Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica AJAX para cambiar contraseña
    document.getElementById('form-password').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const alertArea = document.getElementById('alert-area');
        const btn = this.querySelector('button[type="submit"]');

        // Validar coincidencia
        if (formData.get('new_password') !== formData.get('confirm_password')) {
            alertArea.innerHTML = '<div class="alert alert-danger">Las nuevas contraseñas no coinciden.</div>';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Guardando...';

        fetch('index.php?action=api_cambiar_password', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alertArea.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Contraseña actualizada correctamente.</div>';
                    this.reset();
                } else {
                    alertArea.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>' + data.msg + '</div>';
                }
            })
            .catch(err => {
                alertArea.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-shield-lock me-2"></i>Actualizar Contraseña';
            });
    });

    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>