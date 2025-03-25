let estaCargando = false;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGincanas = document.getElementById('contenedorGincanas');
    actualizarGincanas();
    setInterval(function() {
        actualizarGincanas();
    }, 10000);
});

function actualizarGincanas() {
    if (estaCargando) return;
    estaCargando = true;

    // 1. Añadir una clase de "parpadeo" para indicar que se está actualizando
    contenedorGincanas.classList.add('actualizando');
    
    fetch('/gincana/api/gincanasAbiertas', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        let contenido = "";
        if (data.estado == 'encontrado') {
            data.respuesta.forEach((gincana) => {
                contenido += `
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <a href="/gincana/lobby/${gincana.id}" class="btn btn-outline-warning btn-block py-3 gincana-btn">
                            <strong>${gincana.nombre}</strong>
                        </a>
                        <p class="mt-2 text-white">
                            (${gincana.cantidad_grupos} grupos de ${gincana.cantidad_jugadores} jugadores)
                        </p>
                    </div>
                `;
            });
        } else {
            contenido = `<div class="col-12"><p class="text-white">No hay gincanas abiertas.</p></div>`;
        }

        // 2. Actualizar el contenido y quitar la clase de actualización
        contenedorGincanas.innerHTML = contenido;
        contenedorGincanas.classList.remove('actualizando');
    })
    .catch(error => {
        console.error('Error:', error);
        contenedorGincanas.innerHTML = `
            <div class="col-12">
                <p class="text-danger">Error al cargar gincanas: ${error.message}</p>
            </div>
        `;
        contenedorGincanas.classList.remove('actualizando');
    })
    .finally(() => {
        estaCargando = false;
    });
}
