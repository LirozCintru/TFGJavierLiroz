document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.getElementById("seccion-contenido");

  function cargarSeccion(seccion, params = "") {
    fetch(`/TFGJavierLiroz/public/index.php?url=ContenidoControlador/seccion/${seccion}${params}`)
      .then((resp) => resp.ok ? resp.text() : Promise.reject(resp))
      .then((html) => {
        contenedor.innerHTML = html;

        // Reactivar funciones específicas según la sección
        if (seccion === "inicio") {
          inicializarEventosPublicaciones();
        }
      })
      .catch(() => {
        contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar contenido.</div>';
      });
  }

  function inicializarEventosPublicaciones() {
    // Botón Ver más / Ver menos
    document.querySelectorAll(".toggle-contenido").forEach((btn) => {
      btn.addEventListener("click", () => {
        const card = btn.closest(".card-text");
        card.querySelector(".contenido-completo").classList.toggle("d-none");
        card.querySelector(".contenido-resumen").classList.toggle("d-none");
        btn.textContent = btn.textContent === "Ver más" ? "Ver menos" : "Ver más";
      });
    });

    // Paginación
    document.querySelectorAll(".pagination a.page-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const url = new URL(link.href);
        const pagina = url.searchParams.get("pagina") || "1";
        const tipo = url.searchParams.get("tipo") || "";
        const busqueda = url.searchParams.get("busqueda") || "";
        const departamento = url.searchParams.get("departamento") || "";
        const orden = url.searchParams.get("orden") || "";
        const limite = url.searchParams.get("limite") || "";

        const params = `?pagina=${pagina}&tipo=${tipo}&busqueda=${busqueda}&departamento=${departamento}&orden=${orden}&limite=${limite}`;
        cargarSeccion("inicio", params);
      });
    });
  }

  // Cargar sección inicial
  cargarSeccion("inicio");

  // Navegación por pestañas del navbar
  document.querySelectorAll("[data-section]").forEach((enlace) => {
    enlace.addEventListener("click", (e) => {
      e.preventDefault();
      const seccion = enlace.getAttribute("data-section");
      cargarSeccion(seccion);
      history.pushState({ seccion }, "", "#" + seccion);
    });
  });

  // Back / forward del navegador
  window.addEventListener("popstate", (e) => {
    if (e.state && e.state.seccion) {
      cargarSeccion(e.state.seccion);
    }
  });
});
