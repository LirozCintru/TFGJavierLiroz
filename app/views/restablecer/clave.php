<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center login-backdrop">
    <div class="card shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 900px;">
        <div class="row g-0">

            <!-- Panel izquierdo: formulario -->
            <div class="col-lg-6 bg-white p-4">
                <h3 class="fw-bold text-center text-primary mb-4 login-title">
                    <i class="bi bi-shield-lock-fill me-2"></i>Establecer contraseña
                </h3>

                <!-- Errores de validación -->
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
                        action="<?= RUTA_URL ?>/RestablecerControlador/clave?token=<?= urlencode($datos['token']) ?>"
                        class="needs-validation" novalidate>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Nueva contraseña</label>
                            <input type="password" name="contrasena"
                                class="form-control form-control-lg border border-secondary shadow-sm" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary">Confirmar contraseña</label>
                            <input type="password" name="repetir_contrasena"
                                class="form-control form-control-lg border border-secondary shadow-sm" required>
                            <div class="invalid-feedback">Campo obligatorio.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">Establecer contraseña</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Panel derecho decorativo -->
            <div
                class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-login-side text-white p-4">
                <div class="text-center px-3">
                    <h2 class="fw-bold mb-3">¡Bienvenido a IntraLink!</h2>
                    <p class="mb-4">Establece tu contraseña y accede a todas las funcionalidades de la plataforma.</p>
                    <i class="bi bi-shield-lock-fill display-3 mb-3"></i>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>