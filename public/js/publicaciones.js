document.addEventListener("DOMContentLoaded", () => {
  // 🔽 Expansión de publicaciones (mostrar/ocultar contenido)
  document.querySelectorAll(".toggle-detalle").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.target;
      const detalle = document.getElementById(id);

      if (detalle) {
        detalle.classList.toggle("mostrar"); // Toggle de clase para mostrar detalles

        // Cambia el icono si existe
        const icono = btn.querySelector("i");
        if (icono) {
          icono.classList.toggle("bi-chevron-down");
          icono.classList.toggle("bi-chevron-up");

          // Solo actualiza el texto si hay icono (para evitar que se sobreescriba el título)
          btn.innerHTML =
            icono.outerHTML +
            (detalle.classList.contains("mostrar") ? " Ver menos" : " Ver más");
        }
      }
    });
  });

  // 💬 Envío de comentarios sin recargar
  document.querySelectorAll(".form-comentario").forEach((form) => {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const input = form.querySelector('input[name="contenido"]');
      const contenido = input.value.trim();
      if (!contenido) return;

      try {
        const res = await fetch(form.action, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest", // para diferenciar peticiones AJAX
          },
          body: new URLSearchParams({ contenido }),
        });

        const html = await res.text();

        // Añade nuevo comentario sin recargar
        const lista = form
          .closest(".comentarios")
          .querySelector(".comentarios-lista");
        if (lista && html.trim()) {
          lista.insertAdjacentHTML("beforeend", html);
          input.value = "";

          // Actualizar contador
          const contador = form
            .closest(".comentarios")
            .querySelector(".contador-comentarios");
          if (contador) {
            contador.textContent = parseInt(contador.textContent) + 1;
          }
        }
      } catch (err) {
        console.error("❌ Error al enviar el comentario:", err);
        alert("No se pudo enviar el comentario.");
      }
    });
  });

  // 🗑️ Eliminar comentarios por AJAX
  document.querySelectorAll(".comentarios-lista").forEach((lista) => {
    lista.addEventListener("submit", async (e) => {
      const form = e.target;

      if (form.matches("form") && form.action.includes("eliminarComentario")) {
        e.preventDefault();

        const confirmado = confirm("¿Eliminar comentario?");
        if (!confirmado) return;

        try {
          const res = await fetch(form.action, {
            method: "POST",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
            },
          });

          if (res.ok) {
            const comentarioItem = form.closest(".comentario-item");
            if (comentarioItem) comentarioItem.remove();

            // Reducir contador
            const contador = form
              .closest(".comentarios")
              .querySelector(".contador-comentarios");
            if (contador) {
              const actual = Math.max(0, parseInt(contador.textContent) - 1);
              contador.textContent = actual;
            }
          } else {
            console.warn("⚠️ No se pudo eliminar el comentario.");
          }
        } catch (err) {
          console.error("❌ Error al eliminar el comentario:", err);
        }
      }
    });
  });

  // 🔔 Actualizar contador de notificaciones no leídas (en la cabecera)
  async function actualizarContadorNotificaciones() {
    try {
      const res = await fetch(
        "/TFGJavierLiroz/public/index.php?url=NotificacionesControlador/contador"
      );
      const data = await res.json();

      const badge = document.getElementById("contador-notificaciones");
      if (badge && data.pendientes > 0) {
        badge.textContent = data.pendientes;
        badge.classList.remove("d-none");
        badge.classList.add("bg-danger");
        document.title = `(${data.pendientes}) IntraLink`;
      } else if (badge) {
        badge.classList.add("d-none");
        document.title = "IntraLink";
      }
    } catch (e) {
      console.error("❌ Error al obtener notificaciones:", e);
    }
  }

  // ⏱️ Ejecutar la función al cargar y luego cada 15 segundos
  actualizarContadorNotificaciones();
  setInterval(actualizarContadorNotificaciones, 15000);

  // 📥 Redirección al hacer clic en el icono de notificaciones
  const iconoNotificaciones = document.getElementById("notificaciones-link");
  if (iconoNotificaciones) {
    iconoNotificaciones.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href = "/TFGJavierLiroz/NotificacionesControlador/index";
    });
  }
});
