<?php
// app/views/chat/list.php
require_once RUTA_APP . '/views/inc/headermain.php';
?>

<!-- CSS extra para estilizar el chat -->
<link rel="stylesheet" href="<?= RUTA_URL ?>/public/css/chat.css">

<div class="container py-4">
    <div class="chat-card">
        <!-- Cabecera de la tarjeta -->
        <div class="chat-card-header">
            <h5><i class="bi bi-chat-dots me-2"></i>Chat</h5>
            <a href="<?= RUTA_URL ?>/ContenidoControlador/inicio" class="btn btn-outline-light btn-sm">← Volver</a>
        </div>

        <!-- Contenido del chat -->
        <div class="chat-container" data-user-id="<?= htmlspecialchars($datos['yo']) ?>"
            data-chat-with="<?= htmlspecialchars($datos['con'] ?? 0) ?>"
            data-last-id="<?= !empty($datos['mensajes']) ? end($datos['mensajes'])->id_mensaje : 0 ?>">

            <!-- ─── Lado izquierdo: Lista de contactos ─── -->
            <div class="chat-sidebar">
                <div class="search-bar">
                    <form id="form-buscar" action="<?= RUTA_URL ?>/ChatControlador/index" method="GET" class="d-flex">
                        <input type="text" name="nombre" class="form-control form-control-sm me-2"
                            placeholder="Buscar nombre…"
                            value="<?= htmlspecialchars($datos['datos']['filtro_nombre'] ?? '') ?>">
                        <select name="departamento" class="form-select form-select-sm me-2" style="max-width:120px;">
                            <option value="">Depto.</option>
                            <?php foreach ($datos['departamentos'] as $dep): ?>
                                <option value="<?= $dep->id_departamento ?>"
                                    <?= ((int) ($datos['datos']['filtro_departamento'] ?? 0) === $dep->id_departamento) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">OK</button>
                    </form>
                </div>

                <div class="user-list list-group list-group-flush">
                    <?php if (!empty($datos['usuarios'])): ?>
                        <?php foreach ($datos['usuarios'] as $u): ?>
                            <?php
                            $img = $u->imagen ?: 'default.png';
                            $rutaImg = RUTA_URL . '/public/img/usuarios/' . $img;
                            $sinLeer = $datos['pendientesPorRemitente'][$u->id_usuario] ?? 0;
                            $activo = ((int) $datos['con'] === $u->id_usuario) ? ' active' : '';
                            ?>
                            <a href="<?= RUTA_URL ?>/ChatControlador/index?con=<?= $u->id_usuario ?>"
                                class="list-group-item list-group-item-action d-flex align-items-center<?= $activo ?>">
                                <img src="<?= htmlspecialchars($rutaImg) ?>" alt="Avatar <?= htmlspecialchars($u->nombre) ?>"
                                    class="user-avatar">
                                <div class="flex-grow-1">
                                    <div class="user-name"><?= htmlspecialchars($u->nombre) ?></div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(
                                            array_values(array_filter(
                                                $datos['departamentos'],
                                                fn($d) => $d->id_departamento === $u->id_departamento
                                            ))[0]->nombre ?? ''
                                        ) ?>
                                    </small>
                                </div>
                                <?php if ($sinLeer > 0): ?>
                                    <span class="badge bg-danger badge-unread"><?= $sinLeer ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-3 text-center text-muted">
                            No se encontraron usuarios.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ─── Lado derecho: Área de chat ─── -->
            <div class="chat-main">
                <?php if (empty($datos['con'])): ?>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center text-muted">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>Seleccione un contacto para iniciar el chat.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Cabecera del chat (avatar + nombre + cerrar) -->
                    <div class="chat-header">
                        <div class="contact-info">
                            <?php
                            $contacto = array_values(array_filter(
                                $datos['usuarios'],
                                fn($u) => $u->id_usuario === $datos['con']
                            ))[0] ?? null;
                            $contactImg = $contacto && $contacto->imagen ? $contacto->imagen : 'default.png';
                            ?>
                            <img src="<?= RUTA_URL ?>/public/img/usuarios/<?= htmlspecialchars($contactImg) ?>"
                                alt="Avatar <?= htmlspecialchars($contacto->nombre ?? '') ?>" class="contact-avatar">
                            <div class="contact-name"><?= htmlspecialchars($contacto->nombre ?? '') ?></div>
                        </div>
                        <button id="btn-cerrar-chat" class="btn-close-chat" title="Cerrar chat">&times;</button>
                    </div>

                    <!-- Mensajes -->
                    <div id="chat-messages" class="chat-messages">
                        <?php foreach ($datos['mensajes'] as $m): ?>
                            <?php
                            $esMio = ($m->id_remitente === $datos['yo']);
                            $claseItem = $esMio ? 'sent' : 'received';
                            $claseBurbuja = $esMio ? 'sent' : 'received';
                            ?>
                            <div class="message-item <?= $claseItem ?>" data-id="<?= $m->id_mensaje ?>">
                                <div class="message-bubble <?= $claseBurbuja ?>">
                                    <?= nl2br(htmlspecialchars($m->contenido)) ?>
                                    <div class="message-time">
                                        <?= date('H:i', strtotime($m->fecha)) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pie de página: Enviar mensaje + botones scroll -->
                    <div class="chat-footer">
                        <form id="form-chat" class="d-flex align-items-center w-100">
                            <textarea name="mensaje" class="form-control me-2" rows="1" placeholder="Escribe tu mensaje…"
                                required></textarea>

                            <!-- Botones para desplazar: arriba / abajo -->
                            <button type="button" id="btn-scroll-arriba" class="btn btn-light btn-sm me-1"
                                title="Ir al inicio de la conversación">↑</button>
                            <button type="button" id="btn-scroll-abajo" class="btn btn-light btn-sm me-2"
                                title="Ir al final de la conversación">↓</button>

                            <!-- Botón de enviar (flecha) -->
                            <button type="submit" class="btn btn-primary btn-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JS específico del chat (envío + polling) -->
<script src="<?= RUTA_URL ?>/public/js/chat-view.js"></script>

<!-- Forzar scroll al fondo al cargar por primera vez y controlar botones “↑/↓” -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const chatBox = document.getElementById("chat-messages");
        const btnArriba = document.getElementById("btn-scroll-arriba");
        const btnAbajo = document.getElementById("btn-scroll-abajo");

        if (!chatBox || !btnArriba || !btnAbajo) return;

        // 1) Bajar al fondo al cargar todo el historial
        window.requestAnimationFrame(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        // 2) Mostrar u ocultar “↑/↓” si hay scroll
        function actualizarBotones() {
            // Si el contenido (scrollHeight) es mayor que la altura visible (clientHeight), 
            // significa que puedo desplazarme. Entonces muestro los botones.
            if (chatBox.scrollHeight > chatBox.clientHeight + 5) {
                btnArriba.style.display = "inline-block";
                btnAbajo.style.display = "inline-block";
            } else {
                btnArriba.style.display = "none";
                btnAbajo.style.display = "none";
            }
        }
        actualizarBotones();
        chatBox.addEventListener("scroll", actualizarBotones);
        window.addEventListener("resize", actualizarBotones);

        // 3) “Ir arriba” y “Ir abajo” con animación suave
        btnArriba.addEventListener("click", () => {
            chatBox.scrollTo({ top: 0, behavior: "smooth" });
        });
        btnAbajo.addEventListener("click", () => {
            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: "smooth" });
        });
    });
</script>

<?php
require_once RUTA_APP . '/views/inc/footer.php';
?>