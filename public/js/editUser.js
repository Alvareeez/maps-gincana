document.addEventListener('DOMContentLoaded', () => {

    // Abrir el modal para editar un usuario
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', (e) => {
            const userId = e.target.getAttribute('data-id');
            const username = e.target.getAttribute('data-username');
            const nombre = e.target.getAttribute('data-nombre');
            const apellido = e.target.getAttribute('data-apellido');
            const email = e.target.getAttribute('data-email');
            const idRol = e.target.getAttribute('data-idrol');

            // Rellenar el formulario con los datos del usuario
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-apellido').value = apellido;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-id_rol').value = idRol;

            // Mostrar el modal
            document.getElementById('modal-edit').style.display = 'flex';
        });
    });

    // Cerrar el modal de edición
    document.getElementById('close-modal-edit').addEventListener('click', () => {
        document.getElementById('modal-edit').style.display = 'none';  // Ocultar el modal
    });

    // Enviar la solicitud de edición del usuario
    document.getElementById('edit-form').addEventListener('submit', (e) => {
        e.preventDefault();  // Evitar recargar la página

        const formData = new FormData(document.getElementById('edit-form'));
        const userId = formData.get('user-id');

        // Enviar la solicitud de edición utilizando fetch
        fetch(`/admin/usuario/${userId}`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData.get('_token'),  // Incluir el token CSRF
            },
            body: formData,  // Enviar los datos del formulario
        })
        .then(response => response.json())  // Si la respuesta es exitosa, convertir a JSON
        .then(data => {
            alert('Usuario actualizado exitosamente');
            document.getElementById('modal-edit').style.display = 'none';  // Cerrar el modal
            location.reload();  // Recargar la página para ver los cambios
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al actualizar el usuario.');
        });
    });

});
