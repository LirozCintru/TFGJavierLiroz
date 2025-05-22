<?php $categorias = require RUTA_APP . '/config/categorias_evento.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>

<style>
    #calendar {
        min-height: 640px;
        font-size: 0.95rem;
    }

    .fc-toolbar-title {
        font-size: 1.3rem !important;
    }

    #modalEnlaceRow {
        display: none;
    }
</style>

<h3 class="mb-4">📅 Calendario de Eventos</h3>

<div class="d-flex flex-wrap mb-3">
    <?php foreach ($categorias as $nombre => $info): ?>
        <div class="leyenda-categoria">
            <span class="color-cuadro" style="background-color: <?= htmlspecialchars($info['color']) ?>;"></span>
            <?= ucfirst($nombre) ?>
        </div>
    <?php endforeach; ?>
</div>

<div id="calendar" class="shadow-sm p-2 bg-white rounded"></div>

<!-- Modal -->
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
                <p id="modalEnlaceRow"><strong>🔗 Enlace:</strong> <a href="#" id="modalUrl" target="_blank"
                        rel="noopener noreferrer">Ir al enlace</a></p>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnEditar" class="btn btn-success btn-sm">✏️ Editar publicación</a>
                <form id="formEliminarEvento" method="POST" action="" style="display: inline;">
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('¿Eliminar este evento?')">🗑️ Eliminar</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

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

                if (enlace && enlace !== '#') {
                    document.getElementById('modalUrl').href = enlace;
                    document.getElementById('modalEnlaceRow').style.display = 'block';
                } else {
                    document.getElementById('modalEnlaceRow').style.display = 'none';
                }

                document.getElementById('btnEditar').href = '<?= RUTA_URL ?>/PublicacionesControlador/editar/' + e.id_publicacion;
                document.getElementById('formEliminarEvento').action = '<?= RUTA_URL ?>/EventosControlador/eliminar/' + e.id_evento;

                new bootstrap.Modal(document.getElementById('eventoModal')).show();
                info.jsEvent.preventDefault();
            },

            eventDidMount: function (info) {
                const color = info.event.backgroundColor || '#0d6efd';
                const el = info.el;

                el.style.backgroundColor = `${color}22`;
                el.style.border = `2px solid ${color}`;
                el.style.borderRadius = '6px';
                el.style.padding = '4px 8px';
                el.style.color = '#000';

                el.querySelectorAll('.evento-dot').forEach(dot => dot.remove());

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