document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // VARIABLES Y ELEMENTOS DEL DOM
    // =============================================
    const modal = document.getElementById('modal-etiqueta');
    const addBtn = document.getElementById('btn-add-etiqueta');
    const closeModal = document.getElementById('close-modal-etiqueta');
    const form = document.getElementById('etiqueta-form');
    const modalTitle = document.getElementById('modal-title-etiqueta');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // =============================================
    // FUNCIONALIDAD DE MODALES
    // =============================================
    
    // Mostrar modal para añadir
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            resetForm();
            modalTitle.textContent = 'Añadir Etiqueta';
            modal.style.display = 'block';
        });
    }

    // Cerrar modal
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Cerrar al hacer clic fuera del modal
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // =============================================
    // FUNCIONES AUXILIARES
    // =============================================

    function resetForm() {
        if (form) {
            form.reset();
            document.getElementById('etiqueta-id').value = '';
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

    // =============================================
    // MANEJO DE FORMULARIOS
    // =============================================

    // Configurar botones de editar
    document.querySelectorAll('.btn-edit-etiqueta').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            // Llenar formulario
            document.getElementById('etiqueta-id').value = id;
            document.getElementById('nombre-etiqueta').value = nombre;
            
            // Cambiar título del modal
            modalTitle.textContent = 'Editar Etiqueta';
            
            // Mostrar modal
            modal.style.display = 'block';
        });
    });

    // Manejar envío del formulario (crear/actualizar)
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('etiqueta-id').value;
            const url = id ? `/admin/etiquetas/${id}` : '/admin/etiquetas';
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
                        nombre: document.getElementById('nombre-etiqueta').value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al procesar la solicitud');
                }

                showSuccess(id ? 'Etiqueta actualizada correctamente' : 'Etiqueta creada correctamente');
                modal.style.display = 'none';
            } catch (error) {
                showError(error.message);
            }
        });
    }

    // =============================================
    // ELIMINACIÓN DE ETIQUETAS
    // =============================================

    document.querySelectorAll('.btn-delete-etiqueta').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.getAttribute('data-id');
            
            if (!confirm('¿Estás seguro de que deseas eliminar esta etiqueta?')) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/etiquetas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al eliminar la etiqueta');
                }

                showSuccess('Etiqueta eliminada correctamente');
            } catch (error) {
                showError(error.message);
            }
        });
    });
});