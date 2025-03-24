let estaCargando = false;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGincanas = document.getElementById('contenedorGincanas');
    actualizarGincanas();
    setInterval(function() {
        actualizarGincanas();
    }, 10000);
});

function actualizarGincanas() {
    if (estaCargando) {
        return;
    }
    estaCargando = true;

    fetch('/gincana/api/gincanasAbiertas', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        let contenido = "";
        if (data.estado == 'encontrado') {
            contenido = '<ul>';
            data.respuesta.forEach(gincana => {
                contenido += `
                    <li>
                        <a href="/gincana/lobby/${gincana.id}">
                            ${gincana.nombre}
                        </a>
                        (${gincana.cantidad_grupos} grupos de ${gincana.cantidad_jugadores} jugadores)
                    </li>
                `;
            });
            contenido += '</ul>';
        } else {
            contenido = '<p>No se ha encontrado ninguna gincana abierta.</p>';
        }
        contenedorGincanas.innerHTML = contenido;
    })
    .catch(error => {
        console.error('Error al hacer la solicitud:', error);
        contenedorGincanas.innerHTML = '<p>Ha habido un error: ' + error + '</p>';
    })
    .finally(() => {
        estaCargando = false;
    });
}
