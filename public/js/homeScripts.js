document.addEventListener("DOMContentLoaded", () => {
    // Lista de elementos que queremos mostrar con efecto de fade-in
    const elements = document.querySelectorAll(".logo, .title, .subtitle, .buttons, .fade-image");
    
    let delay = 200; // Tiempo inicial de delay en milisegundos
    elements.forEach((element) => {
        setTimeout(() => {
            element.style.opacity = "1";
            element.style.transform = "translateY(0)";
        }, delay);
        delay += 300; // Aumenta el delay para que aparezcan uno a uno
    });
});
