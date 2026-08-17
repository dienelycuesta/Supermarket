<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #28a745 0%, #1a7431 100%); min-height: 100vh; display: flex; align-items: center; }
        .register-card { max-width: 500px; width: 100%; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="register-card mx-auto">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-shopping-cart fa-3x text-success"></i>
                    <h3 class="mt-2"><?= APP_NAME ?></h3>
                    <p class="text-muted">Crea tu cuenta</p>
                </div>
                <?php if ($msg = Session::getFlash('error')): ?>
                <div class="alert alert-danger"><?= e($msg) ?></div>
                <?php endif; ?>
                <?php $errors = Session::getFlash('errors') ?? []; ?>
                <form method="POST" action="<?= url('register') ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" value="<?= e(old('first_name')) ?>" required>
                            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= e($errors['first_name'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" value="<?= e(old('last_name')) ?>" required>
                            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= e($errors['last_name'][0]) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= e(old('email')) ?>" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tel&eacute;fono</label>
                        <input type="tel" name="phone" class="form-control" value="<?= e(old('phone')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contrase&ntilde;a</label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required minlength="8">
                        <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contrase&ntilde;a</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mb-3">Crear Cuenta</button>
                </form>
                <p class="text-center mb-0">&iquest;Ya tienes cuenta? <a href="<?= url('login') ?>" class="text-success">Iniciar Sesi&oacute;n</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
