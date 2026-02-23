<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - NeoBank</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <h3 class="fw-bold mb-0 text-primary-var">Mi Perfil</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 mb-4">
                    <div class="card-neo text-center p-5">
                        <div class="mb-4">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-surface-2 border-theme border"
                                style="width: 120px; height: 120px; font-size: 3rem; font-weight: 800; color: var(--accent-primary);">
                                <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
                            </div>
                        </div>
                        <h3 class="fw-bold text-primary-var mb-1"><?= htmlspecialchars($_SESSION['nombre']) ?></h3>
                        <p class="text-secondary mb-4 font-monospace small"><?= htmlspecialchars($usuario['email']) ?>
                        </p>

                        <div class="d-flex justify-content-between border-top pt-4 border-theme">
                            <div class="text-start">
                                <small class="text-secondary d-block text-uppercase"
                                    style="letter-spacing: 1px;">Rol</small>
                                <span class="badge rounded-pill px-3 mt-1 badge-primary">CLIENTE</span>
                            </div>
                            <div class="text-end">
                                <small class="text-secondary d-block text-uppercase"
                                    style="letter-spacing: 1px;">Miembro desde</small>
                                <span class="fw-bold text-primary-var mt-1 d-block">2026</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card-neo p-4 p-md-5">
                        <h5 class="fw-bold text-primary-var mb-4">Seguridad de la Cuenta</h5>

                        <div id="alert-area"></div>

                        <form id="form-password">
                            <div class="mb-4">
                                <label class="form-label">CONTRASEÑA ACTUAL</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">NUEVA CONTRASEÑA</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">CONFIRMAR NUEVA CONTRASEÑA</label>
                                <input type="password" name="confirm_password" class="form-control" required
                                    minlength="6">
                            </div>

                            <div class="mt-5 pt-4 border-top border-theme">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Actualizar Credenciales
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
        document.getElementById('form-password').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const alertArea = document.getElementById('alert-area');
            const btn = this.querySelector('button[type="submit"]');

            // Validar coincidencia
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                alertArea.innerHTML = '<div class="alert badge-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>Las nuevas contraseñas no coinciden.</div>';
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
                        alertArea.innerHTML = '<div class="alert badge-success"><i class="bi bi-check-circle-fill me-2"></i>Contraseña actualizada correctamente.</div>';
                        this.reset();
                    } else {
                        alertArea.innerHTML = '<div class="alert badge-danger"><i class="bi bi-x-circle-fill me-2"></i>' + data.msg + '</div>';
                    }
                })
                .catch(err => {
                    alertArea.innerHTML = '<div class="alert badge-danger"><i class="bi bi-x-circle-fill me-2"></i>Error de conexión.</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i>Actualizar Credenciales';
                });
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>