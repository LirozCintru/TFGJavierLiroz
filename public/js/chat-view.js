// public/js/chat-view.js

document.addEventListener("DOMContentLoaded", () => {
  // ——— Botón “Cerrar” chat: elimina ?con y recarga ———
  const btnCerrar = document.getElementById("btn-cerrar-chat");
  if (btnCerrar) {
    btnCerrar.addEventListener("click", () => {
      const params = new URLSearchParams(window.location.search);
      params.delete("con");
      window.location.search = params.toString();
    });
  }

  // ——— Enviar mensaje por AJAX ———
  const formChat = document.getElementById("form-chat");
  if (formChat) {
    formChat.addEventListener("submit", async (e) => {
      e.preventDefault();
      const ta = formChat.querySelector('textarea[name="mensaje"]');
      const texto = ta.value.trim();
      if (!texto) return;

      // Pintar provisional al final
      const chatBox = document.getElementById("chat-messages");
      const miId = parseInt(document.body.dataset.userId, 10);
      const conId = parseInt(document.body.dataset.chatWith, 10);
      const tempId = "tmp" + Date.now();

      const divItem = document.createElement("div");
      divItem.className = "message-item sent";
      divItem.setAttribute("data-id", tempId);
      divItem.innerHTML = `
        <div class="message-bubble sent">${texto.replace(/\n/g, "<br>")}</div>
        <div class="message-time">
          ${new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
          })}
        </div>`;
      chatBox.appendChild(divItem);
      chatBox.scrollTop = chatBox.scrollHeight;
      ta.value = "";

      // AJAX a enviar
      const formData = new FormData(formChat);
      try {
        const res = await fetch(
          "/TFGJavierLiroz/public/index.php?url=ChatControlador/enviar",
          { method: "POST", body: formData }
        );
        const json = await res.json();
        if (json.ok) {
          divItem.setAttribute("data-id", json.id);
        } else {
          divItem.querySelector(".message-bubble").style.backgroundColor =
            "#dc3545";
        }
      } catch (err) {
        console.error("❌ Error al enviar mensaje:", err);
      }
    });
  }

  // ——— Polling: cada 3 s trae mensajes nuevos ———
  let ultimoId = parseInt(document.body.dataset.lastId || "0", 10);
  const conId = parseInt(document.body.dataset.chatWith, 10) || 0;
  const intervalo = setInterval(async () => {
    if (!conId) return;
    try {
      const res = await fetch(
        `/TFGJavierLiroz/public/index.php?url=ChatControlador/nuevos&con=${conId}&ultimo=${ultimoId}`
      );
      if (!res.ok) return;
      const datos = await res.json();
      if (!Array.isArray(datos) || datos.length === 0) return;

      const chatBox = document.getElementById("chat-messages");
      datos.forEach((m) => {
        const esMio =
          m.id_remitente === parseInt(document.body.dataset.userId, 10);
        const claseItem = esMio ? "sent" : "received";
        const claseBubble = esMio ? "sent" : "received";

        const divItem = document.createElement("div");
        divItem.className = "message-item " + claseItem;
        divItem.setAttribute("data-id", m.id_mensaje);
        divItem.innerHTML = `
          <div class="message-bubble ${claseBubble}">
            ${m.contenido.replace(/\n/g, "<br>")}
          </div>
          <div class="message-time">
            ${new Date(m.fecha).toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            })}
          </div>`;
        chatBox.appendChild(divItem);
        ultimoId = m.id_mensaje;
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    } catch (err) {
      console.error("❌ Error en polling chat:", err);
    }
  }, 3000);

  window.addEventListener("beforeunload", () => clearInterval(intervalo));
});
