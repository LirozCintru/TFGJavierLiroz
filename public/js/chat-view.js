document.addEventListener("DOMContentLoaded", () => {
  // 1) Referencia al contenedor principal (donde tenemos data-user-id, data-chat-with, data-last-id)
  const chatContainer = document.querySelector(".chat-container");
  if (!chatContainer) return;

  // Extraemos miId, conId y ultimoId del data-attributes
  const miId = parseInt(chatContainer.dataset.userId, 10) || 0;
  let conId = parseInt(chatContainer.dataset.chatWith, 10) || 0;
  let ultimoId = parseInt(chatContainer.dataset.lastId || "0", 10);

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

      // 1) Tomamos el texto del textarea ANTES de vaciarlo
      const ta = formChat.querySelector('textarea[name="mensaje"]');
      const texto = ta.value.trim();
      if (!texto) return; // Si no hay texto, salimos

      // 2) Pintamos el mensaje provisional en pantalla
      const chatBox = document.getElementById("chat-messages");
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

      // 3) Construimos FormData ANTES de vaciar el textarea
      const formData = new FormData(formChat);

      // 4) Vaciamos el textarea para que el usuario pueda seguir escribiendo
      ta.value = "";

      // 5) Enviamos la petición POST al controlador
      try {
        const res = await fetch(
          "/TFGJavierLiroz/public/index.php?url=ChatControlador/enviar",
          { method: "POST", body: formData }
        );
        if (!res.ok) throw new Error("Status " + res.status);

        const json = await res.json();
        if (json.ok) {
          // Sustituimos el ID temporal por el ID definitivo
          divItem.setAttribute("data-id", json.id);
          // ——————> Aquí está la solución: actualizamos ultimoId
          // para que el polling no vuelva a traer esta misma línea
          ultimoId = json.id;
        } else {
          // Si ok = false, pintamos la burbuja provisional en rojo
          divItem.querySelector(".message-bubble").style.backgroundColor =
            "#dc3545";
        }
      } catch (err) {
        console.error("❌ Error al enviar mensaje:", err);
        // Opcionalmente, pintamos la burbuja provisional en rojo en caso de error
        divItem.querySelector(".message-bubble").style.backgroundColor =
          "#dc3545";
      }
    });
  }

  // ——— Polling: cada 3 s trae mensajes nuevos ———
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
        const esMio = m.id_remitente === miId;
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
        // Cuando traemos mensajes nuevos, avanzamos nuestro “ultimoId”
        ultimoId = m.id_mensaje;
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    } catch (err) {
      console.error("❌ Error en polling chat:", err);
    }
  }, 3000);

  window.addEventListener("beforeunload", () => clearInterval(intervalo));
});

/**/
document.addEventListener("DOMContentLoaded", () => {
  // (1) Inicio de tu código: envío de mensajes, polling, cerrar chat...
  // … (omitido aquí para centrar solo en scroll) …

  // ——— Después de tu código de envío/polling ———
  const chatBox = document.getElementById("chat-messages");
  const btnArriba = document.getElementById("btn-scroll-arriba");
  const btnAbajo = document.getElementById("btn-scroll-abajo");

  if (!chatBox || !btnArriba || !btnAbajo) return;

  // 1) Bajar al fondo la primera vez al cargar el historial completo
  window.requestAnimationFrame(() => {
    chatBox.scrollTop = chatBox.scrollHeight;
  });

  // 2) Mostrar u ocultar “↑/↓” si hay scroll
  function actualizarBotones() {
    if (chatBox.scrollHeight > chatBox.clientHeight + 5) {
      btnArriba.style.display = "inline-flex";
      btnAbajo.style.display = "inline-flex";
    } else {
      btnArriba.style.display = "none";
      btnAbajo.style.display = "none";
    }
  }
  actualizarBotones();
  chatBox.addEventListener("scroll", actualizarBotones);
  window.addEventListener("resize", actualizarBotones);

  // 3) Al pulsar “↑” o “↓”, movemos el scroll suave
  btnArriba.addEventListener("click", () => {
    chatBox.scrollTo({ top: 0, behavior: "smooth" });
  });
  btnAbajo.addEventListener("click", () => {
    chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: "smooth" });
  });
});
