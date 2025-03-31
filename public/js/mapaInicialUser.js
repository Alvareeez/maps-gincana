// Variables globales para almacenar la ubicación del usuario
var userLat, userLng;
// Variable global para almacenar el control de la ruta
var routingControl;
// Inicializar el mapa y establecer la vista a una ubicación y zoom específicos
var map = L.map('map', {
    zoomControl: false // Deshabilitar los controles de zoom predeterminados
});
// Circulo radio
var userLocationCircle;
var userRadius = 1000; // Radio predeterminado en metros
var radiusControl;
// Evitar que el clic en el input del buscador active el evento del mapa
document.getElementById('buscador').addEventListener('click', function (e) {
    e.stopPropagation();
});
// Añadir los controles de zoom en una nueva posición
L.control.zoom({
    position: 'bottomright'
}).addTo(map);
// Crear un control personalizado para el botón de home
var homeControl = L.Control.extend({
    options: {
        position: 'bottomright'
    },
    onAdd: function (map) {
        var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        container.style.marginBottom = '10px';
        container.style.backgroundColor = 'white';
        container.style.borderRadius = '4px';

        var button = L.DomUtil.create('a', 'leaflet-control-zoom-in', container);
        button.href = '/home';
        button.innerHTML = '🏠';
        button.style.fontSize = '20px';
        button.style.lineHeight = '26px';
        button.style.width = '30px';
        button.style.height = '30px';
        button.style.display = 'block';
        button.style.textAlign = 'center';
        button.style.textDecoration = 'none';

        // Prevenir que el clic en el botón afecte al mapa
        L.DomEvent.disableClickPropagation(button);

        return container;
    }
});

// Añadir el control personalizado al mapa
new homeControl().addTo(map);
// Añadir una capa de tiles (baldosas) de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
// Definir un icono personalizado para la ubicación del usuario
var userLocationIcon = L.icon({
    iconUrl: '/img/street-view-solid.png',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});

// Intentar localizar al usuario
map.locate({
    setView: true,
    maxZoom: 15,
    watch: true,          // Activa el modo de seguimiento
    enableHighAccuracy: true // Mayor precisión (opcional)
});

// Evento que se activa cuando se encuentra la ubicación del usuario
map.on('locationfound', function (e) {
    userLat = e.latitude;
    userLng = e.longitude;

    // Eliminar marcador anterior si existe
    if (window.userMarker) {
        map.removeLayer(window.userMarker);
    }

    // Crear nuevo marcador
    window.userMarker = L.marker([userLat, userLng], { icon: userLocationIcon })
        .bindPopup('Estás aquí.')
        .addTo(map);

    // Actualizar círculo de radio
    if (userLocationCircle) {
        map.removeLayer(userLocationCircle);
    }

    // Crear y añadir el círculo con el radio actual
    userLocationCircle = L.circle([userLat, userLng], {
        radius: userRadius,
        color: '#3388ff',
        fillColor: '#3388ff',
        fillOpacity: 0.2
    }).addTo(map);

    // Crear control para ajustar el radio si no existe
    if (!radiusControl) {
        radiusControl = L.control({ position: 'bottomright' });

        // Modifica la función radiusControl.onAdd para incluir el botón "Mostrar todo"
        radiusControl.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'radius-control');
            div.innerHTML = `
        <div class="input-group input-group-sm mb-1">
            <span class="input-group-text">Radio (m)</span>
            <input type="number" id="radiusInput" class="form-control" value="${userRadius}" min="100" max="10000" step="100">
            <button class="btn btn-primary" id="updateRadius">Aplicar</button>
        </div>
        <button class="btn btn-secondary w-100 btn-sm" id="showAll">Mostrar todo</button>
    `;
            return div;
        };

        radiusControl.addTo(map);

        // Evento para actualizar el radio
        document.getElementById('updateRadius').addEventListener('click', function () {
            var newRadius = parseInt(document.getElementById('radiusInput').value);
            if (!isNaN(newRadius) && newRadius >= 100 && newRadius <= 10000) {
                userRadius = newRadius;
                updateUserRadiusCircle();
                filtrarPorRadio(); // Nueva función que filtra por radio
            } else {
                alert('Por favor ingrese un valor entre 100 y 10000 metros');
            }
        });

        // Evento para mostrar todos los lugares
        document.getElementById('showAll').addEventListener('click', function () {
            // Eliminar el círculo de radio temporalmente
            if (userLocationCircle) {
                map.removeLayer(userLocationCircle);
            }

            // Cargar todos los lugares sin filtrar por radio
            cargarTodosLosLugares();

            // Después de un breve retraso, volver a mostrar el círculo
            setTimeout(() => {
                if (userLat && userLng) {
                    userLocationCircle = L.circle([userLat, userLng], {
                        radius: userRadius,
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2
                    }).addTo(map);
                }
            }, 100);
        });
    }
    filtrarPorRadio();
});

// Función para cargar todos los lugares sin filtrar por radio
function cargarTodosLosLugares() {
    // Limpiar marcadores existentes
    lugaresDestacados.forEach(lugar => {
        if (lugar.marker) {
            map.removeLayer(lugar.marker);
        }
    });
    lugaresDestacados = [];

    // Mostrar carga mientras se obtienen los datos
    Swal.fire({
        title: 'Cargando todos los lugares...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/lugares-destacados', {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(lugares => {
            Swal.close();

            lugares.forEach(lugar => {
                // Determinar el tipo de marcador correctamente
                const tipoMarcadorId = lugar.tipo_marcador?.id || lugar.tipoMarcador?.id || 1;
                const icono = crearIconoMarcador(tipoMarcadorId);

                const marker = L.marker([lugar.latitud, lugar.longitud], { icon: icono }).addTo(map);

                // Obtener nombres de etiquetas si existen
                const etiquetasNames = lugar.etiquetas ? lugar.etiquetas.map(e => e.nombre).join(', ') : 'Sin etiquetas';

                // Calcular distancia si tenemos ubicación del usuario
                let distanciaInfo = '';
                if (userLat && userLng) {
                    const userLocation = L.latLng(userLat, userLng);
                    const lugarLocation = L.latLng(lugar.latitud, lugar.longitud);
                    const distancia = userLocation.distanceTo(lugarLocation);
                    distanciaInfo = `<br><small>Distancia: ${Math.round(distancia)} metros</small>`;
                }

                // Crear contenido del popup
                const popupContent = `
                <b>${lugar.nombre}</b><br>
                ${lugar.descripcion}<br>
                <small>Etiquetas: ${etiquetasNames}</small><br>
                Dirección: ${lugar.direccion}${distanciaInfo}<br>
                <button class="btn btn-danger btn-sm mt-2" onclick="eliminarLugar(${lugar.id})">Eliminar</button>
                <button class="btn btn-secondary btn-sm mt-2" onclick="modificarLugar(${lugar.id})">Modificar</button>
                <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                ${lugar.esFavorito
                        ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                        : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${tipoMarcadorId})">Añadir a favoritos</button>`
                    }
            `;

                marker.bindPopup(popupContent);

                lugaresDestacados.push({
                    id: lugar.id,
                    nombre: lugar.nombre,
                    direccion: lugar.direccion,
                    latitud: lugar.latitud,
                    longitud: lugar.longitud,
                    marker: marker,
                    esFavorito: lugar.esFavorito || false,
                    tipoMarcador: {
                        id: tipoMarcadorId,
                        nombre: lugar.tipo_marcador?.nombre || lugar.tipoMarcador?.nombre || 'General'
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error al cargar todos los lugares:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los lugares. Verifica la consola para más detalles.'
            });
        });
}
// Función para filtrar lugares por radio
// Función para filtrar lugares por radio
function filtrarPorRadio() {
    if (!userLat || !userLng) {
        alert('No se pudo obtener tu ubicación. Asegúrate de permitir el acceso a la ubicación.');
        return;
    }

    // Crear un punto LatLng para la ubicación del usuario
    const userLocation = L.latLng(userLat, userLng);

    // Limpiar marcadores existentes
    lugaresDestacados.forEach(lugar => {
        if (lugar.marker) {
            map.removeLayer(lugar.marker);
        }
    });
    lugaresDestacados = [];

    // Mostrar carga mientras se obtienen los datos
    Swal.fire({
        title: 'Cargando lugares...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/lugares-destacados', {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(lugares => {
            Swal.close();

            lugares.forEach(lugar => {
                const lugarLocation = L.latLng(lugar.latitud, lugar.longitud);
                const distancia = userLocation.distanceTo(lugarLocation); // Distancia en metros

                // Solo mostrar si está dentro del radio
                if (distancia <= userRadius) {
                    // Determinar el tipo de marcador correctamente
                    const tipoMarcadorId = lugar.tipo_marcador?.id || lugar.tipoMarcador?.id || 1;
                    const icono = crearIconoMarcador(tipoMarcadorId);
                    const marker = L.marker([lugar.latitud, lugar.longitud], { icon: icono }).addTo(map);

                    // Obtener nombres de etiquetas si existen
                    const etiquetasNames = lugar.etiquetas ? lugar.etiquetas.map(e => e.nombre).join(', ') : 'Sin etiquetas';

                    // Crear contenido del popup
                    const popupContent = `
                    <b>${lugar.nombre}</b><br>
                    ${lugar.descripcion}<br>
                    <small>Etiquetas: ${etiquetasNames}</small><br>
                    Dirección: ${lugar.direccion}<br>
                    <small>Distancia: ${Math.round(distancia)} metros</small><br>
                    <button class="btn btn-danger btn-sm mt-2" onclick="eliminarLugar(${lugar.id})">Eliminar</button>
                    <button class="btn btn-secondary btn-sm mt-2" onclick="modificarLugar(${lugar.id})">Modificar</button>
                    <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                    ${lugar.esFavorito
                            ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                            : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${tipoMarcadorId})">Añadir a favoritos</button>`
                        }
                `;

                    marker.bindPopup(popupContent);

                    lugaresDestacados.push({
                        id: lugar.id,
                        nombre: lugar.nombre,
                        direccion: lugar.direccion,
                        latitud: lugar.latitud,
                        longitud: lugar.longitud,
                        marker: marker,
                        esFavorito: lugar.esFavorito || false,
                        tipoMarcador: {
                            id: tipoMarcadorId,
                            nombre: lugar.tipo_marcador?.nombre || lugar.tipoMarcador?.nombre || 'General'
                        }
                    });
                }
            });
        })
        .catch(error => {
            console.error('Error al filtrar lugares por radio:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los lugares. Verifica la consola para más detalles.'
            });
        });
}
// Función para actualizar el círculo de radio
function updateUserRadiusCircle() {
    if (userLocationCircle && userLat && userLng) {
        map.removeLayer(userLocationCircle);
        userLocationCircle = L.circle([userLat, userLng], {
            radius: userRadius,
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.2
        }).addTo(map);

        // Filtrar los lugares por el nuevo radio
        filtrarPorRadio();
    }
}

// Función para cargar los lugares destacados desde la base de datos
function cargarLugaresDestacados() {
    if (filtrarPorRadio && userLat && userLng) {
        filtrarPorRadio();
        return;
    }
    fetch('/lugares-destacados')
        .then(response => response.json())
        .then(lugares => {
            lugares.forEach(lugar => {
                // Verificar si el lugar está en favoritos
                const esFavorito = lugar.esFavorito || false;

                // Ejemplo de cómo usarla en cargarLugaresDestacados()
                const icono = crearIconoMarcador(lugar.tipoMarcador);
                var marker = L.marker([lugar.latitud, lugar.longitud], { icon: icono }).addTo(map);

                // Crear el contenido del popup con los botones correspondientes
                var popupContent = `
                <b>${lugar.nombre}</b><br>
                ${lugar.descripcion}<br>
                Dirección: ${lugar.direccion}<br>
                <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                ${esFavorito
                        ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                        : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${lugar.tipoMarcador ? lugar.tipoMarcador.id : 1})">Añadir a favoritos</button>`
                    }
                `;

                // Asignar el popup al marcador
                marker.bindPopup(popupContent);

                // Guardar el marcador en la lista de lugares destacados
                lugaresDestacados.push({
                    id: lugar.id,
                    nombre: lugar.nombre,
                    direccion: lugar.direccion,
                    latitud: lugar.latitud,
                    longitud: lugar.longitud,
                    marker: marker,
                    esFavorito: esFavorito,
                    tipoMarcador: lugar.tipoMarcador || null
                });
            });
        })
        .catch(error => console.error('Error al cargar los lugares destacados:', error));
}
// Función para crear una ruta hacia un marcador seleccionado
function crearRuta(destLat, destLng) {
    // Verificar si la ubicación del usuario está disponible

    if (!userLat || !userLng) {
        alert('No se pudo obtener tu ubicación. Asegúrate de permitir el acceso a la ubicación.');
        return;
    }

    // Eliminar la ruta anterior si existe
    if (routingControl) {
        map.removeControl(routingControl);
    }

    // Crear una nueva ruta desde la ubicación del usuario hasta el marcador seleccionado
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(userLat, userLng),
            L.latLng(destLat, destLng)
        ],
        iconUrl: false,
        routeWhileDragging: true,
        show: true,
        language: 'es'
    }).addTo(map);
}

// Función para cargar los tipos de marcadores desde la base de datos
function cargarTiposMarcadores() {
    return fetch('/tipo-marcadores')
        .then(response => response.json())
        .then(tipos => {
            // Generar las opciones del select
            return tipos.map(tipo => `<option value="${tipo.id}">${tipo.nombre}</option>`).join('');
        })
        .catch(error => {
            console.error('Error al cargar los tipos de marcadores:', error);
            return '<option value="">Error al cargar</option>'; // Mostrar un mensaje de error en el select
        });
}
// Lista para almacenar los lugares destacados
var lugaresDestacados = [];

// Función para buscar lugares destacados
// Función para buscar lugares destacados
function buscarLugar(query) {
    fetch(`/buscar-lugares?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(lugares => {
            if (lugares.length > 0) {
                // Limpiar los marcadores existentes
                lugaresDestacados.forEach(lugar => {
                    if (lugar.marker) {
                        map.removeLayer(lugar.marker);
                    }
                });
                lugaresDestacados = [];

                // Añadir los resultados al mapa
                lugares.forEach((lugar, index) => {
                    // Determinar el tipo de marcador correctamente
                    const tipoMarcadorId = lugar.tipo_marcador?.id || lugar.tipoMarcador?.id || 1;
                    const icono = crearIconoMarcador(tipoMarcadorId);
                    const marker = L.marker([lugar.latitud, lugar.longitud], { icon: icono }).addTo(map);

                    // Obtener nombres de etiquetas si existen
                    const etiquetasNames = lugar.etiquetas
                        ? lugar.etiquetas.map(e => e.nombre).join(', ')
                        : 'Sin etiquetas';

                    // Calcular distancia si tenemos ubicación del usuario
                    let distanciaInfo = '';
                    if (userLat && userLng) {
                        const userLocation = L.latLng(userLat, userLng);
                        const lugarLocation = L.latLng(lugar.latitud, lugar.longitud);
                        const distancia = userLocation.distanceTo(lugarLocation);
                        distanciaInfo = `<br><small>Distancia: ${Math.round(distancia)} metros</small>`;
                    }

                    // Crear contenido del popup
                    const popupContent = `
                        <b>${lugar.nombre}</b><br>
                        ${lugar.descripcion}<br>
                        <small>Etiquetas: ${etiquetasNames}</small><br>
                        Dirección: ${lugar.direccion}${distanciaInfo}<br>
                        <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                        ${lugar.esFavorito
                            ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                            : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${tipoMarcadorId})">Añadir a favoritos</button>`
                        }
                    `;

                    // Asignar el popup al marcador
                    marker.bindPopup(popupContent);

                    // Centrar el mapa en el primer resultado y abrir su popup
                    if (index === 0) {
                        map.setView([lugar.latitud, lugar.longitud], 15);
                        marker.openPopup();
                    }

                    // Guardar el marcador en la lista
                    lugaresDestacados.push({
                        id: lugar.id,
                        nombre: lugar.nombre,
                        direccion: lugar.direccion,
                        latitud: lugar.latitud,
                        longitud: lugar.longitud,
                        marker: marker,
                        esFavorito: lugar.esFavorito || false,
                        tipoMarcador: {
                            id: tipoMarcadorId,
                            nombre: lugar.tipo_marcador?.nombre || lugar.tipoMarcador?.nombre || 'General'
                        }
                    });
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin resultados',
                    text: 'No se encontraron lugares con esa búsqueda.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            console.error('Error al buscar lugares:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al realizar la búsqueda.'
            });
        });
}
// Evento para manejar el buscador
document.getElementById('buscador').addEventListener('input', function (e) {
    const query = e.target.value;
    if (query.length > 2) { // Buscar solo si hay más de 2 caracteres
        buscarLugar(query);
    }
});
// Función para cargar las etiquetas desde la base de datos
async function cargarEtiquetas() {
    try {
        const response = await fetch('/etiquetas');
        const etiquetas = await response.json();

        // Generar opciones para el select de etiquetas
        return etiquetas.map(etiqueta =>
            `<option value="${etiqueta.id}">${etiqueta.nombre}</option>`
        ).join('');
    } catch (error) {
        console.error('Error al cargar etiquetas:', error);
        return '';
    }
}

// Función para crear el filtro de etiquetas en la interfaz
async function crearFiltroEtiquetas() {
    const opcionesEtiquetas = await cargarEtiquetas();

    const filtroContainer = document.createElement('div');
    filtroContainer.className = 'filtro-etiquetas';
    filtroContainer.style.position = 'absolute';
    filtroContainer.style.top = '65px';
    filtroContainer.style.right = '170px';
    filtroContainer.style.zIndex = '1000';
    filtroContainer.style.backgroundColor = 'white';
    filtroContainer.style.padding = '5px';
    filtroContainer.style.borderRadius = '5px';
    filtroContainer.style.boxShadow = '0 0 10px rgba(0,0,0,0.2)';
    filtroContainer.style.display = 'flex';
    filtroContainer.style.gap = '0px';

    filtroContainer.innerHTML = `
        <select id="filtro-etiqueta" class="form-select form-select-sm">
            <option value="">Etiquetas</option>
            ${opcionesEtiquetas}
        </select>
        <select id="filtro-favoritos" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="true">Solo favoritos</option>
            <option value="false">No favoritos</option>
        </select>
    `;

    document.getElementById('map').appendChild(filtroContainer);

    // Prevenir la propagación del evento clic
    filtroContainer.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Eventos para filtrar cuando cambia la selección
    document.getElementById('filtro-etiqueta').addEventListener('change', function () {
        aplicarFiltros();
    });

    document.getElementById('filtro-favoritos').addEventListener('change', function () {
        aplicarFiltros();
    });
}

function aplicarFiltros() {
    const etiquetaId = document.getElementById('filtro-etiqueta').value;
    const favoritos = document.getElementById('filtro-favoritos').value;

    // Limpiar marcadores existentes
    lugaresDestacados.forEach(lugar => {
        if (lugar.marker) {
            map.removeLayer(lugar.marker);
        }
    });
    lugaresDestacados = [];

    // Mostrar carga mientras se obtienen los datos
    Swal.fire({
        title: 'Cargando lugares...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Construir URL de consulta
    let url = '/lugares-destacados?';
    if (etiquetaId) {
        url += `etiqueta=${etiquetaId}&`;
    }
    if (favoritos) {
        url += `favoritos=${favoritos}`;
    }

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || 'Error en la respuesta del servidor');
                });
            }
            return response.json();
        })
        .then(lugares => {
            Swal.close();

            if (lugares.error) {
                throw new Error(lugares.message);
            }

            lugares.forEach(lugar => {
                // Determinar el tipo de marcador correctamente
                const tipoMarcadorId = lugar.tipo_marcador?.id || lugar.tipoMarcador?.id || 1;
                const icono = crearIconoMarcador(tipoMarcadorId);
                const marker = L.marker([lugar.latitud, lugar.longitud], { icon: icono }).addTo(map);

                // Obtener nombres de etiquetas si existen
                const etiquetasNames = lugar.etiquetas
                    ? lugar.etiquetas.map(e => e.nombre).join(', ')
                    : 'Sin etiquetas';

                // Calcular distancia si tenemos ubicación del usuario
                let distanciaInfo = '';
                if (userLat && userLng) {
                    const userLocation = L.latLng(userLat, userLng);
                    const lugarLocation = L.latLng(lugar.latitud, lugar.longitud);
                    const distancia = userLocation.distanceTo(lugarLocation);
                    distanciaInfo = `<br><small>Distancia: ${Math.round(distancia)} metros</small>`;
                }

                // Crear contenido del popup
                const popupContent = `
                <b>${lugar.nombre}</b><br>
                ${lugar.descripcion}<br>
                <small>Etiquetas: ${etiquetasNames}</small><br>
                Dirección: ${lugar.direccion}${distanciaInfo}<br>
                <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                ${lugar.esFavorito
                        ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                        : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${tipoMarcadorId})">Añadir a favoritos</button>`
                    }
            `;

                marker.bindPopup(popupContent);

                lugaresDestacados.push({
                    id: lugar.id,
                    nombre: lugar.nombre,
                    direccion: lugar.direccion,
                    latitud: lugar.latitud,
                    longitud: lugar.longitud,
                    marker: marker,
                    esFavorito: lugar.esFavorito || false,
                    tipoMarcador: {
                        id: tipoMarcadorId,
                        nombre: lugar.tipo_marcador?.nombre || lugar.tipoMarcador?.nombre || 'General'
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error al filtrar lugares:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los lugares. Verifica la consola para más detalles.'
            });
        });
}
// Función para filtrar lugares por etiqueta
function filtrarPorEtiqueta(etiquetaId) {
    // Limpiar marcadores existentes
    lugaresDestacados.forEach(lugar => {
        if (lugar.marker) {
            map.removeLayer(lugar.marker);
        }
    });
    lugaresDestacados = [];

    // Mostrar carga mientras se obtienen los datos
    Swal.fire({
        title: 'Cargando lugares...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Construir URL de consulta
    let url = '/lugares-destacados';
    if (etiquetaId) {
        url += `?etiqueta=${etiquetaId}`;
    }

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || 'Error en la respuesta del servidor');
                });
            }
            return response.json();
        })
        .then(lugares => {
            Swal.close();

            if (lugares.error) {
                throw new Error(lugares.message);
            }

            lugares.forEach(lugar => {
                // Crear marcador
                const marker = L.marker([lugar.latitud, lugar.longitud]).addTo(map);

                // Obtener nombres de etiquetas si existen
                const etiquetasNames = lugar.etiquetas
                    ? lugar.etiquetas.map(e => e.nombre).join(', ')
                    : 'Sin etiquetas';

                // Crear contenido del popup
                // Dentro de la función filtrarPorEtiqueta(), modifica la parte donde creas el popupContent:
                const popupContent = `
                        <b>${lugar.nombre}</b><br>
                        ${lugar.descripcion}<br>
                        <small>Etiquetas: ${etiquetasNames}</small><br>
                        Dirección: ${lugar.direccion}<br>
                        <button class="btn btn-primary btn-sm mt-2" onclick="crearRuta(${lugar.latitud}, ${lugar.longitud})">Ir aquí</button>
                        ${lugar.esFavorito
                        ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugar.id})">Quitar de favoritos</button>`
                        : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugar.id}, ${lugar.tipoMarcador ? lugar.tipoMarcador.id : 1})">Añadir a favoritos</button>`
                    }
`;

                marker.bindPopup(popupContent);

                lugaresDestacados.push({
                    id: lugar.id,
                    nombre: lugar.nombre,
                    direccion: lugar.direccion,
                    latitud: lugar.latitud,
                    longitud: lugar.longitud,
                    marker: marker,
                    esFavorito: lugar.esFavorito || false,  // ✨ Añadir esta línea
                    tipoMarcador: lugar.tipoMarcador || null  // ✨ Añadir esta línea
                });
            });
        })
        .catch(error => {
            console.error('Error al filtrar lugares:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los lugares. Verifica la consola para más detalles.'
            });
        });
}
// Función para crear iconos basados en el tipo de marcador
function crearIconoMarcador(tipoMarcadorId) {
    const basePath = window.location.origin;
    const iconosPorTipo = {
        1: '/img/edificio.jpg',  // Ruta para tipo 1
        2: '/img/ocio.png',  // Ruta para tipo 2
        3: '/img/resposteria.png',  // Ruta para tipo 2
        4: '/img/deportes.png',   // Ruta para tipo 4
        5: '/img/interes.png'   // Ruta para tipo 5
    };

    const rutaIcono = iconosPorTipo[tipoMarcadorId] || '/img/default.png';

    return L.icon({
        iconUrl: `${basePath}${rutaIcono}`,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
}
function agregarAFavoritos(lugarId, tipoMarcadorId) {
    // const idListaPredeterminada = 1;

    fetch('/lugares-destacados/favoritos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            lugar_destacado_id: lugarId,
            tipoMarcador: tipoMarcadorId || 1,
            // id_lista: idListaPredeterminada
        })
    })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const errorText = await response.text();
                throw new Error(`El servidor respondió con: ${errorText.substring(0, 100)}...`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Añadido!',
                    text: 'El lugar se ha añadido a tus favoritos',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Actualizar el estado del lugar en la lista
                const lugar = lugaresDestacados.find(l => l.id === lugarId);
                if (lugar) {
                    lugar.esFavorito = true;
                    // Actualizar el popup inmediatamente
                    const popup = lugar.marker.getPopup();
                    let content = popup.getContent();
                    // Reemplazar el botón de añadir por el de quitar
                    content = content.replace(
                        /<button class="btn btn-success[^>]*>Añadir a favoritos<\/button>/,
                        `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugarId})">Quitar de favoritos</button>`
                    );
                    popup.setContent(content);
                }
            } else {
                throw new Error(data.message || 'Error al añadir a favoritos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo añadir a favoritos'
            });
        });
}
// Función para quitar un lugar de favoritos
function quitarDeFavoritos(lugarId) {
    fetch(`/lugares-destacados/favoritos/${lugarId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const errorText = await response.text();
                throw new Error(`El servidor respondió con: ${errorText.substring(0, 100)}...`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'El lugar se ha quitado de tus favoritos',
                    timer: 2000,
                    showConfirmButton: false
                });
                // Actualizar el estado del lugar en la lista
                const lugar = lugaresDestacados.find(l => l.id === lugarId);
                if (lugar) {
                    lugar.esFavorito = false;
                    // Actualizar el popup
                    const popup = lugar.marker.getPopup();
                    let content = popup.getContent();
                    // Reemplazar el botón de quitar por el de añadir
                    content = content.replace(
                        /<button class="btn btn-warning[^>]*>Quitar de favoritos<\/button>/,
                        `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugarId}, ${lugar.tipoMarcador ? lugar.tipoMarcador.id : 1})">Añadir a favoritos</button>`
                    );
                    popup.setContent(content);
                }
            } else {
                throw new Error(data.message || 'Error al quitar de favoritos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo quitar de favoritos'
            });
        });
}

// Función para actualizar el botón de favoritos en el popup
function actualizarBotonFavoritos(lugarId, esFavorito) {
    const lugar = lugaresDestacados.find(l => l.id === lugarId);
    if (lugar && lugar.marker) {
        const popup = lugar.marker.getPopup();
        let content = popup.getContent();

        // Primero elimina cualquier botón de favoritos existente
        content = content.replace(
            /<button class="btn btn-(warning|success)[^>]*>(Quitar de|Añadir a) favoritos<\/button>/,
            ''
        );

        // Luego añade el botón correspondiente
        const botonFavoritos = esFavorito
            ? `<button class="btn btn-warning btn-sm mt-2" onclick="quitarDeFavoritos(${lugarId})">Quitar de favoritos</button>`
            : `<button class="btn btn-success btn-sm mt-2" onclick="agregarAFavoritos(${lugarId}, ${lugar.tipoMarcador ? lugar.tipoMarcador.id : 1})">Añadir a favoritos</button>`;

        // Inserta el botón antes del primer </div> de cierre
        content = content.replace('</div>', `${botonFavoritos}</div>`);

        popup.setContent(content);
        lugar.esFavorito = esFavorito;
    }
}
// Cargar los lugares destacados al iniciar el mapa
cargarLugaresDestacados();
// Inicializar el filtro cuando se carga el mapa
document.addEventListener('DOMContentLoaded', function () {
    crearFiltroEtiquetas();
}); 