let estaCargando = false;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGrupos = document.getElementById('contenedorGrupos');
    let id = window.location.pathname.split('/').pop();
    actualizarGrupos(id);
    setInterval(function() {
        actualizarGrupos(id);
    }, 10000);
});

function actualizarGrupos(id) {
    if (estaCargando) {
        return;
    }
    estaCargando = true;

    fetch(`/gincana/api/gruposDisponibles/${id}`, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        let contenido = "";
        if (data.estado == 'no disponible') {
            window.location.href = '/gincana';
        } else if (data.estado == 'encontrado') {
            contenido = '<ul>';
            data.respuesta.forEach(grupo => {
                contenido += `
                    <li>
                        <a href="#">
                            ${grupo.nombre}
                        </a>
                        (x/x)
                    </li>
                `;
            });
            contenido += '</ul>';
        } else {
            contenido = '<p>No se ha encontrado ningún grupo disponible.</p>';
        }
        contenedorGrupos.innerHTML = contenido;
    })
    .catch(error => {
        console.error('Error al hacer la solicitud:', error);
        contenedorGrupos.innerHTML = '<p>Ha habido un error: ' + error + '</p>';
    })
    .finally(() => {
        estaCargando = false;
    });
}
