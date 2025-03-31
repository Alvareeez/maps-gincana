// menuLobby.js

let estaCargando = false;
let intervaloActualizacion = null;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGrupos = document.getElementById('contenedorGrupos');
    const gincanaId = window.location.pathname.split('/').pop();
    const gruposUrl = `/gincana/api/grupos-disponibles/${gincanaId}`;

    actualizarGrupos(gruposUrl);
    intervaloActualizacion = setInterval(() => actualizarGrupos(gruposUrl), 10000);

    window.addEventListener('beforeunload', limpiarIntervalo);

    document.addEventListener('submit', async function (event) {
        const form = event.target;
        if (!form.classList.contains('join-form')) return;

        event.preventDefault();

        const idGrupo = form.querySelector('input[name="id_grupo"]').value;
        const token = form.querySelector('input[name="_token"]').value;

        const confirmar = await verificarGrupoDisponible(idGrupo);

        if (confirmar) {
            form.submit();
        } else {
            alert('Este grupo ya está lleno o la gincana se ha cerrado.');
            actualizarGrupos(gruposUrl);
        }
    });
});

function limpiarIntervalo() {
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
    }
}

async function verificarGrupoDisponible(idGrupo) {
    try {
        const response = await fetch(`/gincana/api/grupo/${idGrupo}/disponibilidad`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();
        return data && data.disponible === true;
    } catch (error) {
        console.warn('Error verificando grupo:', error);
        return false;
    }
}

async function actualizarGrupos(url) {
    if (estaCargando) return;

    estaCargando = true;
    const contenedorGrupos = document.getElementById('contenedorGrupos');

    try {
        contenedorGrupos.classList.add('cargando');
        contenedorGrupos.innerHTML = `
            <div class="col-12 text-center">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando grupos...</p>
            </div>
        `;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const { success, data, message, redirect } = await response.json();

        if (redirect) {
            window.location.href = redirect;
            return;
        }

        if (!success) {
            contenedorGrupos.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        ${message || 'Error al cargar grupos'}
                    </div>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-secondary">
                        Recargar
                    </button>
                </div>
            `;
            return;
        }

        let contenido = '';
        const gruposDisponibles = data.filter(grupo => grupo.disponible);

        if (gruposDisponibles.length > 0) {
            contenido = gruposDisponibles.map(grupo => `
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <form action="/gincana/unirse" method="POST" class="join-form">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="id_grupo" value="${grupo.id}">
                        <button type="submit" class="btn btn-outline-warning btn-block py-3 gincana-btn">
                            <strong>${grupo.nombre}</strong>
                            <div class="small mt-1">
                                ${grupo.jugadores_actuales}/${grupo.max_jugadores} jugadores
                            </div>
                        </button>
                    </form>
                </div>
            `).join('');
        } else {
            contenido = `
                <div class="col-12">
                    <div class="alert alert-info">
                        No hay grupos disponibles en este momento.
                    </div>
                </div>
            `;
        }

        contenedorGrupos.innerHTML = contenido;

    } catch (error) {
        console.error('Error al cargar grupos:', error);
        contenedorGrupos.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    Error al cargar grupos: ${error.message}
                </div>
                <button onclick="window.location.reload()" class="btn btn-sm btn-secondary">
                    Recargar
                </button>
            </div>
        `;
    } finally {
        estaCargando = false;
        contenedorGrupos.classList.remove('cargando');
    }
}