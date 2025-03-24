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

            // Añadir el marcador al mapa
            var marker = L.marker([latitud, longitud]).addTo(map)
                .bindPopup(`<b>${nombre}</b><br>${descripcion}<br>Dirección: ${direccion}`)
                .openPopup();

            // Habilitar la eliminación del marcador con clic derecho
            marker.on('contextmenu', function () {
                if (confirm('¿Deseas eliminar este lugar?')) {
                    map.removeLayer(marker);
                    eliminarLugarDestacado(data.id);
                }
            });
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

// Habilitar la funcionalidad de añadir marcadores manualmente
map.on('click', async function (e) {
    var latlng = e.latlng;

    // Cargar los tipos de marcadores antes de mostrar el modal
    const opcionesTipoMarcador = await cargarTiposMarcadores();

    // Mostrar un modal de SweetAlert2 para ingresar los datos del lugar
    Swal.fire({
        title: 'Crear un nuevo lugar',
        html: `
            <div class="my-3">
                <input type="text" id="nombre" class="form-control my-3" placeholder="Nombre">
                <input type="text" id="descripcion" class="form-control my-3" placeholder="Descripción">
                <input type="text" id="direccion" class="form-control my-3" placeholder="Dirección">
                <select id="tipoMarcador" class="form-control my-3">
                    ${opcionesTipoMarcador} <!-- Opciones generadas dinámicamente -->
                </select>
                <input type="number" id="latitud" class="form-control my-3" value="${latlng.lat}" readonly>
                <input type="number" id="longitud" class="form-control my-3" value="${latlng.lng}" readonly>
            </div>
        `,
        confirmButtonText: 'Guardar',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        preConfirm: () => {
            const nombre = document.getElementById('nombre').value;
            const descripcion = document.getElementById('descripcion').value;
            const direccion = document.getElementById('direccion').value;
            const tipoMarcador = document.getElementById('tipoMarcador').value;
            const latitud = document.getElementById('latitud').value;
            const longitud = document.getElementById('longitud').value;

            if (!nombre || !descripcion || !direccion || !tipoMarcador) {
                Swal.showValidationMessage('Por favor, completa todos los campos');
                return false;
            }

            return { nombre, descripcion, direccion, tipoMarcador, latitud, longitud };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { nombre, descripcion, direccion, tipoMarcador, latitud, longitud } = result.value;

            // Guardar el lugar en la base de datos
            guardarLugarDestacado(nombre, descripcion, direccion, latitud, longitud, tipoMarcador);
        }
    });
});

// Lista para almacenar los lugares destacados
var lugaresDestacados = [];

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

                // Añadir los resultados al mapa
                lugares.forEach(lugar => {
                    var marker = L.marker([lugar.latitud, lugar.longitud]).addTo(map)
                        .bindPopup(`<b>${lugar.nombre}</b><br>${lugar.descripcion}<br>Dirección: ${lugar.direccion}`)
                        .openPopup();

                    // Centrar el mapa en el primer resultado
                    map.setView([lugar.latitud, lugar.longitud], 15);

                    // Guardar el marcador en la lista
                    lugaresDestacados.push({
                        id: lugar.id,
                        nombre: lugar.nombre,
                        direccion: lugar.direccion,
                        latitud: lugar.latitud,
                        longitud: lugar.longitud,
                        marker: marker
                    });
                });
            } else {
                alert('No se encontraron lugares con esa búsqueda.');
            }
        })
        .catch(error => console.error('Error al buscar lugares:', error));
}

// Evento para manejar el buscador
document.getElementById('buscador').addEventListener('input', function (e) {
    const query = e.target.value;
    if (query.length > 2) { // Buscar solo si hay más de 2 caracteres
        buscarLugar(query);
    }
});

// Cargar los lugares destacados al iniciar el mapa
cargarLugaresDestacados();