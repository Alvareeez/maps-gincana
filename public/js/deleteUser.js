document.addEventListener('DOMContentLoaded', () => {

    // Eliminar usuario
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', (e) => {
            const userId = e.target.getAttribute('data-id');
            
            // Confirmación antes de eliminar
            if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                // Obtener el token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Enviar la solicitud DELETE utilizando fetch
                fetch(`/admin/usuario/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,  // Incluir el token CSRF
                    }
                })
                .then(response => response.json())  // Si la respuesta es exitosa, convertir a JSON
                .then(data => {
                    alert('Usuario eliminado exitosamente');
                    location.reload();  // Recargar la página para ver los cambios
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al eliminar el usuario.');
                });
            }
        });
    });

});
