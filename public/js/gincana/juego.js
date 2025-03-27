class JuegoGincana {
    constructor(gincanaId) {
        this.gincanaId = gincanaId;
        this.contenedorEstado = document.getElementById('estado-juego');
        this.mapa = null;
        this.radioVisible = null;
        this.marcadorNivel = null;
        this.modalPista = null;
        this.init();
    }

    init() {
        this.mostrarLoader();
        this.actualizarEstadoJuego();
        this.crearModalPista();
    }

    crearModalPista() {
        // Crear estructura del modal
        const modalHTML = `
        <div class="modal fade" id="modalPista" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pista del nivel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="contenido-pista">
                        Cargando pista...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>`;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modalPista = new bootstrap.Modal(document.getElementById('modalPista'));
    }

    mostrarLoader() {
        this.contenedorEstado.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando estado del juego...</p>
            </div>
        `;
    }

    async actualizarEstadoJuego() {
        try {
            const response = await fetch(`/gincana/api/estado-juego/${this.gincanaId}`);
            
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            const data = await response.json();
            if (!data.estado) throw new Error('Respuesta del servidor incompleta');

            this.procesarRespuesta(data);
        } catch (error) {
            console.error('Error:', error);
            this.mostrarError(error.message);
            setTimeout(() => this.actualizarEstadoJuego(), 10000);
        }
    }

    procesarRespuesta(data) {
        switch(data.estado) {
            case 'iniciado':
                this.mostrarJuegoIniciado(data);
                break;
            case 'esperando':
                this.mostrarEstadoEsperando(data);
                setTimeout(() => this.actualizarEstadoJuego(), 10000);
                break;
            default:
                throw new Error(`Estado desconocido: ${data.estado}`);
        }
    }

    mostrarJuegoIniciado(data) {
        this.contenedorEstado.innerHTML = `
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0">¡El juego ha comenzado!</h4>
                        </div>
                        <div class="card-body">
                            <button id="btn-mostrar-pista" class="btn btn-primary mb-3">
                                <i class="fas fa-eye"></i> Mostrar pista
                            </button>
                            <div id="mapa-container" style="height: 500px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Configurar el modal de pista
        document.getElementById('btn-mostrar-pista').addEventListener('click', () => {
            document.getElementById('contenido-pista').innerHTML = data.pista || 'No hay pista disponible';
            this.modalPista.show();
        });

        // Inicializar mapa
        this.inicializarMapa(data.ubicacionNivel);
    }

    inicializarMapa(ubicacion) {
        // Coordenadas por defecto si no vienen en los datos
        const coords = ubicacion || { lat: 40.4168, lng: -3.7038 }; // Madrid como fallback
        
        this.mapa = L.map('mapa-container').setView([coords.lat, coords.lng], 16);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.mapa);

        // Radio de 50 metros (amarillo transparente)
        this.radioVisible = L.circle([coords.lat, coords.lng], {
            radius: 50,
            color: '#ffc107',
            fillColor: '#ffc107',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(this.mapa);

        // Marcador oculto inicialmente
        this.marcadorNivel = L.marker([coords.lat, coords.lng], {
            opacity: 0,
            icon: L.icon({
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(this.mapa);

        // Verificar posición del jugador (simulada)
        this.simularMovimientoJugador(coords);
    }

    simularMovimientoJugador(objetivoCoords) {
        // En una aplicación real, esto vendría del GPS del dispositivo
        navigator.geolocation.watchPosition(
            (position) => {
                const jugadorCoords = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Calcular distancia al objetivo
                const distancia = this.calcularDistancia(
                    jugadorCoords.lat, jugadorCoords.lng,
                    objetivoCoords.lat, objetivoCoords.lng
                );
                
                // Mostrar/ocultar marcador según distancia
                if (distancia <= 50) {
                    this.marcadorNivel.setOpacity(1);
                    this.marcadorNivel.bindPopup(`
                        <h5>¡Has llegado!</h5>
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); document.getElementById('btn-mostrar-pista').click()">
                            Ver pista
                        </button>
                    `).openPopup();
                } else {
                    this.marcadorNivel.setOpacity(0);
                    this.marcadorNivel.closePopup();
                }
                
                // Mover el círculo de visibilidad (opcional)
                this.radioVisible.setLatLng([jugadorCoords.lat, jugadorCoords.lng]);
            },
            (error) => {
                console.error('Error GPS:', error);
                // Simulación para desarrollo
                const simulacionInterval = setInterval(() => {
                    const randomOffset = () => (Math.random() - 0.5) * 0.01;
                    const fakeCoords = {
                        lat: objetivoCoords.lat + randomOffset(),
                        lng: objetivoCoords.lng + randomOffset()
                    };
                    
                    const distancia = this.calcularDistancia(
                        fakeCoords.lat, fakeCoords.lng,
                        objetivoCoords.lat, objetivoCoords.lng
                    );
                    
                    if (distancia <= 50) {
                        this.marcadorNivel.setOpacity(1);
                        clearInterval(simulacionInterval);
                    }
                }, 2000);
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
        );
    }

    calcularDistancia(lat1, lon1, lat2, lon2) {
        // Fórmula Haversine para calcular distancia en metros
        const R = 6371e3; // Radio de la Tierra en metros
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    mostrarEstadoEsperando(data) {
        if (!data.grupos || !Array.isArray(data.grupos)) {
            throw new Error('Formato de grupos inválido');
        }

        const gruposHTML = data.grupos.map(grupo => this.crearTarjetaGrupo(grupo)).join('');
        
        this.contenedorEstado.innerHTML = `
            <div class="alert alert-info">
                <h4 class="text-center mb-3">${data.mensaje}</h4>
                <div class="row">
                    ${gruposHTML}
                </div>
            </div>
        `;

        setTimeout(() => this.actualizarEstadoJuego(), 10000);
    }

    crearTarjetaGrupo(grupo) {
        const porcentaje = Math.round((grupo.jugadores / grupo.max_jugadores) * 100);
        const esMiGrupo = grupo.es_mi_grupo ? 'border-primary border-3 shadow-sm' : '';
        const badgeMiGrupo = grupo.es_mi_grupo ? 
            '<span class="badge bg-primary float-end">Tú estás aquí</span>' : '';

        return `
            <div class="col-md-6 mb-3">
                <div class="card h-100 ${esMiGrupo}">
                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            ${grupo.nombre}
                            ${badgeMiGrupo}
                        </h5>
                        <div class="mb-2">
                            <span class="fw-bold">${grupo.jugadores}</span>
                            <span class="text-muted">/${grupo.max_jugadores} jugadores</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-success" 
                                 role="progressbar" 
                                 style="width: ${porcentaje}%" 
                                 aria-valuenow="${porcentaje}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="text-end mt-1 small text-muted">
                            ${porcentaje}% completo
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    mostrarError(mensaje) {
        this.contenedorEstado.innerHTML = `
            <div class="alert alert-danger">
                <h4 class="alert-heading">Error</h4>
                <p>${mensaje}</p>
                <hr>
                <div class="d-flex justify-content-between">
                    <a href="/gincana" class="btn btn-sm btn-outline-primary">
                        Volver a gincanas
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Recargar
                    </button>
                </div>
            </div>
        `;
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    try {
        const scriptElement = document.querySelector('script[data-gincana-id]');
        if (!scriptElement) throw new Error('Elemento script no encontrado');
        
        const gincanaId = scriptElement.getAttribute('data-gincana-id');
        if (!gincanaId) throw new Error('ID de gincana no especificado');
        
        // Cargar CSS de Leaflet
        const leafletCSS = document.createElement('link');
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
        document.head.appendChild(leafletCSS);
        
        // Cargar JS de Leaflet
        const leafletJS = document.createElement('script');
        leafletJS.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
        leafletJS.onload = () => new JuegoGincana(gincanaId);
        document.body.appendChild(leafletJS);
        
    } catch (error) {
        console.error('Error al inicializar el juego:', error);
        document.getElementById('estado-juego').innerHTML = `
            <div class="alert alert-danger">
                Error crítico: ${error.message}
            </div>
        `;
    }
});