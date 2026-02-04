<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Admin - NeoBank</title>
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
                <h4 class="ms-3 fw-bold text-primary mb-0">Configuración de Administrador</h4>
            </div>
        </nav>

        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card-neo">
                    <h5 class="fw-bold text-primary mb-4">Cambiar Contraseña Maestra</h5>

                    <div id="alert-area"></div>

                    <form id="form-password-admin">
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

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill py-3">
                                Actualizar Credenciales
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
    // Lógica de cambio de pass (Reutiliza la API del cliente, ya que la lógica es la misma: verificar pass y update)
    // PERO necesitamos asegurarnos que la ruta api_cambiar_password funcione para admins tambien.
    document.getElementById('form-password-admin').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const alertArea = document.getElementById('alert-area');
        const btn = this.querySelector('button[type="submit"]');

        if (formData.get('new_password') !== formData.get('confirm_password')) {
            alertArea.innerHTML = '<div class="alert alert-danger">Las contraseñas no coinciden.</div>';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Guardando...';

        fetch('index.php?action=api_cambiar_password', { // Usamos la misma API
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alertArea.innerHTML = '<div class="alert alert-success">Contraseña actualizada.</div>';
                    this.reset();
                } else {
                    alertArea.innerHTML = '<div class="alert alert-danger">' + data.msg + '</div>';
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Actualizar Credenciales';
            });
    });

    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>