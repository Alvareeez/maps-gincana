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

            <button id="close-modal">Cerrar</button>
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

            <button id="close-modal-edit">Cerrar</button>
        </div>
    </div>

    <!--   ------------------------------ -->
            <!-- CRUDS DE PRUEBAS -->
    <!--   ------------------------------ -->

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

            <button id="close-modal-prueba">Cerrar</button>
        </div>
    </div>





    <!-- Incluir los archivos JS -->
    <script src="{{ asset('js/editUser.js') }}"></script>
    <script src="{{ asset('js/addUser.js') }}"></script>
    <script src="{{ asset('js/deleteUser.js') }}"></script>
    <script src="{{ asset('js/managePrueba.js') }}"></script>

</body>
</html>
