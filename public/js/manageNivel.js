document.addEventListener("DOMContentLoaded", function () {

    // Abrir el modal para añadir un nivel
    document.getElementById('btn-add-nivel').addEventListener('click', function () {
        document.getElementById('modal-nivel').style.display = 'flex';
    });

    // Cerrar el modal para añadir un nivel
    document.getElementById('close-modal-nivel').addEventListener('click', function () {
        document.getElementById('modal-nivel').style.display = 'none';
    });

    // Si se desea editar un nivel (esto se puede hacer de manera similar a los otros modales)
    // Este código podría gestionarse para obtener el id, pregunta, respuesta, etc., y luego cargar los datos en el formulario.

    // Ejemplo para escuchar la edición de un nivel:
    document.querySelectorAll('.btn-edit-nivel').forEach(button => {
        button.addEventListener('click', function () {
            const nivelId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const idLugar = this.getAttribute('data-id-lugar');
            const idPrueba = this.getAttribute('data-id-prueba');
            const idGincana = this.getAttribute('data-id-gincana');

            // Llenar el formulario de edición
            document.getElementById('edit-nivel-id').value = nivelId;
            document.getElementById('edit-nivel-nombre').value = nombre;
            document.getElementById('edit-nivel-id_lugar').value = idLugar;
            document.getElementById('edit-nivel-id_prueba').value = idPrueba;
            document.getElementById('edit-nivel-id_gincana').value = idGincana;

            // Abrir el modal de edición
            document.getElementById('modal-edit-nivel').style.display = 'flex';
        });
    });

    // Cerrar el modal de edición
    document.getElementById('close-modal-edit-nivel').addEventListener('click', function () {
        document.getElementById('modal-edit-nivel').style.display = 'none';
    });
});
