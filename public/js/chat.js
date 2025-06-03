// public/js/chat.js
document.addEventListener("DOMContentLoaded", () => {
  // 🔔 Función para actualizar el badge de chat
  async function actualizarBadgeChat() {
    try {
      // Ajusta esta URL según tu estructura de index.php?url=...
      const res = await fetch(
        "/TFGJavierLiroz/public/index.php?url=ChatControlador/contador"
      );
      if (!res.ok) return;
      const data = await res.json();

      const badge = document.getElementById("badge-chat");
      if (!badge) return;

      if (data.pendientes > 0) {
        badge.textContent = data.pendientes;
        badge.classList.remove("d-none");
      } else {
        badge.classList.add("d-none");
      }
    } catch (e) {
      console.error("❌ Error al obtener badge-chat:", e);
    }
  }

  // Ejecutar al cargar la página y luego cada 10 segundos
  actualizarBadgeChat();
  setInterval(actualizarBadgeChat, 10000);

  // 📥 Al hacer clic en el icono de chat, redirigir a la lista de conversaciones
  const iconoChat = document.getElementById("chat-link");
  if (iconoChat) {
    iconoChat.addEventListener("click", (e) => {
      e.preventDefault();
      window.location.href =
        "/TFGJavierLiroz/public/index.php?url=ChatControlador/index";
    });
  }
});
