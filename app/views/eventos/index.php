<?php require RUTA_APP . '/views/inc/headermain.php'; ?>
<?php $categorias = require RUTA_APP . '/config/categorias_evento.php'; ?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

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

<div class="container py-4">
    <!-- Contenedor visual en bloque -->
    <div class="rounded-4 overflow-hidden shadow border border-2 bg-white">

        <!-- Cabecera con franja azul -->
        <div class="encabezado-usuarios-index px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="titulo-edicion mb-0">
                <i class="bi bi-calendar-event me-2"></i>Calendario de Eventos
            </h5>
        </div>

        <!-- Leyenda de categorías -->
        <div class="px-4 pt-3 pb-2 d-flex flex-wrap">
            <?php foreach ($categorias as $nombre => $info): ?>
                <div class="leyenda-categoria">
                    <span class="color-cuadro" style="background-color: <?= htmlspecialchars($info['color']) ?>;"></span>
                    <?= ucfirst($nombre) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Calendario -->
        <div class="px-0 pb-0">
            <div id="calendar" class="shadow-sm p-2 bg-white rounded"></div>
        </div>
    </div>
</div>

<?php require RUTA_APP . '/views/inc/footer.php'; ?>


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
                document.getElementById('btnEditar').href = '<?= RUTA_URL ?>/PublicacionesControlador/editar/' + e.id_publicacion;
                document.getElementById('formEliminarEvento').action = '<?= RUTA_URL ?>/EventosControlador/eliminar/' + e.id_evento;

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