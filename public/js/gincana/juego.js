class JuegoGincana {
    constructor(gincanaId) {
        this.gincanaId = gincanaId;
        this.contenedorEstado = document.getElementById('estado-juego');
        this.mapa = null;
        this.marcadores = {};
        this.watchId = null;
        this.ultimaPosicion = null;
        this.nivelActual = 1;
        this.modales = {};
        this.gpsIntentos = 0;

        this.init();
    }

    async init() {
        this.crearModales();
        await this.cargarEstadoJuego();
    }

    crearModales() {
        const modals = {
            pista: {
                title: 'Pista del Nivel',
                body: '<div id="contenido-pista">Cargando pista...</div>',
                footer: '<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>'
            },
            pregunta: {
                title: 'Pregunta del Nivel',
                body: `
                    <div class="alert alert-info" id="texto-pregunta">Cargando pregunta...</div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" id="respuesta-jugador" placeholder="Escribe tu respuesta">
                        <label for="respuesta-jugador">Tu respuesta</label>
                    </div>
                    <div class="invalid-feedback" id="feedback-respuesta"></div>
                `,
                footer: `
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" id="btn-enviar-respuesta">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner-respuesta"></span>
                        Enviar respuesta
                    </button>
                `
            }
        };

        Object.entries(modals).forEach(([id, config]) => {
            const modalHTML = `
                <div class="modal fade" id="modal-${id}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">${config.title}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">${config.body}</div>
                            <div class="modal-footer">${config.footer}</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            this.modales[id] = new bootstrap.Modal(document.getElementById(`modal-${id}`));
        });

        // Event listeners
        document.getElementById('btn-enviar-respuesta').addEventListener('click', () => this.procesarRespuesta());
        document.getElementById('respuesta-jugador').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.procesarRespuesta();
        });
    }

    async cargarEstadoJuego() {
        this.mostrarLoader('Cargando estado del juego...');
        
        try {
            const response = await fetch(`/gincana/api/estado-juego/${this.gincanaId}`);
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            if (data.estado === 'iniciado') {
                await this.iniciarJuego();
            } else if (data.estado === 'esperando') {
                this.mostrarEstadoEsperando(data);
                setTimeout(() => this.cargarEstadoJuego(), 5000);
            } else {
                throw new Error('Estado del juego desconocido');
            }
        } catch (error) {
            this.mostrarError(`Error al cargar el estado: ${error.message}`);
        }
    }

    async iniciarJuego() {
        try {
            const response = await fetch(`/gincana/api/nivel-actual/${this.gincanaId}`);
            if (!response.ok) throw new Error('Error al cargar nivel');
            
            const data = await response.json();
            
            if (data.estado === 'completado') {
                return this.mostrarFinJuego(data.ganador);
            }
            
            this.nivelActual = data.nivel;
            this.mostrarInterfazJuego(data);
            
            if (!data.ubicacion) throw new Error('Ubicación no válida');
            
            // Actualizar modales
            document.getElementById('contenido-pista').textContent = data.pista;
            document.getElementById('texto-pregunta').textContent = data.pregunta;
            
            // Inicializar mapa
            this.inicializarMapa(data.ubicacion);
            
            // Mostrar pista al inicio
            setTimeout(() => this.modales.pista.show(), 1000);
            
        } catch (error) {
            this.mostrarError(`Error al iniciar juego: ${error.message}`);
        }
    }

    mostrarInterfazJuego(data) {
        this.contenedorEstado.innerHTML = `
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>Nivel ${this.nivelActual}
                    </h4>
                    <div>
                        <button id="btn-mostrar-pista" class="btn btn-light">
                            <i class="fas fa-eye me-2"></i>Ver Pista
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-container" style="height: 500px; width: 100%;"></div>
                    <div class="p-3 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Acércate al marcador para ver la pregunta</small>
                            <span class="badge bg-primary">Nivel ${this.nivelActual}/4</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('btn-mostrar-pista').addEventListener('click', () => {
            this.modales.pista.show();
        });
    }

    inicializarMapa(ubicacionObjetivo) {
        if (!navigator.geolocation) {
            return this.mapaFallback(ubicacionObjetivo);
        }

        const opcionesGPS = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
            (position) => this.configurarMapa(position, ubicacionObjetivo),
            (error) => this.manejarErrorGPS(error, ubicacionObjetivo),
            opcionesGPS
        );
    }

    configurarMapa(position, objetivo) {
        const jugadorCoords = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy
        };

        this.ultimaPosicion = jugadorCoords;
        
        // Configurar mapa
        this.mapa = L.map('mapa-container', {
            zoomControl: false,
            tap: false
        }).setView([jugadorCoords.lat, jugadorCoords.lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.mapa);

        L.control.zoom({ position: 'topright' }).addTo(this.mapa);

        // Marcador del jugador
        this.marcadores.jugador = L.marker([jugadorCoords.lat, jugadorCoords.lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            }),
            zIndexOffset: 1000
        }).addTo(this.mapa).bindPopup('Tu ubicación actual');

        // Círculo de precisión
        this.marcadores.precision = L.circle([jugadorCoords.lat, jugadorCoords.lng], {
            radius: jugadorCoords.accuracy,
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.2
        }).addTo(this.mapa);

        // Radio visible (50m)
        this.marcadores.radio = L.circle([objetivo.latitud, objetivo.longitud], {
            radius: 50,
            color: '#ffc107',
            fillColor: '#ffc107',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(this.mapa);

        // Marcador del objetivo (inicialmente invisible)
        this.marcadores.objetivo = L.marker([objetivo.latitud, objetivo.longitud], {
            opacity: 0,
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            }),
            zIndexOffset: 900
        }).addTo(this.mapa);

        // Iniciar seguimiento
        this.iniciarSeguimientoPosicion(objetivo);
    }

    iniciarSeguimientoPosicion(objetivo) {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
        }

        this.watchId = navigator.geolocation.watchPosition(
            (position) => this.actualizarPosicion(position, objetivo),
            (error) => this.mostrarErrorGPS(error),
            { 
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 10000
            }
        );
    }

    actualizarPosicion(position, objetivo) {
        const jugadorCoords = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy
        };

        this.ultimaPosicion = jugadorCoords;
        
        // Actualizar marcador y círculo de precisión
        if (this.marcadores.jugador) {
            this.marcadores.jugador.setLatLng([jugadorCoords.lat, jugadorCoords.lng]);
        }
        
        if (this.marcadores.precision) {
            this.marcadores.precision.setLatLng([jugadorCoords.lat, jugadorCoords.lng])
                .setRadius(jugadorCoords.accuracy);
        }
        
        // Calcular distancia al objetivo
        const distancia = this.calcularDistancia(
            jugadorCoords.lat, jugadorCoords.lng,
            objetivo.latitud, objetivo.longitud
        );
        
        // Mostrar/ocultar marcador según distancia
        if (this.marcadores.objetivo) {
            if (distancia <= 50) {
                this.marcadores.objetivo.setOpacity(1);
                this.marcadores.objetivo.bindPopup(`
                    <div class="text-center">
                        <h5>¡Objetivo encontrado!</h5>
                        <p class="mb-2">Estás a ${Math.round(distancia)} metros</p>
                        <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); window.juegoGincana.mostrarPregunta()">
                            <i class="fas fa-question-circle me-1"></i>Responder pregunta
                        </button>
                    </div>
                `).openPopup();
            } else {
                this.marcadores.objetivo.setOpacity(0);
                this.marcadores.objetivo.closePopup();
            }
        }
        
        // Centrar mapa si nos alejamos mucho
        if (this.mapa) {
            const centro = this.mapa.getCenter();
            const distanciaDesdeCentro = this.calcularDistancia(
                jugadorCoords.lat, jugadorCoords.lng,
                centro.lat, centro.lng
            );
            
            if (distanciaDesdeCentro > 100) {
                this.mapa.panTo([jugadorCoords.lat, jugadorCoords.lng]);
            }
        }
    }

    mostrarPregunta() {
        const respuestaInput = document.getElementById('respuesta-jugador');
        if (respuestaInput) {
            respuestaInput.value = '';
            respuestaInput.classList.remove('is-invalid');
        }
        
        this.modales.pregunta.show();
    }

    async procesarRespuesta() {
        const respuestaInput = document.getElementById('respuesta-jugador');
        const respuesta = respuestaInput?.value.trim() || '';
        const spinner = document.getElementById('spinner-respuesta');
        const btnEnviar = document.getElementById('btn-enviar-respuesta');
        
        if (!respuesta) {
            if (respuestaInput) respuestaInput.classList.add('is-invalid');
            const feedback = document.getElementById('feedback-respuesta');
            if (feedback) feedback.textContent = 'Por favor, introduce una respuesta';
            return;
        }
        
        if (spinner) spinner.classList.remove('d-none');
        if (btnEnviar) btnEnviar.disabled = true;
        
        try {
            const response = await fetch(`/gincana/api/responder/${this.gincanaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ respuesta })
            });
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            if (data.estado === 'correcto') {
                this.mostrarFeedback('¡Respuesta correcta!', 'success');
                this.modales.pregunta.hide();
                
                if (data.completado) {
                    this.mostrarLoader('Esperando al resto del grupo...');
                    setTimeout(() => this.iniciarJuego(), 5000);
                }
            } else if (data.estado === 'nivel_completado') {
                this.mostrarFeedback('¡Nivel completado!', 'success');
                this.modales.pregunta.hide();
                this.mostrarLoader('Cargando siguiente nivel...');
                setTimeout(() => this.iniciarJuego(), 3000);
            } else if (data.estado === 'completado') {
                this.mostrarFinJuego(data.ganador);
            } else {
                this.mostrarFeedback('Respuesta incorrecta', 'danger');
                if (respuestaInput) respuestaInput.classList.add('is-invalid');
                const feedback = document.getElementById('feedback-respuesta');
                if (feedback) feedback.textContent = 'La respuesta no es correcta';
            }
        } catch (error) {
            this.mostrarFeedback('Error al enviar respuesta', 'danger');
        } finally {
            if (spinner) spinner.classList.add('d-none');
            if (btnEnviar) btnEnviar.disabled = false;
        }
    }

    mostrarEstadoEsperando(data) {
        this.contenedorEstado.innerHTML = `
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0"><i class="fas fa-users me-2"></i>Esperando jugadores</h4>
                        </div>
                        <div class="card-body">
                            <p class="lead">Esperando a que se unan todos los jugadores...</p>
                            <div class="progress mb-4" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: ${this.calcularProgreso(data.grupos)}%">
                                </div>
                            </div>
                            <div class="row" id="contenedor-grupos">
                                ${data.grupos.map(grupo => this.crearTarjetaGrupo(grupo)).join('')}
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 mb-0">Actualizando en 5 segundos...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    calcularProgreso(grupos) {
        const totalJugadores = grupos.reduce((sum, grupo) => sum + grupo.max_jugadores, 0);
        const jugadoresConectados = grupos.reduce((sum, grupo) => sum + grupo.jugadores, 0);
        return Math.round((jugadoresConectados / totalJugadores) * 100);
    }

    crearTarjetaGrupo(grupo) {
        return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 ${grupo.es_mi_grupo ? 'border-primary' : ''}">
                    <div class="card-header ${grupo.es_mi_grupo ? 'bg-primary text-white' : ''}">
                        <h5 class="mb-0">${grupo.nombre}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Jugadores:</span>
                            <span class="badge ${grupo.jugadores >= grupo.max_jugadores ? 'bg-success' : 'bg-warning'}">
                                ${grupo.jugadores}/${grupo.max_jugadores}
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar ${grupo.jugadores >= grupo.max_jugadores ? 'bg-success' : 'bg-warning'}" 
                                 role="progressbar" style="width: ${(grupo.jugadores / grupo.max_jugadores) * 100}%">
                            </div>
                        </div>
                    </div>
                    ${grupo.es_mi_grupo ? 
                        '<div class="card-footer bg-light text-center"><i class="fas fa-user-check text-primary me-2"></i>Tu grupo</div>' : 
                        ''}
                </div>
            </div>
        `;
    }

    mostrarFinJuego(ganador) {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
        }
        
        if (ganador) {
            this.contenedorEstado.innerHTML = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="card text-center border-success">
                            <div class="card-header bg-success text-white">
                                <h3 class="mb-0"><i class="fas fa-trophy me-2"></i>¡Felicidades!</h3>
                            </div>
                            <div class="card-body py-5">
                                <div class="display-4 text-success mb-4">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <h4 class="card-title">¡Tu grupo ha ganado la gincana!</h4>
                                <p class="card-text">Has completado todos los niveles correctamente.</p>
                                <a href="/gincana" class="btn btn-success btn-lg mt-3">
                                    <i class="fas fa-home me-2"></i>Volver al menú
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                ¡Gracias por jugar!
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            this.contenedorEstado.innerHTML = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="card text-center border-danger">
                            <div class="card-header bg-danger text-white">
                                <h3 class="mb-0"><i class="fas fa-flag me-2"></i>Juego terminado</h3>
                            </div>
                            <div class="card-body py-5">
                                <div class="display-4 text-danger mb-4">
                                    <i class="fas fa-hourglass-end"></i>
                                </div>
                                <h4 class="card-title">Otro grupo ha completado la gincana primero</h4>
                                <p class="card-text">Sigue intentándolo para la próxima.</p>
                                <a href="/gincana" class="btn btn-danger btn-lg mt-3">
                                    <i class="fas fa-home me-2"></i>Volver al menú
                                </a>
                            </div>
                            <div class="card-footer text-muted">
                                ¡Mejor suerte la próxima vez!
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    mapaFallback(objetivo) {
        const coords = objetivo || { latitud: 40.4168, longitud: -3.7038 };
        
        const mapaContainer = document.getElementById('mapa-container');
        if (!mapaContainer) {
            console.error('Contenedor del mapa no encontrado en modo fallback');
            return;
        }

        this.mapa = L.map(mapaContainer, {
            zoomControl: false
        }).setView([coords.latitud, coords.longitud], 16);
        
        L.control.zoom({
            position: 'topright'
        }).addTo(this.mapa);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.mapa);

        L.popup()
            .setLatLng([coords.latitud, coords.longitud])
            .setContent(`
                <div class="text-center">
                    <h6>Modo sin GPS</h6>
                    <p>No se pudo obtener tu ubicación. Usando ubicación por defecto.</p>
                    <small class="text-muted">Algunas funciones pueden no estar disponibles</small>
                </div>
            `)
            .openOn(this.mapa);

        this.marcadores.radio = L.circle([coords.latitud, coords.longitud], {
            radius: 50,
            color: '#ffc107',
            fillColor: '#ffc107',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(this.mapa);

        this.marcadores.objetivo = L.marker([coords.latitud, coords.longitud], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(this.mapa)
        .bindPopup(`
            <div class="text-center">
                <h6>Ubicación del objetivo</h6>
                <button class="btn btn-sm btn-success mt-2" onclick="window.juegoGincana.mostrarPregunta()">
                    <i class="fas fa-question-circle me-1"></i>Responder pregunta
                </button>
            </div>
        `);
    }

    manejarErrorGPS(error, objetivo) {
        console.error('Error obteniendo ubicación:', error);
        this.gpsIntentos++;
        
        if (this.gpsIntentos < 2) {
            setTimeout(() => {
                navigator.geolocation.getCurrentPosition(
                    (position) => this.configurarMapa(position, objetivo),
                    (err) => this.manejarErrorGPS(err, objetivo),
                    { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 }
                );
            }, 1000);
        } else {
            this.mapaFallback(objetivo);
            this.modales.pista.show();
            this.mostrarErrorGPS(error);
        }
    }

    mostrarErrorGPS(error) {
        let mensajeError = '';
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                mensajeError = 'Permiso de ubicación denegado. Por favor, habilita los permisos de ubicación en la configuración de tu navegador.';
                break;
            case error.POSITION_UNAVAILABLE:
                mensajeError = 'La información de ubicación no está disponible. Verifica tu conexión a internet o señal GPS.';
                break;
            case error.TIMEOUT:
                mensajeError = 'La solicitud de ubicación ha caducado. Inténtalo de nuevo en un área con mejor recepción.';
                break;
            default:
                mensajeError = 'Error desconocido al obtener la ubicación.';
        }
        
        const errorMsg = `
            <div class="alert alert-warning alert-dismissible fade show">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Error de GPS</h5>
                <p>${mensajeError}</p>
                <ul>
                    <li>Asegúrate de tener el GPS activado</li>
                    <li>Verifica los permisos de ubicación para este sitio</li>
                    <li>Intenta en un área con mejor recepción</li>
                </ul>
                <div class="d-flex justify-content-between align-items-center">
                    <button onclick="window.juegoGincana.reintentarGPS()" class="btn btn-sm btn-warning">
                        <i class="fas fa-sync-alt me-1"></i>Reintentar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        this.contenedorEstado.insertAdjacentHTML('afterbegin', errorMsg);
    }

    reintentarGPS() {
        this.gpsIntentos = 0;
        this.mostrarLoader('Reintentando obtener ubicación...');
        setTimeout(() => this.cargarEstadoJuego(), 1000);
    }

    mostrarLoader(mensaje) {
        if (this.contenedorEstado) {
            this.contenedorEstado.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 lead">${mensaje}</p>
                </div>
            `;
        }
    }

    mostrarError(mensaje) {
        if (this.contenedorEstado) {
            this.contenedorEstado.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-circle me-2"></i>Error</h5>
                    <p>${mensaje}</p>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-danger">
                        <i class="fas fa-sync-alt me-1"></i>Recargar
                    </button>
                </div>
            `;
        }
    }

    mostrarFeedback(mensaje, tipo) {
        const feedback = document.createElement('div');
        feedback.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        feedback.style.top = '20px';
        feedback.style.right = '20px';
        feedback.style.zIndex = '2000';
        feedback.style.maxWidth = '300px';
        feedback.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(feedback);
        
        setTimeout(() => {
            feedback.classList.remove('show');
            setTimeout(() => feedback.remove(), 150);
        }, 5000);
    }

    calcularDistancia(lat1, lon1, lat2, lon2) {
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
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado');
        const estadoJuego = document.getElementById('estado-juego');
        if (estadoJuego) {
            estadoJuego.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Error crítico</h5>
                    <p>Bootstrap no está cargado correctamente</p>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-danger">
                        <i class="fas fa-sync-alt me-1"></i>Recargar página
                    </button>
                </div>
            `;
        }
        return;
    }

    try {
        const scriptElement = document.querySelector('script[data-gincana-id]');
        if (!scriptElement) throw new Error('Elemento script no encontrado');
        
        const gincanaId = scriptElement.getAttribute('data-gincana-id');
        if (!gincanaId) throw new Error('ID de gincana no especificado');
        
        const loadDependencies = () => {
            if (typeof L === 'undefined') {
                const leafletCSS = document.createElement('link');
                leafletCSS.rel = 'stylesheet';
                leafletCSS.href = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css';
                
                const leafletJS = document.createElement('script');
                leafletJS.src = 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js';
                leafletJS.onload = () => window.juegoGincana = new JuegoGincana(gincanaId);
                
                document.head.appendChild(leafletCSS);
                document.body.appendChild(leafletJS);
            } else {
                window.juegoGincana = new JuegoGincana(gincanaId);
            }
        };

        if (!document.querySelector('link[href*="font-awesome"]')) {
            const fontAwesomeCSS = document.createElement('link');
            fontAwesomeCSS.rel = 'stylesheet';
            fontAwesomeCSS.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
            fontAwesomeCSS.onload = loadDependencies;
            document.head.appendChild(fontAwesomeCSS);
        } else {
            loadDependencies();
        }
        
    } catch (error) {
        console.error('Error al inicializar el juego:', error);
        const estadoJuego = document.getElementById('estado-juego');
        if (estadoJuego) {
            estadoJuego.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Error crítico</h5>
                    <p>${error.message}</p>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-danger">
                        <i class="fas fa-sync-alt me-1"></i>Recargar página
                    </button>
                </div>
            `;
        }
    }
});