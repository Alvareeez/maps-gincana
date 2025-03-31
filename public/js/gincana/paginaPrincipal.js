// paginaPrincipal.js

let estaCargando = false;
let ultimaActualizacion = 0;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGincanas = document.getElementById('contenedorGincanas');

    actualizarGincanas();
    const intervalo = setInterval(actualizarGincanas, 10000);

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(intervalo);
        } else {
            actualizarGincanas();
            intervalo = setInterval(actualizarGincanas, 10000);
        }
    });
});

async function actualizarGincanas() {
    const ahora = Date.now();
    if (estaCargando || (ahora - ultimaActualizacion < 5000)) return;

    estaCargando = true;
    ultimaActualizacion = ahora;

    const contenedorGincanas = document.getElementById('contenedorGincanas');
    const gincanasUrl = contenedorGincanas.dataset.gincanasUrl;
    contenedorGincanas.classList.add('actualizando');

    try {
        const response = await fetch(gincanasUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const {success, data, message} = await response.json();
        if (!success) throw new Error(message || 'Error al cargar gincanas');

        let contenido = '';
        if (data && data.length > 0) {
            contenido = data.map(gincana => `
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/gincana/lobby/${gincana.id}" 
                       class="btn btn-outline-warning btn-block py-3 gincana-btn"
                       onclick="return confirmarUnirse(event, ${gincana.id})">
                        <strong>${gincana.nombre}</strong>
                        <div class="small mt-1">
                            ${gincana.cantidad_grupos} grupos · ${gincana.cantidad_jugadores} jugadores
                        </div>
                    </a>
                </div>
            `).join('');
        } else {
            contenido = `
                <div class="col-12">
                    <div class="alert alert-info alerta-gincana">
                        ${message || 'No hay gincanas disponibles en este momento.'}
                    </div>
                </div>
            `;
        }
        contenedorGincanas.innerHTML = contenido;

    } catch (error) {
        console.error('Error al cargar gincanas:', error);
        contenedorGincanas.innerHTML = `
            <div class="col-12">
                <p class="text-danger">Error al cargar gincanas</p>
                <p class="text-muted small">${error.message}</p>
                <button onclick="window.location.reload()" class="btn btn-sm btn-secondary">
                    Recargar
                </button>
            </div>
        `;
    } finally {
        contenedorGincanas.classList.remove('actualizando');
        estaCargando = false;
    }
}

// Solo comprueba si la gincana está ocupada
async function confirmarUnirse(event, gincanaId) {
    event.preventDefault();

    if (estaCargando) {
        alert('Por favor espera, se está procesando otra acción');
        return false;
    }

    estaCargando = true;

    try {
        const res = await fetch(`/gincana/api/gincana/${gincanaId}/estado`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await res.json();

        if (!data.disponible) {
            alert('Esta gincana ya ha comenzado. Intenta con otra.');
            window.location.reload();
            return false;
        }

        window.location.href = `/gincana/lobby/${gincanaId}`;
        return true;

    } catch (err) {
        alert('Error al verificar disponibilidad. Intenta de nuevo.');
        console.error(err);
        estaCargando = false;
        return false;
    }
}