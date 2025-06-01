<?php require RUTA_APP . '/views/inc/header.php'; ?>
<!-- Asegúrate de tener Bootstrap y Bootstrap Icons en header.php -->

<div class="login-background d-flex justify-content-center align-items-center">
    <div class="login-container">
        <h3 class="login-title">Iniciar Sesión</h3>
        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>


        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <?= $_SESSION['mensaje'];
                unset($_SESSION['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?= $_SESSION['mensaje_error'];
                unset($_SESSION['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>


        <!-- Mensaje si las credenciales no son válidas -->
        <?php if (!empty($datos['errorUnique'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($datos['errorUnique']); ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje si no tiene permisos -->
        <?php if (isset($_SESSION['errorPermiso'])): ?>
            <div class="alert alert-danger text-center">
                <?php
                echo htmlspecialchars($_SESSION['errorPermiso']);
                unset($_SESSION['errorPermiso']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form action="<?php echo RUTA_URL; ?>/loginsControlador/acceder" method="POST">
            <div class="form-group mb-3">
                <label for="email" class="form-label fw-bold text-primary">
                    <i class="bi bi-envelope-fill me-1"></i> Email
                </label>
                <input type="text" class="form-control border-primary shadow-sm" id="email" name="email"
                    value="<?php echo isset($datos['email']) ? htmlspecialchars($datos['email']) : ''; ?>" required>
                <?php if (!empty($datos['errorEmail'])): ?>
                    <span class="text-danger"><?php echo htmlspecialchars($datos['errorEmail']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mb-3">
                <label for="contrasena" class="form-label fw-bold text-primary">
                    <i class="bi bi-lock-fill me-1"></i> Contraseña
                </label>
                <input type="password" class="form-control border-primary shadow-sm" id="contrasena" name="contrasena"
                    required>
                <?php if (!empty($datos['errorContrasena'])): ?>
                    <span class="text-danger"><?php echo htmlspecialchars($datos['errorContrasena']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary login-btn mt-2">Ingresar</button>
        </form>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>