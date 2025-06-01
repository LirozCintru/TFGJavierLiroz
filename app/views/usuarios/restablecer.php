<?php require RUTA_APP . '/views/inc/header.php'; ?>

<div class="container py-4">
    <div class="card mx-auto shadow p-4" style="max-width: 500px;">
        <h4 class="mb-3 text-primary">🔐 Establecer nueva contraseña</h4>

        <?php if (!empty($datos['error'])): ?>
            <div class="alert alert-danger"><?= $datos['error'] ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Nueva contraseña</label>
                <input type="password" name="contrasena" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Guardar contraseña</button>
        </form>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>