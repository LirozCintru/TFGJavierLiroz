document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".toggle-detalle").forEach((titulo) => {
    titulo.addEventListener("click", () => {
      const id = titulo.dataset.target;
      const detalle = document.getElementById(id);

      if (!detalle) return;

      if (detalle.classList.contains("mostrar")) {
        // Oculta con animación
        detalle.classList.remove("mostrar");
        setTimeout(() => {
          detalle.style.display = "none";
        }, 300); // debe coincidir con el tiempo del CSS
      } else {
        // Muestra con animación
        detalle.style.display = "block";
        setTimeout(() => {
          detalle.classList.add("mostrar");
        }, 10); // deja tiempo para que el display se aplique antes del efecto
      }
    });
  });
});
