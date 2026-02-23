<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - NeoBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <script>
        const theme = localStorage.getItem('neobank-theme') || 'light';
        if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="mb-4 text-center">
                <h1 class="fw-bold mb-0" style="font-size: 2.5rem; letter-spacing: -1px;">
                    <i class="bi bi-bank text-accent me-2"></i><span class="text-accent">Neo</span>Bank
                </h1>
                <p class="text-secondary mt-2 small text-uppercase" style="letter-spacing: 1px;">
                    Acceso Seguro
                </p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="border-radius: 12px; font-weight: 500;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=login" id="loginForm">
                <div class="mb-4 text-start">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control form-control-lg"
                        placeholder="usuario@correo.com" required style="font-size: 1rem;">
                </div>

                <div class="mb-5 text-start">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Contraseña</label>
                        <a href="#" class="text-accent text-decoration-none small fw-bold"
                            style="font-size: 0.8rem;">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••"
                        required style="font-size: 1rem;">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 mb-4"
                    style="font-size: 1.1rem; letter-spacing: 1px;">
                    Ingresar a mi cuenta
                </button>
            </form>

            <div class="text-secondary small" style="font-size: 0.8rem;">
                Conexión Encriptada de Extremo a Extremo <i class="bi bi-shield-lock-fill ms-1 text-accent"></i>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>