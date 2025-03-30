document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // VARIABLES Y ELEMENTOS DEL DOM
    // =============================================
    const addModal = document.getElementById('modal-lugar-destacado');
    const editModal = document.getElementById('modal-edit-lugar-destacado');
    const addBtn = document.getElementById('btn-add-lugar-destacado');
    const closeAddModal = document.getElementById('close-modal-lugar-destacado');
    const closeEditModal = document.getElementById('close-modal-edit-lugar-destacado');
    const createForm = document.getElementById('lugar-destacado-form');
    const editForm = document.getElementById('edit-lugar-destacado-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // =============================================
    // FUNCIONALIDAD DE MODALES
    // =============================================
    
    const showModal = (modal) => modal.style.display = 'block';
    const closeModal = (modal) => modal.style.display = 'none';
    
    if (addBtn) addBtn.addEventListener('click', () => {
        createForm.reset();
        document.getElementById('lugar-destacado-id').value = '';
        document.getElementById('modal-title-lugar-destacado').textContent = 'Añadir Lugar Destacado';
        showModal(addModal);
    });

    if (closeAddModal) closeAddModal.addEventListener('click', () => closeModal(addModal));
    if (closeEditModal) closeEditModal.addEventListener('click', () => closeModal(editModal));

    window.addEventListener('click', (event) => {
        if (event.target === addModal) closeModal(addModal);
        if (event.target === editModal) closeModal(editModal);
    });

    // =============================================
    // FUNCIONES AUXILIARES
    // =============================================

    const showError = (message) => {
        alert('Error: ' + message);
        console.error(message);
    };

    const fillEditForm = (id, nombre, descripcion, direccion, latitud, longitud, tipoMarcador) => {
        document.getElementById('edit-lugar-destacado-id').value = id;
        document.getElementById('edit-nombreDestacado').value = nombre;
        document.getElementById('edit-descripcion').value = descripcion;
        document.getElementById('edit-direccion').value = direccion;
        document.getElementById('edit-latitudDestacada').value = latitud;
        document.getElementById('edit-longitudDestacada').value = longitud;
        document.getElementById('edit-tipo-marcador').value = tipoMarcador;
        showModal(editModal);
    };

    document.querySelectorAll('.btn-edit-lugar-destacado').forEach(btn => {
        btn.addEventListener('click', function() {
            fillEditForm(
                this.dataset.id,
                this.dataset.nombre,
                this.dataset.descripcion,
                this.dataset.direccion,
                this.dataset.latitud,
                this.dataset.longitud,
                this.dataset.tipoMarcador
            );
        });
    });

    // =============================================
    // MANEJO DE FORMULARIOS
    // =============================================

    const submitForm = async (form, url, method) => {
        const formData = new FormData(form);
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error en la solicitud');
            alert('Operación realizada con éxito');
            window.location.reload();
        } catch (error) {
            showError(error.message);
        }
    };

    if (createForm) {
        createForm.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm(createForm, '/admin/lugares-destacados', 'POST');
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-lugar-destacado-id').value;
            submitForm(editForm, `/admin/lugares-destacados/${id}`, 'POST');
        });
    }

    // =============================================
    // ELIMINACIÓN DE REGISTROS
    // =============================================

    document.querySelectorAll('.btn-delete-lugar-destacado').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            if (!confirm('¿Estás seguro de que deseas eliminar este lugar destacado?')) return;
            try {
                const response = await fetch(`/admin/lugares-destacados/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Error al eliminar');
                alert('Lugar destacado eliminado con éxito');
                window.location.reload();
            } catch (error) {
                showError(error.message);
            }
        });
    });
});
