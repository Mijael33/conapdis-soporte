<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - CONAPDIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/conapdis.css')); ?>" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-card">
        <img src="<?php echo e(asset('images/logos/logo-conapdis.png')); ?>" alt="CONAPDIS" style="max-width: 150px; height: auto; display: block; margin: 0 auto 1.5rem;">
        <h4 class="login-title">Sistema de Gestión Técnica</h4>
        <p class="login-subtitle">Acceso</p>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <form action="<?php echo e(route('admin.login.submit')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Recordar sesión</label>
            </div>
            <button type="submit" class="btn-login">Acceder al Sistema</button>
        </form>
    </div>
</body>
</html><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/auth/login.blade.php ENDPATH**/ ?>