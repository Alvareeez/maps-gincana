<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <!-- Incluir el archivo de estilo para el modal si es necesario -->
    <style>
        /* Estilo básico para el modal */
        #modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
        }
        button {
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>Lista de Usuarios</h1>

    <!-- Mostrar los usuarios -->
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
            @foreach($users as $user)  <!-- Aquí 'users' es el nombre de la variable pasada desde el controlador -->
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->nombre }}</td>
                    <td>{{ $user->apellido }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button class="btn-edit" data-id="{{ $user->id }}">Editar</button>
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
                @csrf <!-- Token CSRF para la seguridad de la solicitud -->
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

    <!-- Incluir el archivo JS para manejar el AJAX -->
    <script src="{{ asset('js/addUser.js') }}"></script>

</body>
</html>
