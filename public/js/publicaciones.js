document.addEventListener("DOMContentLoaded", () => {
  // 🔽 Expansión de publicaciones (toggle)
  document.querySelectorAll(".toggle-detalle").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.target;
      const detalle = document.getElementById(id);

      if (detalle) {
        detalle.classList.toggle("mostrar");

        // Cambiar icono (chevron abajo / arriba)
        const icono = btn.querySelector("i");
        if (icono) {
          icono.classList.toggle("bi-chevron-down");
          icono.classList.toggle("bi-chevron-up");
        }

        // Cambiar texto del botón, pero solo si existe icono
        if (icono) {
          btn.innerHTML =
            icono.outerHTML +
            (detalle.classList.contains("mostrar") ? " Ver menos" : " Ver más");
        }
      }
    });
  });

  // 💬 Enviar comentario sin recargar
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
            "X-Requested-With": "XMLHttpRequest", // para diferenciar AJAX en backend
          },
          body: new URLSearchParams({ contenido }),
        });

        const html = await res.text();
        const lista = form
          .closest(".comentarios")
          .querySelector(".comentarios-lista");

        if (lista && html.trim()) {
          lista.insertAdjacentHTML("beforeend", html); // añade al final
          input.value = "";

          // Actualiza contador de comentarios
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

  // 🗑️ Eliminar comentario sin recargar
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

            // Actualizar contador
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

  // 🔔 Contador de notificaciones (burbuja en header)
  async function actualizarContadorNotificaciones() {
    try {
      const res = await fetch("/NotificacionesControlador/contador"); // ← Ruta que devuelve JSON: { pendientes: X }
      const data = await res.json();

      const badge = document.getElementById("contador-notificaciones");
      if (badge && data.pendientes > 0) {
        badge.textContent = data.pendientes;
        badge.classList.remove("d-none");
        document.title = `(${data.pendientes}) IntraLink`; // actualiza título
      } else if (badge) {
        badge.classList.add("d-none");
        document.title = "IntraLink";
      }
    } catch (e) {
      console.error("❌ Error al obtener notificaciones:", e);
    }
  }

  // ⏱️ Ejecutar ahora y luego repetir cada 15 segundos
  actualizarContadorNotificaciones();
  setInterval(actualizarContadorNotificaciones, 15000);

  // 📥 Clic en icono o enlace de notificaciones → ir a página de lista
  const iconoNotificaciones = document.getElementById("notificaciones-link");
  if (iconoNotificaciones) {
    iconoNotificaciones.addEventListener("click", (e) => {
      window.location.href = "/NotificacionesControlador/index"; // ← o ajusta si usas secciones dinámicas
    });
  }
});
