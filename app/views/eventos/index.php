<?php require RUTA_APP . '/views/inc/headerMain.php'; ?>
<?php $categorias = require RUTA_APP . '/config/categorias_evento.php'; ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
<style>
    #calendar {
        width: 100% !important;
        padding: 1rem !important;
        margin: 0 auto;
        font-size: 0.95rem;
        min-height: 640px;
        background-color: #fdfdfd;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }
    .bg-white {
        background-color: #fbfbfc !important;
    }
    .fc-toolbar-title {
        font-size: 1.3rem !important;
    }
    #modalEnlaceRow {
        display: none;
    }
    .contenedor-calendario {
        margin: 0;
        padding: 0;
    }
    .bloque-calendario {
        border-radius: 0;
        border: none;
        box-shadow: none;
        border-top: 2px solid #dee2e6;
    }
    .leyenda-categoria {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-right: 12px;
        margin-bottom: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        color: #212529;
    }
    .color-cuadro {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid #333;
        flex-shrink: 0;
        display: inline-block;
    }
</style>
<div class="container py-4">
    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">
        <!-- Cabecera -->
        <div class="encabezado-edicion px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-calendar-event-fill me-2"></i>Calendario de Eventos
            </h5>
        </div>

        <div class="px-4 pt-3 pb-4">
            <div class="d-flex flex-wrap mb-3">
                <?php foreach ($categorias as $nombre => $info): ?>
                    <div class="leyenda-categoria me-4 mb-2">
                        <span class="color-cuadro"
                            style="background-color: <?= htmlspecialchars($info['color']) ?>;"></span>
                        <?= ucfirst($nombre) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="calendar" class="w-100 bg-white rounded"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventoModalLabel">📌 Detalles del Evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Título:</strong> <span id="modalTitulo"></span></p>
                <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                <p><strong>Hora:</strong> <span id="modalHora"></span></p>
                <p><strong>Departamento:</strong> <span id="modalDepartamento"></span></p>
                <p><strong>Descripción:</strong><br><span id="modalDescripcion"></span></p>
                <p id="modalEnlaceRow" style="display:none;"><strong>🔗 Enlace:</strong> <a href="#" id="modalUrl"
                        target="_blank" rel="noopener noreferrer">Ir al enlace</a></p>
            </div>
            <div class="modal-footer">
                <?php if ($usuario && in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE])): ?>
                    <div id="accionesEvento">
                        <a href="#" id="btnEditar" class="btn btn-success btn-sm">✏️ Editar publicación</a>
                        <form id="formEliminarEvento" method="POST" action="" style="display: inline;">
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este evento?')">🗑️ Eliminar</button>
                        </form>
                    </div>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 'auto',
            eventDisplay: 'block',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            events: '<?= RUTA_URL ?>/EventosControlador/obtener',

            eventClick: function (info) {
                const e = info.event.extendedProps;

                const esTodoElDia = info.event.allDay;
                const inicio = info.event.startStr || '-';
                const fin = info.event.endStr || '';
                const horaInicio = e.hora || '';
                const horaFin = e.hora_fin || '';
                const enlace = e.url || '';

                document.getElementById('modalTitulo').textContent = info.event.title || 'Sin título';
                document.getElementById('modalFecha').textContent = esTodoElDia ? inicio : `${inicio} - ${fin}`;
                document.getElementById('modalHora').textContent = esTodoElDia
                    ? 'Todo el día'
                    : (horaInicio ? `${horaInicio}${horaFin ? ' - ' + horaFin : ''}` : '-');
                document.getElementById('modalDepartamento').textContent = e.nombre_departamento || 'General';
                document.getElementById('modalDescripcion').textContent = e.descripcion || 'Sin descripción';

                // Enlace
                if (enlace && enlace !== '#') {
                    document.getElementById('modalUrl').href = enlace;
                    document.getElementById('modalEnlaceRow').style.display = 'block';
                } else {
                    document.getElementById('modalEnlaceRow').style.display = 'none';
                }

                // Botones
                const btnEditar = document.getElementById('btnEditar');
                if (btnEditar) {
                    btnEditar.href = '<?= RUTA_URL ?>/PublicacionesControlador/editar/' + e.id_publicacion;
                }

                const formEliminar = document.getElementById('formEliminarEvento');
                if (formEliminar) {
                    formEliminar.action = '<?= RUTA_URL ?>/EventosControlador/eliminar/' + e.id_evento;
                }

                new bootstrap.Modal(document.getElementById('eventoModal')).show();
                info.jsEvent.preventDefault();
            },

            eventDidMount: function (info) {
                const color = info.event.backgroundColor || '#0d6efd';
                const el = info.el;

                // Estilo base
                el.style.backgroundColor = `${color}22`;
                el.style.border = `2px solid ${color}`;
                el.style.borderRadius = '6px';
                el.style.padding = '4px 8px';
                el.style.color = '#000'; // Siempre texto negro

                // Limpiar puntos anteriores
                el.querySelectorAll('.evento-dot').forEach(dot => dot.remove());

                // Crear nuevo punto
                const dot = document.createElement('span');
                dot.className = 'evento-dot';
                dot.style.cssText = `
                    display: inline-block;
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background-color: ${color};
                    margin-right: 6px;
                    vertical-align: middle;
                `;

                // Insertar el punto delante del texto del evento
                const titleContainer = el.querySelector('.fc-event-title');
                if (titleContainer) {
                    const span = document.createElement('span');
                    span.textContent = titleContainer.textContent;
                    span.style.fontWeight = 'normal';
                    span.className = 'evento-titulo';

                    titleContainer.innerHTML = '';
                    titleContainer.appendChild(dot);
                    titleContainer.appendChild(span);
                } else {
                    el.prepend(dot);
                }
            }
        });

        calendar.render();
    });
</script>


<?php require RUTA_APP . '/views/inc/footer.php'; ?>