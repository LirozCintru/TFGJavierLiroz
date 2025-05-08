document.addEventListener("DOMContentLoaded", () => {
  // 📌 Expansión de publicación
  document.querySelectorAll(".toggle-detalle").forEach((titulo) => {
    titulo.addEventListener("click", () => {
      const id = titulo.dataset.target;
      const detalle = document.getElementById(id);
      if (detalle) {
        detalle.classList.toggle("mostrar");
      }
    });
  });

  // 💬 Enviar comentario sin recargar
  document.querySelectorAll(".form-comentario").forEach((form) => {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const publicacionId = form.dataset.id;
      const input = form.querySelector('input[name="contenido"]');
      const contenido = input.value.trim();

      if (!contenido) return;

      try {
        const res = await fetch(form.action, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest", // ← CABECERA CLAVE
          },
          body: new URLSearchParams({ contenido }),
        });

        const html = await res.text();

        const lista = form
          .closest(".comentarios")
          .querySelector(".comentarios-lista");

        if (lista && html.trim()) {
          lista.insertAdjacentHTML("beforeend", html);
          input.value = "";

          // 🔄 Actualizar contador
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
              "X-Requested-With": "XMLHttpRequest", // ← TAMBIÉN AQUÍ
            },
          });

          if (res.ok) {
            const comentarioItem = form.closest(".comentario-item");
            if (comentarioItem) {
              comentarioItem.remove();

              // 🔄 Actualizar contador
              const contenedor = form.closest(".comentarios");
              const contador = contenedor.querySelector(
                ".contador-comentarios"
              );
              if (contador) {
                const actual = Math.max(0, parseInt(contador.textContent) - 1);
                contador.textContent = actual;
              }
            }
          } else {
            console.warn("⚠️ Respuesta no exitosa al eliminar el comentario.");
          }
        } catch (err) {
          console.error("❌ Error al eliminar el comentario:", err);
        }
      }
    });
  });
});
