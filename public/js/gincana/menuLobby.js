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
            contenido = '<div class="row justify-content-center">';
            data.respuesta.forEach(grupo => {
                contenido += `
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <form action="/gincana/api/unirse" method="POST">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                            <input type="hidden" name="id_grupo" value="${grupo.id}">
                            <button type="submit" class="btn btn-outline-warning btn-block py-3 gincana-btn">
                                <strong>${grupo.nombre}
                                (${grupo.jugadores_actuales}/${grupo.max_jugadores})
                                </strong>
                            </button>
                        </form>
                    </div>
                `;
            });
            contenido += '</div>';
        } else {
            contenido = `<div class="col-12"><p class="text-white">No se ha encontrado ningún grupo disponible.</p></div>`;
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

function mostrarGrupos(grupos) {
    const contenedorGrupos = document.getElementById('contenedorGrupos');
    contenedorGrupos.innerHTML = '';

    grupos.forEach(grupo => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/gincana/api/unirse';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const grupoInput = document.createElement('input');
        grupoInput.type = 'hidden';
        grupoInput.name = 'id_grupo';
        grupoInput.value = grupo.id;
        
        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'btn btn-primary w-100';
        button.textContent = `${grupo.nombre} (${grupo.jugadores_actuales}/${grupo.max_jugadores})`;
        
        form.appendChild(tokenInput);
        form.appendChild(grupoInput);
        form.appendChild(button);
        li.appendChild(form);
        contenedorGrupos.appendChild(li);
    });
}
