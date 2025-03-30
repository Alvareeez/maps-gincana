document.addEventListener('DOMContentLoaded', function() {
    // ==============================================
    // VARIABLES Y ELEMENTOS DEL DOM
    // ==============================================
    const modal = document.getElementById('modal-tipo-marcador');
    const addBtn = document.getElementById('btn-add-tipo-marcador');
    const closeModal = document.getElementById('close-modal-tipo-marcador');
    const form = document.getElementById('tipo-marcador-form');
    const modalTitle = document.getElementById('modal-title-tipo-marcador');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ==============================================
    // FUNCIONALIDAD DE MODALES
    // ==============================================
    
    // Mostrar modal para añadir
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            resetForm();
            modalTitle.textContent = 'Añadir Tipo Marcador';
            modal.style.display = 'block';
        });
    }

    // Cerrar modal
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Cerrar modal al hacer clic fuera de él
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // ==============================================
    // FUNCIONES AUXILIARES
    // ==============================================
    
    function resetForm() {
        if (form) {
            form.reset();
            document.getElementById('tipo-marcador-id').value = '';
        }
    }

    function showError(message) {
        alert('Error: ' + message);
        console.error(message);
    }

    function showSuccess(message) {
        alert(message);
        window.location.reload(); // Recargar para ver los cambios
    }

    // ==============================================
    // MANEJO DE FORMULARIOS
    // ==============================================

    // Configurar botones de editar
    document.querySelectorAll('.btn-edit-tipo-marcador').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            // Llenar formulario
            document.getElementById('tipo-marcador-id').value = id;
            document.getElementById('nombre-tipo-marcador').value = nombre;
            
            // Cambiar título del modal
            modalTitle.textContent = 'Editar Tipo Marcador';
            
            // Mostrar modal
            modal.style.display = 'block';
        });
    });

    // Manejar envío del formulario (crear/actualizar)
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('tipo-marcador-id').value;
            const url = id ? `/admin/tipo-marcadores/${id}` : '/admin/tipo-marcadores'; // URL actualizada
            const method = id ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre: document.getElementById('nombre-tipo-marcador').value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al procesar la solicitud');
                }

                showSuccess(id ? 'Tipo Marcador actualizado correctamente' : 'Tipo Marcador creado correctamente');
                modal.style.display = 'none';
            } catch (error) {
                showError(error.message);
            }
        });
    }

    // ==============================================
    // ELIMINACIÓN DE TIPOS MARCADORES
    // ==============================================

    document.querySelectorAll('.btn-delete-tipo-marcador').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.getAttribute('data-id');
            
            if (!confirm('¿Estás seguro de que deseas eliminar este tipo de marcador?')) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/tipo-marcadores/${id}`, { // URL actualizada
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al eliminar el tipo de marcador');
                }

                showSuccess('Tipo Marcador eliminado correctamente');
            } catch (error) {
                showError(error.message);
            }
        });
    });
});
