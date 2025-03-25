document.addEventListener('DOMContentLoaded', () => {
    // Abrir modal para añadir nivel
    document.getElementById('btn-add-nivel')?.addEventListener('click', () => {
        document.getElementById('modal-title-nivel').textContent = 'Añadir Nivel';
        document.getElementById('nivel-form').reset();
        document.getElementById('nivel-id').value = '';
        document.getElementById('modal-nivel').style.display = 'flex';
    });

    // Cerrar modales
    document.getElementById('close-modal-nivel')?.addEventListener('click', () => {
        document.getElementById('modal-nivel').style.display = 'none';
    });

    document.getElementById('close-modal-edit-nivel')?.addEventListener('click', () => {
        document.getElementById('modal-edit-nivel').style.display = 'none';
    });

    // Cargar datos para edición
    document.querySelectorAll('.btn-edit-nivel').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const btn = e.currentTarget;
            const nivelId = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const idLugar = btn.dataset.id_lugar; // Asegúrate que coincide con el HTML
            const idPrueba = btn.dataset.id_prueba;
            const idGincana = btn.dataset.id_gincana;

            // Rellenar formulario
            document.getElementById('edit-nivel-id').value = nivelId;
            document.getElementById('edit-nombre-nivel').value = nombre;
            
            // Establecer valores en los selects
            document.getElementById('edit-id_lugar').value = idLugar;
            document.getElementById('edit-id_prueba').value = idPrueba;
            document.getElementById('edit-id_gincana').value = idGincana;
            
            // Actualizar acción del formulario
            document.getElementById('edit-nivel-form').action = `/niveles/${nivelId}`;
            
            // Mostrar modal
            document.getElementById('modal-edit-nivel').style.display = 'flex';
        });
    });

    // Manejar envío de formulario de creación
    document.getElementById('nivel-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        try {
            const response = await fetch(e.target.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al crear el nivel');
            }

            alert(data.message);
            document.getElementById('modal-nivel').style.display = 'none';
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        }
    });

    // Manejar envío de formulario de edición
    document.getElementById('edit-nivel-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        try {
            const response = await fetch(e.target.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al actualizar el nivel');
            }

            alert(data.message);
            document.getElementById('modal-edit-nivel').style.display = 'none';
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        }
    });

    // Manejar eliminación de nivel
    document.querySelectorAll('.btn-delete-nivel').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            if (!confirm('¿Estás seguro de que deseas eliminar este nivel?')) return;

            const nivelId = e.currentTarget.dataset.id;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(`/niveles/${nivelId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al eliminar el nivel');
                }

                alert(data.message);
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
            }
        });
    });
});