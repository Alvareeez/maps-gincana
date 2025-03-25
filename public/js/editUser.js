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
            

            // Asegúrate de que el userId no sea null o vacío
            if (userId) {
                // Rellenar el formulario con los datos del usuario
                document.getElementById('edit-user-id').value = userId;
                document.getElementById('edit-username').value = username;
                document.getElementById('edit-nombre').value = nombre;
                document.getElementById('edit-apellido').value = apellido;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-id_rol').value = idRol;

                // Mostrar el modal
                document.getElementById('modal-edit').style.display = 'flex';
            } else {
                console.error("El userId no es válido.");
            }
        });
    });

    // Cerrar el modal de edición
    document.getElementById('close-modal-edit').addEventListener('click', () => {
        document.getElementById('modal-edit').style.display = 'none';
    });

    // Enviar la solicitud de edición del usuario
    document.getElementById('edit-form').addEventListener('submit', (e) => {
        e.preventDefault();  // Evitar recargar la página

        const userId = document.getElementById('edit-user-id').value;
        const username = document.getElementById('edit-username').value;
        const nombre = document.getElementById('edit-nombre').value;
        const apellido = document.getElementById('edit-apellido').value;
        const email = document.getElementById('edit-email').value;
        const idRol = document.getElementById('edit-id_rol').value;
        

        if (!userId) {
            alert('ID de usuario no válido');
            return;  // Si el userId es inválido, no continuar con la solicitud
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Obtener CSRF Token

        // Crear el objeto con los datos del formulario
        const data = {
            username: username,
            nombre: nombre,
            apellido: apellido,
            email: email,
            id_rol: idRol,
        };


        // Enviar la solicitud de edición utilizando fetch
        fetch(`/admin/usuario/${userId}`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,  // Incluir el token CSRF
                'Content-Type': 'application/json', // Especificamos que los datos son JSON
            },
            body: JSON.stringify(data),  // Enviar los datos como JSON
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json();  // Si la respuesta es exitosa, convertir a JSON
        })
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
