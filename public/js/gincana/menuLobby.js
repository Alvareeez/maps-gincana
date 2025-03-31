// Definir variables globales al inicio del archivo
let estaCargando = false;
let intervaloActualizacion = null;

document.addEventListener('DOMContentLoaded', function() {
    const contenedorGrupos = document.getElementById('contenedorGrupos');
    const gincanaId = window.location.pathname.split('/').pop();
    
    // Configurar URL correctamente usando la ruta definida en Laravel
    const gruposUrl = `/gincana/api/grupos-disponibles/${gincanaId}`;
    
    // Cargar inmediatamente
    actualizarGrupos(gruposUrl);
    
    // Configurar intervalo de actualización
    intervaloActualizacion = setInterval(() => actualizarGrupos(gruposUrl), 10000);
    
    // Limpiar intervalo al salir de la página
    window.addEventListener('beforeunload', limpiarIntervalo);
});

function limpiarIntervalo() {
    if (intervaloActualizacion) {
        clearInterval(intervaloActualizacion);
    }
}

async function actualizarGrupos(url) {
    // Evitar múltiples llamadas simultáneas
    if (estaCargando) {
        return;
    }
    
    estaCargando = true;
    const contenedorGrupos = document.getElementById('contenedorGrupos');
    
    try {
        // Mostrar estado de carga
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

        // Verificar si la respuesta es OK
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Parsear la respuesta JSON
        const {success, data, message, redirect} = await response.json();
        
        // Redirigir si es necesario
        if (redirect) {
            window.location.href = redirect;
            return;
        }

        // Manejar respuesta no exitosa
        if (!success) {
            throw new Error(message || 'Error al cargar grupos');
        }

        // Generar contenido HTML para los grupos disponibles
        let contenido = '';
        
        // Filtrar solo grupos disponibles
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
        
        // Actualizar el DOM
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
        // Restablecer estado
        estaCargando = false;
        contenedorGrupos.classList.remove('cargando');
    }
}