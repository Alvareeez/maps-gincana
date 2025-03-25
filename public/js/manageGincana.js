document.addEventListener('DOMContentLoaded', () => {
    // Abrir modal para añadir
    document.getElementById('btn-add-gincana')?.addEventListener('click', () => {
        document.getElementById('modal-title-gincana').textContent = 'Añadir Gincana';
        document.getElementById('gincana-form').reset();
        document.getElementById('gincana-id').value = '';
        document.getElementById('modal-gincana').style.display = 'flex';
    });

    // Cerrar modales
    document.getElementById('close-modal-gincana')?.addEventListener('click', () => {
        document.getElementById('modal-gincana').style.display = 'none';
    });
    
    document.getElementById('close-modal-edit-gincana')?.addEventListener('click', () => {
        document.getElementById('modal-edit-gincana').style.display = 'none';
    });

    // Cargar datos para edición
    document.querySelectorAll('.btn-edit-gincana').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const btn = e.currentTarget;
            
            // Obtener datos del botón
            const gincanaId = btn.getAttribute('data-id');
            const nombre = btn.getAttribute('data-nombre');
            const estado = btn.getAttribute('data-estado');
            const cantidadJugadores = btn.getAttribute('data-cantidad_jugadores');
            const cantidadGrupos = btn.getAttribute('data-cantidad_grupos');
            const idGanador = btn.getAttribute('data-id_ganador');

            // Establecer valores en el formulario
            document.getElementById('edit-gincana-id').value = gincanaId;
            document.getElementById('edit-nombre-gincana').value = nombre;
            document.getElementById('edit-estado').value = estado;
            document.getElementById('edit-cantidad_jugadores').value = cantidadJugadores;
            document.getElementById('edit-cantidad_grupos').value = cantidadGrupos;
            document.getElementById('edit-id_ganador').value = idGanador;
            
            // Actualizar acción del formulario
            document.getElementById('edit-gincana-form').action = `/gincanas/${gincanaId}`;
            
            // Mostrar modal
            document.getElementById('modal-edit-gincana').style.display = 'flex';
        });
    });

    // [El resto del código JavaScript permanece igual]
    // ... (manejo de formularios y eliminación)
});