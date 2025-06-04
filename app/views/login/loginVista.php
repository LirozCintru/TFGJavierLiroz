<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center login-backdrop">
    <div class="card shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 900px;">
        <div class="row g-0">

            <!-- Panel Izquierdo: Formulario de Login -->
            <div class="col-lg-6 bg-white p-4">
                <h3 class="fw-bold text-center text-primary mb-4 login-title">Iniciar Sesión</h3>

                <!-- Mensajes de alerta (error / éxito) -->
                <?php if (!empty($_SESSION['mensaje'])): ?>
                    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                        <?= htmlspecialchars($_SESSION['mensaje']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                    <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['mensaje_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                <!-- Validación específica enviada desde el controlador -->
                <?php if (!empty($datos['errorUnique'])): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($datos['errorUnique']); ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <form action="<?= RUTA_URL ?>/loginsControlador/acceder" method="POST" class="needs-validation"
                    novalidate>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-secondary">
                            <i class="bi bi-envelope-fill me-1"></i> Correo electrónico
                        </label>
                        <input type="email" class="form-control form-control-lg border border-secondary shadow-sm"
                            id="email" name="email" placeholder="usuario@ejemplo.com"
                            value="<?= htmlspecialchars($datos['email'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Por favor, introduce un correo válido.
                        </div>
                        <?php if (!empty($datos['errorEmail'])): ?>
                            <small class="text-danger"><?= htmlspecialchars($datos['errorEmail']); ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-4">
                        <label for="contrasena" class="form-label fw-semibold text-secondary">
                            <i class="bi bi-lock-fill me-1"></i> Contraseña
                        </label>
                        <input type="password" class="form-control form-control-lg border border-secondary shadow-sm"
                            id="contrasena" name="contrasena" placeholder="••••••••••" required>
                        <div class="invalid-feedback">
                            La contraseña es obligatoria.
                        </div>
                        <?php if (!empty($datos['errorContrasena'])): ?>
                            <small class="text-danger"><?= htmlspecialchars($datos['errorContrasena']); ?></small>
                        <?php endif; ?>
                    </div>

                    <!-- Botón “Ingresar” -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">
                            Ingresar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Panel Derecho: Bienvenida / Imagen -->
            <div
                class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-login-side text-white p-4">
                <div class="text-center px-3">
                    <h2 class="fw-bold mb-3">¡Bienvenido a IntraLink!</h2>
                    <p class="mb-4">Accede a tus notificaciones, chatea con tu equipo y mantente al tanto de todas las
                        novedades.</p>
                    <i class="bi bi-chat-dots-fill display-3 mb-3"></i>
                </div>
            </div>

        </div> <!-- /.row -->
    </div> <!-- /.card -->
</div> <!-- /.login-backdrop -->

<?php require RUTA_APP . '/views/inc/footer.php'; ?>