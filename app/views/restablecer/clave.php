<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="login-background d-flex justify-content-center align-items-center">
    <div class="login-container">

        <h4 class="login-title text-center mb-4">
            <i class="bi bi-shield-lock-fill me-2"></i>Establecer contraseña
        </h4>

        <?php if (!empty($datos['errores'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($datos['errores'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <!-- Éxito -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                <?php unset($_SESSION['mensaje']); ?>
                <br><br>
                <a href="<?= RUTA_URL ?>/logins" class="btn btn-success w-100 mt-3">Ir a iniciar sesión</a>
            </div>
        <?php endif; ?>

        <!-- Error -->
        <?php if (!empty($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                <?php unset($_SESSION['mensaje_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <?php if (empty($_SESSION['mensaje'])): ?>
            <form method="POST"
                action="<?= RUTA_URL ?>/RestablecerControlador/clave?token=<?= urlencode($datos['token']) ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Nueva contraseña</label>
                    <input type="password" name="contrasena" class="form-control border-primary shadow-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Confirmar contraseña</label>
                    <input type="password" name="repetir_contrasena" class="form-control border-primary shadow-sm" required>
                </div>
                <button type="submit" class="btn btn-primary login-btn mt-2">Establecer contraseña</button>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>