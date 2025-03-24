document.addEventListener('DOMContentLoaded', () => {

    // Mostrar el modal para añadir un nuevo usuario
    document.getElementById('btn-add').addEventListener('click', () => {
        document.getElementById('modal-title').innerText = 'Añadir Usuario';
        document.getElementById('user-form').reset();  // Limpiar el formulario
        document.getElementById('user-id').value = '';  // No tiene ID, ya que es un nuevo usuario
        document.getElementById('modal').style.display = 'flex';  // Mostrar el modal
    });

    // Cerrar el modal
    document.getElementById('close-modal').addEventListener('click', () => {
        document.getElementById('modal').style.display = 'none';  // Ocultar el modal
    });

    // Enviar los datos del formulario (crear un nuevo usuario)
    document.getElementById('user-form').addEventListener('submit', (e) => {
        e.preventDefault();  // Evitar recargar la página

        // Recoger los datos del formulario
        const formData = new FormData(document.getElementById('user-form'));

        // Enviar la solicitud utilizando fetch
        fetch('/admin/usuario', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': formData.get('_token'),  // Incluir el token CSRF
            },
            body: formData  // Enviar los datos del formulario
        })
        .then(response => response.json())  // Si la respuesta es exitosa, convertir a JSON
        .then(data => {
            alert('Usuario creado exitosamente');
            document.getElementById('modal').style.display = 'none';  // Cerrar el modal
            location.reload();  // Recargar la página para ver el nuevo usuario
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al crear el usuario.');
        });
    });
});
