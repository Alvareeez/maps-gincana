// Inicializar el mapa y establecer la vista a una ubicación y zoom específicos
var map = L.map('map').setView([40.416775, -3.703790], 6); // Coordenadas de Madrid, España

// Añadir una capa de tiles (baldosas) de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Función para guardar un lugar en la base de datos
function guardarLugar(pista, latitud, longitud) {
    fetch('/lugares', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ pista, latitud, longitud })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Lugar guardado:', data);
        })
        .catch(error => console.error('Error al guardar el lugar:', error));
}

// Función para eliminar un lugar de la base de datos
function eliminarLugar(id) {
    fetch(`/lugares/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('Lugar eliminado:', data);
        })
        .catch(error => console.error('Error al eliminar el lugar:', error));
}

// Añadir el control de geocodificación
var geocoder = L.Control.geocoder({
    defaultMarkGeocode: false
}).on('markgeocode', function (e) {
    var latlng = e.geocode.center;

    // Añadir un marcador en la ubicación encontrada
    var newMarker = L.marker(latlng).addTo(map)
        .bindPopup(e.geocode.name)
        .openPopup();

    // Guardar el lugar en la base de datos
    guardarLugar(e.geocode.name, latlng.lat, latlng.lng);

    // Habilitar la eliminación del marcador con clic derecho
    newMarker.on('contextmenu', function () {
        map.removeLayer(newMarker);
        eliminarLugar(newMarker.options.id); // Eliminar de la base de datos
    });

    // Centrar el mapa en la ubicación encontrada
    map.setView(latlng, 15);
}).addTo(map);

// Habilitar la funcionalidad de añadir marcadores manualmente
map.on('click', function (e) {
    var latlng = e.latlng;

    // Añadir un marcador en la ubicación clickeada
    var newMarker = L.marker(latlng).addTo(map)
        .bindPopup('Nuevo punto de interés<br>Lat: ' + latlng.lat.toFixed(5) + '<br>Lng: ' + latlng.lng.toFixed(5))
        .openPopup();

    // Guardar el lugar en la base de datos
    guardarLugar('Nuevo punto de interés', latlng.lat, latlng.lng);

    // Habilitar la eliminación del marcador con clic derecho
    newMarker.on('contextmenu', function () {
        map.removeLayer(newMarker);
        eliminarLugar(newMarker.options.id); // Eliminar de la base de datos
    });
});