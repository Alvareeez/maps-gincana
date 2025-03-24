document.addEventListener('DOMContentLoaded', () => {
    // Mostrar el modal para añadir una nueva prueba
    document.getElementById('btn-add-prueba').addEventListener('click', () => {
        document.getElementById('modal-title-prueba').innerText = 'Añadir Prueba';
        document.getElementById('prueba-form').reset();
        document.getElementById('prueba-id').value = '';
        document.getElementById('modal-prueba').style.display = 'flex';
    });

    // Cerrar el modal
    document.getElementById('close-modal-prueba').addEventListener('click', () => {
        document.getElementById('modal-prueba').style.display = 'none';
    });

    // Enviar formulario
    document.getElementById('prueba-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const pruebaId = document.getElementById('prueba-id').value;
        const formData = new FormData(document.getElementById('prueba-form'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Configuración común
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };

        if (pruebaId) {
            // Para edición - usamos POST con _method=PUT
            formData.append('_method', 'PUT');
            fetch(`/pruebas/${pruebaId}`, {
                method: 'POST',
                headers: headers,
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                alert('Prueba actualizada exitosamente');
                document.getElementById('modal-prueba').style.display = 'none';
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al actualizar la prueba: ' + error.message);
            });
        } else {
            // Para creación - POST normal
            fetch('/pruebas', {
                method: 'POST',
                headers: headers,
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                alert('Prueba creada exitosamente');
                document.getElementById('modal-prueba').style.display = 'none';
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al crear la prueba: ' + error.message);
            });
        }
    });

    // Editar una prueba
    document.querySelectorAll('.btn-edit-prueba').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const pruebaId = e.target.dataset.id;
            const pregunta = e.target.dataset.pregunta;
            const respuesta = e.target.dataset.respuesta;

            if (!pruebaId) {
                console.error('No se encontró el ID de la prueba.');
                return;
            }

            document.getElementById('modal-title-prueba').innerText = 'Editar Prueba';
            document.getElementById('prueba-form').reset();
            document.getElementById('prueba-id').value = pruebaId;
            document.getElementById('pregunta').value = pregunta;
            document.getElementById('respuesta').value = respuesta;
            document.getElementById('modal-prueba').style.display = 'flex';
        });
    });

    // Eliminar una prueba
    document.querySelectorAll('.btn-delete-prueba').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const pruebaId = e.target.dataset.id;

            if (confirm('¿Estás seguro de que deseas eliminar esta prueba?')) {
                fetch(`/pruebas/${pruebaId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la respuesta del servidor');
                    return response.json();
                })
                .then(data => {
                    alert('Prueba eliminada exitosamente');
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al eliminar la prueba: ' + error.message);
                });
            }
        });
    });
});