document.addEventListener('DOMContentLoaded', () => {
    // Modal de añadir
    const btnAdd = document.getElementById('btn-add-tipo-marcador');
    const modal = document.getElementById('modal-tipo-marcador');
    const closeModal = document.getElementById('close-modal-tipo-marcador');

    if (btnAdd && modal) {
        btnAdd.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Formulario de guardar
    const form = document.getElementById('tipo-marcador-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const id = formData.get('id');

            fetch(id ? `/admin/tipo-marcadores/${id}` : '/admin/tipo-marcadores', {
                method: id ? 'PUT' : 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        });
    }
});