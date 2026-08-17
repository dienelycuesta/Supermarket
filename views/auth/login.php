<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesi&oacute;n - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #28a745 0%, #1a7431 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 420px; width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card mx-auto">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-shopping-cart fa-3x text-success"></i>
                    <h3 class="mt-2"><?= APP_NAME ?></h3>
                    <p class="text-muted">Inicia sesi&oacute;n en tu cuenta</p>
                </div>
                <?php if ($msg = Session::getFlash('error')): ?>
                <div class="alert alert-danger"><?= e($msg) ?></div>
                <?php endif; ?>
                <?php if ($msg = Session::getFlash('success')): ?>
                <div class="alert alert-success"><?= e($msg) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= url('login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contrase&ntilde;a</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mb-3">Iniciar Sesi&oacute;n</button>
                </form>
                <p class="text-center mb-0">&iquest;No tienes cuenta? <a href="<?= url('register') ?>" class="text-success">Registrarse</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
