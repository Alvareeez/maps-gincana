<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/adminStyle.css') }}">
    <title>Administrar Usuarios</title>
</head>
<body>

    <!-- Navbar -->
    <nav class="admin-navbar">
        <div class="navbar-container">
            <a href="/" class="logo-link">
                <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo" class="navbar-logo">
            </a>
            <ul class="navbar-menu">
                <li><a href="#" class="nav-link" data-section="usuarios">Usuarios</a></li>
                <li><a href="#" class="nav-link" data-section="pruebas">Pruebas</a></li>
                <li><a href="#" class="nav-link" data-section="lugares">Lugares</a></li>
                <li><a href="#" class="nav-link" data-section="niveles">Niveles</a></li>
                <li><a href="#" class="nav-link" data-section="gincanas">Gincanas</a></li>
                <li><a href="#" class="nav-link" data-section="lugarDestacado">Lugar Destacado</a></li>
                <li><a href="#" class="nav-link" data-section="etiqueta">Etiqueta</a></li>
                <li><a href="#" class="nav-link" data-section="tipoMarcador">Tipo Marcador</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="admin-container">
    <div id="usuarios-section" class="content-section">
    <h1>Lista de Usuarios</h1>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->nombre }}</td>
                    <td>{{ $user->apellido }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button class="btn-edit" data-id="{{ $user->id }}" data-username="{{ $user->username }}" data-nombre="{{ $user->nombre }}" data-apellido="{{ $user->apellido }}" data-email="{{ $user->email }}" data-idrol="{{ $user->id_rol }}">Editar</button>
                        <button class="btn-delete" data-id="{{ $user->id }}">Eliminar</button>
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Botón para añadir un nuevo usuario -->
    <button id="btn-add">Añadir Usuario</button>

    <!-- Modal para añadir un nuevo usuario -->
    <div id="modal">
        <div class="modal-content">
            <h2 id="modal-title">Añadir Usuario</h2>

            <!-- Formulario para añadir un nuevo usuario -->
            <form id="user-form">
                @csrf
                <input type="hidden" id="user-id">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <label for="id_rol">Rol</label>
                <select id="id_rol" name="id_rol" required>

                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" id="save-btn">Guardar</button>
            </form>

            <button id="close-modal">X</button>
        </div>
    </div>

    <!-- Modal para editar un usuario -->
    <div id="modal-edit">
        <div class="modal-content">
            <h2 id="modal-title-edit">Editar Usuario</h2>

            <!-- Formulario para editar un usuario -->
            <form id="edit-form">
                @csrf
                <input type="hidden" id="edit-user-id">

                <label for="edit-username">Username</label>
                <input type="text" id="edit-username" name="username" required>

                <label for="edit-nombre">Nombre</label>
                <input type="text" id="edit-nombre" name="nombre" required>

                <label for="edit-apellido">Apellido</label>
                <input type="text" id="edit-apellido" name="apellido" required>

                <label for="edit-email">Email</label>
                <input type="email" id="edit-email" name="email" required>

                <label for="edit-password">Contraseña (dejar en blanco si no se desea cambiar)</label>
                <input type="password" id="edit-password" name="password">

                <label for="edit-id_rol">Rol</label>
                <select id="edit-id_rol" name="id_rol" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" id="edit-btn">Actualizar</button>
            </form>

            <button id="close-modal-edit">X</button>
        </div>
    </div>
    </div>

    <!--   ------------------------------ -->
            <!-- CRUDS DE PRUEBAS -->
    <!--   ------------------------------ -->

    <div id="pruebas-section" class="content-section hidden">
    <h1>Lista de Pruebas</h1>
    <table>
        <thead>
            <tr>
                <th>Pregunta</th>
                <th>Respuesta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pruebas as $prueba)
                <tr>
                    <td>{{ $prueba->pregunta }}</td>
                    <td>{{ $prueba->respuesta }}</td>
                    <td>
                        <!-- Botón para editar una prueba -->
                        <button class="btn-edit-prueba" data-id="{{ $prueba->id }}" data-pregunta="{{ $prueba->pregunta }}" data-respuesta="{{ $prueba->respuesta }}">Editar</button>
                        <button class="btn-delete-prueba" data-id="{{ $prueba->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Botón para añadir una nueva prueba -->
    <button id="btn-add-prueba">Añadir Prueba</button>

    <!-- Modal para añadir una nueva prueba -->
    <div id="modal-prueba" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-prueba">Añadir Prueba</h2>

            <form id="prueba-form">
                @csrf
                <input type="hidden" id="prueba-id">
                <label for="pregunta">Pregunta</label>
                <input type="text" id="pregunta" name="pregunta" required>

                <label for="respuesta">Respuesta</label>
                <input type="text" id="respuesta" name="respuesta" required>

                <button type="submit" id="save-prueba-btn">Guardar</button>
            </form>

            <button id="close-modal-prueba">X</button>
        </div>
    </div>
    </div>

    <!--   ------------------------------ -->
            <!-- CRUDS DE LUGARES -->
    <!--   ------------------------------ -->
    
    <div id="lugares-section" class="content-section hidden">
    <h1>Lista de Lugares</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Pista</th>
                <th>Latitud</th>
                <th>Longitud</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lugares as $lugar)
                <tr>
                    <td>{{ $lugar->nombre }}</td>
                    <td>{{ $lugar->pista }}</td>
                    <td>{{ $lugar->latitud }}</td>
                    <td>{{ $lugar->longitud }}</td>
                    <td>
                        <!-- Botón para editar un lugar -->
                        <button class="btn-edit-lugar" data-id="{{ $lugar->id }}" data-nombre="{{ $lugar->nombre }}" data-pista="{{ $lugar->pista }}" data-latitud="{{ $lugar->latitud }}" data-longitud="{{ $lugar->longitud }}">Editar</button>
                        <button class="btn-delete-lugar" data-id="{{ $lugar->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Botón para añadir un nuevo lugar -->
    <button id="btn-add-lugar">Añadir Lugar</button>

<!-- Modal para añadir un nuevo lugar -->
<div id="modal-lugar" style="display: none;">
    <div class="modal-content">
        <h2 id="modal-title-lugar">Añadir Lugar</h2>
        
        <!-- Formulario de creación -->
        <form id="lugar-form" action="{{ route('lugares.store') }}" method="POST">
            @csrf
            <input type="hidden" id="lugar-id" name="id">

            <label for="nombre-lugar">Nombre</label>
            <input type="text" id="nombre-lugar" name="nombre" required>

            <label for="pista">Pista</label>
            <input type="text" id="pista" name="pista" required>

            <label for="latitud">Latitud</label>
            <input type="text" id="latitud" name="latitud" required step="0.00000001">

            <label for="longitud">Longitud</label>
            <input type="text" id="longitud" name="longitud" required step="0.00000001">

            <button type="submit" id="save-lugar-btn">Guardar</button>
        </form>

        <button id="close-modal-lugar">X</button>
    </div>
</div>

<!-- Modal para editar un lugar -->
<div id="modal-edit-lugar" style="display: none;">
    <div class="modal-content">
        <h2 id="modal-title-edit-lugar">Editar Lugar</h2>

        <!-- Formulario de edición -->
        <form id="edit-lugar-form" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-lugar-id" name="id">

            <label for="edit-nombre-lugar">Nombre</label>
            <input type="text" id="edit-nombre-lugar" name="nombre" required>

            <label for="edit-pista">Pista</label>
            <input type="text" id="edit-pista" name="pista" required>

            <label for="edit-latitud">Latitud</label>
            <input type="number" id="edit-latitud" name="latitud" required step="0.00000001">

            <label for="edit-longitud">Longitud</label>
            <input type="number" id="edit-longitud" name="longitud" required step="0.00000001">

            <button type="submit" id="edit-lugar-btn">Actualizar</button>
        </form>

        <button id="close-modal-edit-lugar">X</button>
    </div>
</div>
</div>



    <!--   ------------------------------ -->
            <!-- CRUDS DE NIVELES -->
    <!--   ------------------------------ -->

    <div id="niveles-section" class="content-section hidden">
    <h1>Lista de Niveles</h1>

    <!-- Tabla de niveles -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Lugar</th>
                <th>Prueba</th>
                <th>Gincana</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($niveles as $nivel)
                <tr>
                    <td>{{ $nivel->nombre }}</td>
                    <td>{{ $nivel->lugar->nombre }}</td>
                    <td>{{ $nivel->prueba->pregunta }}</td>
                    <td>{{ $nivel->gincana->nombre }}</td>
                    <td>
                        <!-- Botón para editar nivel -->
                        <button class="btn-edit-nivel" data-id="{{ $nivel->id }}" data-nombre="{{ $nivel->nombre }}" data-id_lugar="{{ $nivel->id_lugar }}" data-id_prueba="{{ $nivel->id_prueba }}" data-id_gincana="{{ $nivel->id_gincana }}">Editar</button>
                        <!-- Botón para eliminar nivel -->
                        <button class="btn-delete-nivel" data-id="{{ $nivel->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Botón para añadir un nuevo nivel -->
    <button id="btn-add-nivel">Añadir Nivel</button>

    <!-- Modal para añadir un nuevo nivel -->
    <div id="modal-nivel" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-nivel">Añadir Nivel</h2>

            <!-- Formulario de creación -->
            <form id="nivel-form" action="{{ route('niveles.store') }}" method="POST">
                @csrf
                <input type="hidden" id="nivel-id" name="id">

                <label for="nombre-nivel">Nombre</label>
                <input type="text" id="nombre-nivel" name="nombre" required>

                <label for="id_lugar">Lugar</label>
                <select id="id_lugar" name="id_lugar" required>
                    <option value="">Selecciona un lugar</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>

                <label for="id_prueba">Prueba</label>
                <select id="id_prueba" name="id_prueba" required>
                    <option value="">Selecciona una prueba</option>
                    @foreach($pruebas as $prueba)
                        <option value="{{ $prueba->id }}">{{ $prueba->pregunta }}</option>
                    @endforeach
                </select>

                <label for="id_gincana">Gincana</label>
                <select id="id_gincana" name="id_gincana" required>
                    <option value="">Selecciona una gincana</option>
                    @foreach($gincanas as $gincana)
                        <option value="{{ $gincana->id }}">{{ $gincana->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" id="save-nivel-btn">Guardar</button>
            </form>

            <button id="close-modal-nivel">X</button>
        </div>
    </div>

    <!-- Modal para editar un nivel -->
    <div id="modal-edit-nivel" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-edit-nivel">Editar Nivel</h2>

            <!-- Formulario de edición -->
            <form id="edit-nivel-form" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-nivel-id" name="id">

                <label for="edit-nombre-nivel">Nombre</label>
                <input type="text" id="edit-nombre-nivel" name="nombre" required>

                <label for="edit-id_lugar">Lugar</label>
                <select id="edit-id_lugar" name="id_lugar" required>
                    <option value="">Selecciona un lugar</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>

                <label for="edit-id_prueba">Prueba</label>
                <select id="edit-id_prueba" name="id_prueba" required>
                    <option value="">Selecciona una prueba</option>
                    @foreach($pruebas as $prueba)
                        <option value="{{ $prueba->id }}">{{ $prueba->pregunta }}</option>
                    @endforeach
                </select>

                <label for="edit-id_gincana">Gincana</label>
                <select id="edit-id_gincana" name="id_gincana" required>
                    <option value="">Selecciona una gincana</option>
                    @foreach($gincanas as $gincana)
                        <option value="{{ $gincana->id }}">{{ $gincana->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" id="edit-nivel-btn">Actualizar</button>
            </form>

            <button id="close-modal-edit-nivel">X</button>
        </div>
    </div>
    </div>


    <!--   ------------------------------ -->
            <!-- CRUDS DE GINCANA -->
    <!--   ------------------------------ -->

    <div id="gincanas-section" class="content-section hidden">
        <h1>Lista de Gincanas</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Jugadores</th>
                <th>Grupos</th>
                <th>Grupo Ganador</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gincanas as $gincana)
                <tr>
                    <td>{{ $gincana->nombre }}</td>
                    <td>{{ ucfirst($gincana->estado) }}</td>
                    <td>{{ $gincana->cantidad_jugadores }}</td>
                    <td>{{ $gincana->cantidad_grupos }}</td>
                    <td>{{ $gincana->ganadorGrupo->nombre ?? 'Sin ganador' }}</td>
                    <td>
                        <button class="btn-edit-gincana" 
                                data-id="{{ $gincana->id }}"
                                data-nombre="{{ $gincana->nombre }}"
                                data-estado="{{ $gincana->estado }}"
                                data-cantidad_jugadores="{{ $gincana->cantidad_jugadores }}"
                                data-cantidad_grupos="{{ $gincana->cantidad_grupos }}"
                                data-id_ganador="{{ $gincana->id_ganador }}">
                            Editar
                        </button>
                        <button class="btn-delete-gincana" data-id="{{ $gincana->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button id="btn-add-gincana">Añadir Gincana</button>

    <!-- Modal para añadir -->
    <div id="modal-gincana" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-gincana">Añadir Gincana</h2>
            <form id="gincana-form" action="{{ route('gincanas.store') }}" method="POST">
                @csrf
                <input type="hidden" id="gincana-id" name="id">

                <label for="nombre-gincana">Nombre</label>
                <input type="text" id="nombre-gincana" name="nombre" required>

                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="abierta">Abierta</option>
                    <option value="ocupada">Ocupada</option>
                </select>

                <label for="cantidad_jugadores">Cantidad de Jugadores</label>
                <input type="number" id="cantidad_jugadores" name="cantidad_jugadores" min="1" required>

                <label for="cantidad_grupos">Cantidad de Grupos</label>
                <input type="number" id="cantidad_grupos" name="cantidad_grupos" min="1" required>

                <button type="submit" id="save-gincana-btn">Guardar</button>
            </form>
            <button id="close-modal-gincana">X</button>
        </div>
    </div>

    <!-- Modal para editar -->
    <div id="modal-edit-gincana" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-edit-gincana">Editar Gincana</h2>
            <form id="edit-gincana-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-gincana-id" name="id">

                <label for="edit-nombre-gincana">Nombre</label>
                <input type="text" id="edit-nombre-gincana" name="nombre" required>

                <label for="edit-estado">Estado</label>
                <select id="edit-estado" name="estado" required>
                    <option value="abierta">Abierta</option>
                    <option value="ocupada">Ocupada</option>
                </select>

                <label for="edit-cantidad_jugadores">Cantidad de Jugadores</label>
                <input type="number" id="edit-cantidad_jugadores" name="cantidad_jugadores" min="1" required>

                <label for="edit-cantidad_grupos">Cantidad de Grupos</label>
                <input type="number" id="edit-cantidad_grupos" name="cantidad_grupos" min="1" required>

                <button type="submit" id="edit-gincana-btn">Actualizar</button>
            </form>
            <button id="close-modal-edit-gincana">X</button>
        </div>
    </div>
    </div>

    <!--   ------------------------------ -->
    <!-- CRUDS DE LUGAR DESTACADO -->
    <!--   ------------------------------ -->
    <div id="lugarDestacado-section" class="content-section hidden">
    <h1>Lista de Lugares Destacados</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Dirección</th>
                <th>Latitud</th>
                <th>Longitud</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lugaresDestacados as $lugar)
                <tr>
                    <td>{{ $lugar->nombre }}</td>
                    <td>{{ $lugar->descripcion }}</td>
                    <td>{{ $lugar->direccion }}</td>
                    <td>{{ $lugar->latitud }}</td>
                    <td>{{ $lugar->longitud }}</td>
                    <td>
                    <button class="btn-edit-lugar-destacado" 
                            data-id="{{ $lugar->id }}"
                            data-nombre="{{ $lugar->nombre }}"
                            data-descripcion="{{ $lugar->descripcion }}"
                            data-direccion="{{ $lugar->direccion }}"
                            data-latitud="{{ $lugar->latitud }}"
                            data-longitud="{{ $lugar->longitud }}"
                            data-tipo-marcador="{{ $lugar->tipoMarcador->id ?? '' }}">
                        Editar
                    </button>
                        <button class="btn-delete-lugar-destacado" data-id="{{ $lugar->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button id="btn-add-lugar-destacado">Añadir Lugar Destacado</button>

<!-- Modal para añadir -->
<div id="modal-lugar-destacado" style="display: none;">
    <div class="modal-content">
        <h2 id="modal-title-lugar-destacado">Añadir Lugar Destacado</h2>
        <form id="lugar-destacado-form">
            @csrf
            <input type="hidden" id="lugar-destacado-id" name="id">

            <label for="nombre-lugar-destacado">Nombre</label>
            <input type="text" id="nombre-lugar-destacado" name="nombre" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" required>

            <label for="latitud">Latitud</label>
            <input type="number" id="create-latitud" name="latitud" step="0.00000001" required>

            <label for="longitud">Longitud</label>
            <input type="number" id="longitudDestacada" name="longitud" step="0.00000001" required>

            <label for="tipoMarcador">Tipo Marcador</label>
            <select id="tipoMarcador" name="tipoMarcador" required>
                @foreach($tipoMarcadores as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>

            <button type="submit" id="save-lugar-destacado-btn">Guardar</button>
        </form>
        <button id="close-modal-lugar-destacado">X</button>
    </div>
</div>

<!-- Modal para editar -->
<div id="modal-edit-lugar-destacado" style="display: none;">
    <div class="modal-content">
        <h2>Editar Lugar Destacado</h2>
        <form id="edit-lugar-destacado-form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-lugar-destacado-id" name="id">

            <label for="edit-nombreDestacado">Nombre</label>
            <input type="text" id="edit-nombreDestacado" name="nombre" required>

            <label for="edit-descripcion">Descripción</label>
            <textarea id="edit-descripcion" name="descripcion" required></textarea>

            <label for="edit-direccion">Dirección</label>
            <input type="text" id="edit-direccion" name="direccion" required>

            <label for="edit-latitudDestacada">Latitud</label>
            <input type="number" id="edit-latitudDestacada" name="latitud" step="0.00000001" required>

            <label for="edit-longitudDestacada">Longitud</label>
            <input type="number" id="edit-longitudDestacada" name="longitud" step="0.00000001" required>

            <label for="edit-tipo-marcador">Tipo Marcador</label>
            <select id="edit-tipo-marcador" name="tipoMarcador" required>
                @foreach($tipoMarcadores as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>

            <button type="submit" id="edit-lugar-destacado-btn">Actualizar</button>
        </form>
        <button id="close-modal-edit-lugar-destacado">X</button>
    </div>
</div>    </div>

    <!--   ------------------------------ -->
    <!-- CRUDS DE ETIQUETA -->
    <!--   ------------------------------ -->
    <div id="etiqueta-section" class="content-section hidden">
    <h1>Lista de Etiquetas</h1>
    <button id="btn-add-etiqueta">Añadir Etiqueta</button>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($etiquetas as $etiqueta)
                <tr>
                    <td>{{ $etiqueta->id }}</td>
                    <td>{{ $etiqueta->nombre }}</td>
                    <td>
                        <button class="btn-edit-etiqueta" 
                                data-id="{{ $etiqueta->id }}"
                                data-nombre="{{ $etiqueta->nombre }}">
                            Editar
                        </button>
                        <button class="btn-delete-etiqueta" data-id="{{ $etiqueta->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal para añadir/editar -->
    <div id="modal-etiqueta" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-etiqueta">Añadir Etiqueta</h2>
            <form id="etiqueta-form">
                @csrf
                <input type="hidden" id="etiqueta-id" name="id">
                
                <label for="nombre-etiqueta">Nombre</label>
                <input type="text" id="nombre-etiqueta" name="nombre" required>
                
                <button type="submit" id="save-etiqueta-btn">Guardar</button>
            </form>
            <button id="close-modal-etiqueta">X</button>
        </div>
    </div>
</div>
    <!--   ------------------------------ -->
    <!-- CRUDS DE TIPO MARCADOR -->
    <!--   ------------------------------ -->
    <div id="tipoMarcador-section" class="content-section hidden">
    <h1>Lista de Tipos de Marcador</h1>
    <button id="btn-add-tipo-marcador">Añadir Tipo Marcador</button>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tipoMarcadores as $tipo)
                <tr>
                    <td>{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>
                        <button class="btn-edit-tipo-marcador" 
                                data-id="{{ $tipo->id }}"
                                data-nombre="{{ $tipo->nombre }}">
                            Editar
                        </button>
                        <button class="btn-delete-tipo-marcador" data-id="{{ $tipo->id }}">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal para añadir/editar -->
    <div id="modal-tipo-marcador" style="display: none;">
        <div class="modal-content">
            <h2 id="modal-title-tipo-marcador">Añadir Tipo Marcador</h2>
            <form id="tipo-marcador-form">
                @csrf
                <input type="hidden" id="tipo-marcador-id" name="id">
                
                <label for="nombre-tipo-marcador">Nombre</label>
                <input type="text" id="nombre-tipo-marcador" name="nombre" required>
                
                <button type="submit" id="save-tipo-marcador-btn">Guardar</button>
            </form>
            <button id="close-modal-tipo-marcador">X</button>
        </div>
    </div>
</div>






    <!-- Incluir los archivos JS -->
    <script src="{{ asset('js/adminNavbar.js') }}"></script>
    <script src="{{ asset('js/editUser.js') }}"></script>
    <script src="{{ asset('js/addUser.js') }}"></script>
    <script src="{{ asset('js/deleteUser.js') }}"></script>
    <script src="{{ asset('js/managePrueba.js') }}"></script>
    <script src="{{ asset('js/manageLugar.js') }}"></script>
    <script src="{{ asset('js/manageNivel.js') }}"></script>
    <script src="{{ asset('js/manageGincana.js') }}"></script>
    <script src="{{ asset('js/manageLugarDestacado.js') }}"></script>
    <script src="{{ asset('js/manageEtiqueta.js') }}"></script>
    <script src="{{ asset('js/manageTipoMarcador.js') }}"></script>




</body>
</html>
