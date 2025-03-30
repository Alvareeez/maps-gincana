document.addEventListener('DOMContentLoaded', () => {
    // Mostrar sección por defecto (usuarios)
    showSection('usuarios');
    
    // Manejar clicks en los enlaces de la navbar
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const section = e.target.getAttribute('data-section');
            showSection(section);
            
            // Actualizar clase activa
            document.querySelectorAll('.nav-link').forEach(navLink => {
                navLink.classList.remove('active');
            });
            e.target.classList.add('active');
        });
    });
    
    // Función para mostrar la sección seleccionada
    function showSection(sectionId) {
        // Ocultar todas las secciones
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Mostrar la sección seleccionada
        const activeSection = document.getElementById(`${sectionId}-section`);
        if (activeSection) {
            activeSection.classList.remove('hidden');
            
            // Desplazamiento suave a la parte superior
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }
    
    // Activar el primer enlace por defecto
    const firstLink = document.querySelector('.nav-link');
    if (firstLink) {
        firstLink.classList.add('active');
    }
});
