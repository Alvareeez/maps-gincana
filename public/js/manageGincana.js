document.addEventListener('DOMContentLoaded', () => {
    // 1. Abrir modal para añadir gincana
    const btnAddGincana = document.getElementById('btn-add-gincana');
    if (btnAddGincana) {
        btnAddGincana.addEventListener('click', () => {
            document.getElementById('modal-title-gincana').textContent = 'Añadir Gincana';
            document.getElementById('gincana-form').reset();
            document.getElementById('gincana-id').value = '';
            document.getElementById('modal-gincana').style.display = 'flex';
        });
    }

    // 2. Cerrar modales
    const closeModalGincana = document.getElementById('close-modal-gincana');
    if (closeModalGincana) {
        closeModalGincana.addEventListener('click', () => {
            document.getElementById('modal-gincana').style.display = 'none';
        });
    }

    const closeModalEditGincana = document.getElementById('close-modal-edit-gincana');
    if (closeModalEditGincana) {
        closeModalEditGincana.addEventListener('click', () => {
            document.getElementById('modal-edit-gincana').style.display = 'none';
        });
    }

    // 3. Cargar datos para edición
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

            // Debug: Mostrar valores en consola
            console.log('Datos de la gincana:', {
                gincanaId, nombre, estado, cantidadJugadores, cantidadGrupos, idGanador
            });

            // Establecer valores en el formulario
            document.getElementById('edit-gincana-id').value = gincanaId;
            document.getElementById('edit-nombre-gincana').value = nombre;
            document.getElementById('edit-estado').value = estado;
            document.getElementById('edit-cantidad_jugadores').value = cantidadJugadores;
            document.getElementById('edit-cantidad_grupos').value = cantidadGrupos;
            document.getElementById('edit-id_ganador').value = idGanador;
            
            // Actualizar acción del formulario
            const editForm = document.getElementById('edit-gincana-form');
            if (editForm) {
                editForm.action = `/gincanas/${gincanaId}`;
            }
            
            // Mostrar modal
            document.getElementById('modal-edit-gincana').style.display = 'flex';
        });
    });

    // 4. Manejar envío de formulario de creación
    const gincanaForm = document.getElementById('gincana-form');
    if (gincanaForm) {
        gincanaForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(gincanaForm);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(gincanaForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al crear la gincana');
                }

                alert(data.message);
                document.getElementById('modal-gincana').style.display = 'none';
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            }
        });
    }

    // 5. Manejar envío de formulario de edición
    const editGincanaForm = document.getElementById('edit-gincana-form');
    if (editGincanaForm) {
        editGincanaForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(editGincanaForm);
            formData.append('_method', 'PUT'); // Para Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(editGincanaForm.action, {
                    method: 'POST', // Laravel necesita POST para PUT con _method
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar la gincana');
                }

                alert(data.message);
                document.getElementById('modal-edit-gincana').style.display = 'none';
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            }
        });
    }

    // 6. Manejar eliminación de gincana (VERSIÓN CORREGIDA)
    document.querySelectorAll('.btn-delete-gincana').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const gincanaId = e.currentTarget.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Debug
            console.log(`Intentando eliminar gincana ID: ${gincanaId}`);
            console.log(`Token CSRF: ${csrfToken}`);

            if (!confirm('¿Estás seguro de eliminar esta gincana?')) {
                return;
            }

            try {
                const response = await fetch(`/gincanas/${gincanaId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                // Verificar si la respuesta es exitosa
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al eliminar la gincana');
                }

                const data = await response.json();
                
                // Mostrar mensaje de éxito
                alert(data.message || 'Gincana eliminada correctamente');
                
                // Opción 1: Recargar la página
                location.reload();
                
                // Opción 2: Eliminar la fila directamente (sin recargar)
                // e.currentTarget.closest('tr').remove();

            } catch (error) {
                console.error('Error al eliminar la gincana:', error);
                alert('Error: ' + error.message);
            }
        });
    });
});