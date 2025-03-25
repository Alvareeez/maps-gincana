document.addEventListener('DOMContentLoaded', () => {
    // Abrir el modal para añadir un nuevo lugar
    document.getElementById('btn-add-lugar').addEventListener('click', () => {
        document.getElementById('modal-title-lugar').innerText = 'Añadir Lugar';
        document.getElementById('lugar-form').reset();
        document.getElementById('lugar-id').value = ''; // Vaciar el ID para indicar creación
        document.getElementById('modal-lugar').style.display = 'flex';
    });

    // Cerrar el modal de añadir lugar
    document.querySelectorAll('#close-modal-lugar').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal-lugar').style.display = 'none';
        });
    });

    // Cargar datos al modal de edición
    document.querySelectorAll('.btn-edit-lugar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const btn = e.currentTarget;
            const lugarId = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const pista = btn.dataset.pista;
            const latitud = btn.dataset.latitud;
            const longitud = btn.dataset.longitud;

            // Rellenar el formulario de edición con los datos del lugar
            document.getElementById('modal-title-edit-lugar').innerText = 'Editar Lugar';
            document.getElementById('edit-lugar-id').value = lugarId;
            document.getElementById('edit-nombre-lugar').value = nombre;
            document.getElementById('edit-pista').value = pista;
            document.getElementById('edit-latitud').value = latitud;
            document.getElementById('edit-longitud').value = longitud;

            // Mostrar el modal de edición
            document.getElementById('modal-edit-lugar').style.display = 'flex';
        });
    });

    // Cerrar el modal de edición
    document.querySelectorAll('#close-modal-edit-lugar').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal-edit-lugar').style.display = 'none';
        });
    });

    // Enviar formulario de creación o edición
    const form = document.getElementById('lugar-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const lugarId = document.getElementById('lugar-id').value;
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            let url = '/lugares';
            let method = 'POST';

            // Si es edición, cambiar a método PUT
            if (lugarId) {
                url = `/lugares/${lugarId}`;
                method = 'POST'; // Laravel necesita POST con _method=PUT
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!response.ok) throw new Error('Error en la respuesta del servidor');

                alert(lugarId ? 'Lugar actualizado correctamente' : 'Lugar creado correctamente');
                document.getElementById('modal-lugar').style.display = 'none';
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
            }
        });
    }

    // Enviar formulario de edición
    const editForm = document.getElementById('edit-lugar-form');
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const lugarId = document.getElementById('edit-lugar-id').value;
            const formData = new FormData(editForm);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(`/lugares/${lugarId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                if (!response.ok) throw new Error('Error al actualizar el lugar');

                alert('Lugar actualizado correctamente');
                document.getElementById('modal-edit-lugar').style.display = 'none';
                location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
            }
        });
    }
});


// Eliminar lugar
document.querySelectorAll('.btn-delete-lugar').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        const lugarId = e.currentTarget.dataset.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Mostrar el cuadro de confirmación
        const confirmDelete = confirm('¿Estás seguro de que deseas eliminar este lugar?');

        // Si el usuario cancela (confirmDelete es false), no se realiza la eliminación
        if (!confirmDelete) return;  // Detener la ejecución si el usuario presiona "Cancelar"

        try {
            // Realizar la solicitud DELETE solo si el usuario confirma
            const response = await fetch(`/lugares/${lugarId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });

            if (!response.ok) throw new Error('Error al eliminar');

            alert('Lugar eliminado correctamente');
            location.reload(); // Recargar la página para reflejar la eliminación
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un error al eliminar el lugar.');
        }
    });
});
