document.addEventListener('DOMContentLoaded', () => {

    // Mostrar el modal para añadir un nuevo lugar
    document.getElementById('btn-add-lugar').addEventListener('click', () => {
        document.getElementById('modal-title-lugar').innerText = 'Añadir Lugar';
        document.getElementById('lugar-form').reset();
        document.getElementById('modal-lugar').style.display = 'flex';
        document.getElementById('lugar-id').value = ''; // Limpiar el ID para nuevo lugar
    });

    // Cerrar el modal de añadir lugar
    document.getElementById('close-modal-lugar').addEventListener('click', () => {
        document.getElementById('modal-lugar').style.display = 'none';
    });

    // Enviar formulario para añadir/editar lugar
    document.getElementById('lugar-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const lugarId = document.getElementById('lugar-id').value; // Obtener el ID del lugar
        const formData = new FormData(document.getElementById('lugar-form'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };

        let url, method;

        // Si existe un lugar-id, significa que es una actualización (PUT)
        if (lugarId) {
            url = `/lugares/${lugarId}`;
            method = 'PUT';
        } else {
            url = '/lugares';
            method = 'POST';
        }

        // Realizar la solicitud fetch con el método adecuado
        fetch(url, {
            method: method,
            headers: headers,
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            alert(lugarId ? 'Lugar actualizado exitosamente' : 'Lugar creado exitosamente');
            document.getElementById('modal-lugar').style.display = 'none';
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al procesar la solicitud: ' + error.message);
        });
    });

    // Editar lugar
    document.querySelectorAll('.btn-edit-lugar').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const lugarId = e.target.dataset.id;
            const pista = e.target.dataset.pista;
            const latitud = e.target.dataset.latitud;
            const longitud = e.target.dataset.longitud;

            document.getElementById('modal-title-lugar').innerText = 'Editar Lugar';
            document.getElementById('lugar-id').value = lugarId;
            document.getElementById('pista').value = pista;
            document.getElementById('latitud').value = latitud;
            document.getElementById('longitud').value = longitud;

            document.getElementById('modal-lugar').style.display = 'flex';
        });
    });

    // Cerrar el modal de edición
    document.getElementById('close-modal-lugar').addEventListener('click', () => {
        document.getElementById('modal-lugar').style.display = 'none';
    });

// Eliminar lugar
document.querySelectorAll('.btn-delete-lugar').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const lugarId = e.target.dataset.id; // Obtener el ID desde el atributo data-id
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Realizar la solicitud DELETE directamente
        fetch(`/lugares/${lugarId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Lugar eliminado exitosamente');
            window.location.href = '/admin'; // Redirigir a la página de administración
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al eliminar el lugar.');
        });
        
    });
});

});
