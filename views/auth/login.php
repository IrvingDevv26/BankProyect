<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="bg-shape shape-1"></div>
<div class="bg-shape shape-2"></div>

<div class="login-wrapper position-relative" style="z-index: 1;">
    <div class="login-card">

        <div class="login-header">
            <div class="login-logo">
                <span style="color: var(--accent-color)">Neo</span>Bank
            </div>
            <p class="text-secondary mb-0">Banca Segura y Digital</p>
            <small class="text-muted" style="font-size: 0.8rem">Ingresa tus credenciales para continuar</small>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'timeout'): ?>
            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning d-flex align-items-center mb-4 rounded-3">
                <i class="bi bi-clock-history fs-4 me-3"></i>
                <div>
                    <strong>Sesión Expirada</strong><br>
                    <small>Por seguridad, tu sesión se cerró tras 10 min de inactividad.</small>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center mb-4 rounded-3">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <strong>Acceso Denegado</strong><br>
                    <small><?= htmlspecialchars($error) ?></small>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login" id="loginForm">

            <div class="mb-4">
                <label class="form-label text-secondary fw-bold small ms-1">CORREO ELECTRÓNICO</label>
                <div class="input-group">
                    <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-login rounded-end-pill" placeholder="nombre@correo.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-secondary fw-bold small ms-1">CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text rounded-start-pill ps-3"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control form-control-login rounded-end-pill" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-grid gap-2 mt-5">
                <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm" style="background: var(--accent-color); border:none; font-size: 1.1rem;">
                    Ingresar a mi Cuenta
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="#" class="text-decoration-none text-secondary small">¿Olvidaste tu contraseña?</a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Efecto de carga en el botón (Feedback UI según SQAP)
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...';
    });
</script>
</body>
</html>