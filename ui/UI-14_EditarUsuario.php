<?php

// ------------------------------------------------------------
// UI-14: Editar usuario
// Caso de uso asociado: CU-08-2 Editar usuario
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';

if (!isset($_GET['usuario']))
{
    die("No se especificó el usuario");
}
$usuario_v = $_GET['usuario'];

//<!-- Paso 1 del CU-08-2: El GTR-01 obtiene el usuario seleccionado -->
$usuario = GestionarUsuario::obtenerUsuarioBD($usuario_v);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_val = $_POST['usuario'];
    $nombre_val = $_POST['nombre'];
    $contrasena_val = $_POST['password'];
    $rol_val = $_POST['rol'];

    //<!-- Paso 10 del CU-08-2: Actualiza el usuario -->
    GestionarUsuario::actualizarDatosUsuarioBD($usuario_val, $nombre_val, $contrasena_val, $rol_val);
    
    //<!-- Paso 11 del CU-08-2: Redirige al AC-01 a la interfaz UI-12 -->
    header("Location: UI-12_VisualizarUsuarios.php");
    exit;
    
}

?>

<!-- Paso 2-3 del CU-08-2: La interfaz se carga y presenta los campos pertinentes -->

 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar concepto</title>
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/configuracion.css">
    <link rel="stylesheet" href="../css/icons.css">
</head>
<body>
<div class="contenedor-principal">
    <header class="barra-superior">
        <section class="seccion-izquierda">
            <h1 class="titulo-app">On a budget</h1>
        </section>

        <section class="seccion-derecha">
            <h2 class="subtitulo">Configuración</h2>

            <div class="info-usuario">
                <span class="nombre-usuario">
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <span class="rol-usuario">
                        <?php echo htmlspecialchars($_SESSION['rol']); ?>
                </span>
            </div>
        </section>
    </header>

    <div class="contenedor-medio">
        <!-- Menú lateral -->
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="UI-05_Balance.php">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="UI-07_CuentaPersonal.php">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="UI-10_VisualizarAgenda.php">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="UI-11_VisualizarRanking.php">
                    <i class="icono icono-grafico"></i>Ranking
                </a>
                <a class="opcion-menu activa" href="UI-16_VisualizarConceptos.php">
                    <i class="icono icono-configuracion"></i>Configuración
                </a>
            </nav>

            <footer class="parte-abajo">
                <a class="opcion-menu" href="UI-01_InicioDeSesion.php">
                    <i class="icono icono-salir"></i>Cerrar sesión
                </a>
            </footer>
        </aside>

        <main class="contenedor-medio">
            <aside class="submenu-configuracion" id="Sub_menuConfig">
                <nav>
                    <?php if ($_SESSION['rol'] === 'Administrador familiar'): ?>
                        <a class="opcion-submenu activa" href="UI-12_VisualizarUsuarios.php">
                            <i></i>Usuarios
                        </a>
                    <?php endif; ?>
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                    <a class="opcion-submenu" href="UI-23_VisualizarTransaccion.php">
                        <i></i>Transacciones
                    </a>
                </nav>
            </aside>

            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Editar usuario</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                    

                    <!-- Paso 8-9 del CU-08-2: Se validan los datos y se comprueba que no hayan campos vacios -->
                    <form class="form-crear-concepto" method="POST">

                    <h1 style="text-align: center;">Editando usuario: </h1>
                        <!-- Paso 4 del CU-08-2: La interfaz se carga y presenta el usuario como no editable -->
                        <div class="campo-formulario">
                            <label for="usuario">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario->usuario) ?>" readonly>

                        </div>
                        
                        <!-- Paso 5 del CU-08-2: El AC-01 Administrador Familiar modifica el nombre de usuario -->
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre" value="<?= htmlspecialchars($usuario->nombre) ?>" required>
                        </div>

                        <!-- Paso 6 del CU-08-2: El AC-01 Administrador Familiar selecciona el rol -->
                        <div class="campo-formulario">
                            <label for="rol">Rol:</label>
                            <select id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="Administrador familiar">Administrador familiar</option>
                                <option value="Familiar">Familiar</option>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" placeholder = "Ingrese su contraseña" value="<?= htmlspecialchars($usuario->contrasena) ?>" required>
                        </div>

                        
                        <!-- Paso 7 del CU-08-2: El AC-01 Administrador Familiar selecciona la opción guardar -->
                        <div style="text-align:center;">
                            <div class="grupo-botones">
                                <button type="button" class="boton-crear boton-cancelar" onclick="window.location.href='UI-12_VisualizarUsuarios.php'">Cancelar</button>
                                <button type="submit" class="boton-crear">Guardar</button>
                            </div>

                        </div>

                    </form>
                </article>
            </section>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radiosPeriodo = document.querySelectorAll('input[name="periodo"]');
    const campoPersonalizado = document.querySelector('.periodicidad-personalizada');

    radiosPeriodo.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === "Personalizado") {
                campoPersonalizado.style.display = "flex";
                campoPersonalizado.style.alignItems = "center";
                campoPersonalizado.style.gap = "10px";
            } else {
                campoPersonalizado.style.display = "none";
            }
        });
    });
});
</script>
</body>
</html>
