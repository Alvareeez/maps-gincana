// Inicializar el mapa y establecer la vista a una ubicación y zoom específicos
var map = L.map('map').setView([40.416775, -3.703790], 6); // Coordenadas de Madrid, España

// Añadir una capa de tiles (baldosas) de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Función para cargar los lugares destacados desde la base de datos
function cargarLugaresDestacados() {
    fetch('/lugares-destacados')
        .then(response => response.json())
        .then(lugares => {
            lugares.forEach(lugar => {
                // Añadir un marcador para cada lugar destacado
                var marker = L.marker([lugar.latitud, lugar.longitud]).addTo(map)
                    .bindPopup(`<b>${lugar.nombre}</b><br>${lugar.descripcion}<br>Dirección: ${lugar.direccion}`)
                    .openPopup();

                // Habilitar la eliminación del marcador con clic derecho
                marker.on('contextmenu', function () {
                    if (confirm('¿Deseas eliminar este lugar?')) {
                        map.removeLayer(marker);
                        eliminarLugarDestacado(lugar.id);
                    }
                });
            });
        })
        .catch(error => console.error('Error al cargar los lugares destacados:', error));
}

// Función para guardar un lugar destacado en la base de datos
function guardarLugarDestacado(nombre, descripcion, direccion, latitud, longitud, tipoMarcador) {
    fetch('/lugares-destacados', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ nombre, descripcion, direccion, latitud, longitud, tipoMarcador })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Lugar destacado guardado:', data);
        })
        .catch(error => console.error('Error al guardar el lugar destacado:', error));
}

// Función para eliminar un lugar destacado de la base de datos
function eliminarLugarDestacado(id) {
    fetch(`/lugares-destacados/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('Lugar destacado eliminado:', data);
        })
        .catch(error => console.error('Error al eliminar el lugar destacado:', error));
}

// Habilitar la funcionalidad de añadir marcadores manualmente
map.on('click', function (e) {
    var latlng = e.latlng;

    // Solicitar información del usuario para el nuevo lugar
    var nombre = prompt('Introduce el nombre del lugar:');
    var descripcion = prompt('Introduce una descripción del lugar:');
    var direccion = prompt('Introduce la dirección del lugar:');
    var tipoMarcador = 'default'; // Puedes cambiar esto según el tipo de marcador que desees

    if (nombre && descripcion && direccion) {
        // Añadir un marcador en la ubicación clickeada
        var newMarker = L.marker(latlng).addTo(map)
            .bindPopup(`<b>${nombre}</b><br>${descripcion}<br>Dirección: ${direccion}`)
            .openPopup();

        // Guardar el lugar en la base de datos
        guardarLugarDestacado(nombre, descripcion, direccion, latlng.lat, latlng.lng, tipoMarcador);

        // Habilitar la eliminación del marcador con clic derecho
        newMarker.on('contextmenu', function () {
            if (confirm('¿Deseas eliminar este lugar?')) {
                map.removeLayer(newMarker);
                eliminarLugarDestacado(newMarker.options.id); // Eliminar de la base de datos
            }
        });
    }
});

// Cargar los lugares destacados al iniciar el mapa
cargarLugaresDestacados();