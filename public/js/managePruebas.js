document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // Abrir modal de añadir prueba
    document.getElementById("btn-add-prueba").addEventListener("click", function () {
        document.getElementById("modal-prueba").style.display = "flex";
        document.getElementById("prueba-id").value = "";
        document.getElementById("pregunta").value = "";
        document.getElementById("respuesta").value = "";
    });

    // Cerrar modal
    document.getElementById("close-modal-prueba").addEventListener("click", function () {
        document.getElementById("modal-prueba").style.display = "none";
    });

    // Guardar prueba (Añadir o Editar)
    document.getElementById("prueba-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("prueba-id").value;
        const pregunta = document.getElementById("pregunta").value;
        const respuesta = document.getElementById("respuesta").value;
        const url = id ? `/pruebas/${id}` : "/pruebas";
        const method = id ? "PUT" : "POST";

        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ pregunta, respuesta }),
        })
            .then(response => response.json())
            .then(data => {
                alert(id ? "Prueba actualizada" : "Prueba añadida");
                location.reload();
            })
            .catch(error => console.error("Error:", error));
    });

    // Cargar datos para editar prueba
    document.querySelectorAll(".btn-edit-prueba").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("modal-prueba").style.display = "flex";
            document.getElementById("prueba-id").value = this.dataset.id;
            document.getElementById("pregunta").value = this.dataset.pregunta;
            document.getElementById("respuesta").value = this.dataset.respuesta;
        });
    });

    // Eliminar prueba
    document.querySelectorAll(".btn-delete-prueba").forEach(button => {
        button.addEventListener("click", function () {
            const id = this.dataset.id;

            if (confirm("¿Seguro que quieres eliminar esta prueba?")) {
                fetch(`/pruebas/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        alert("Prueba eliminada");
                        location.reload();
                    })
                    .catch(error => console.error("Error:", error));
            }
        });
    });
});
