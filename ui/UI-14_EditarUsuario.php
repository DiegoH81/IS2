<?php

// ------------------------------------------------------------
// UI-22: Editar categoria
// Caso de uso asociado: CU-08-2 Editar usuario
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';

if (!isset($_GET['usuario']))
{
    die("No se especificó el usuario");
}
$usuario_v = $_GET['usuario'];

$usuario = GestionarUsuario::obtenerUsuarioBD($usuario_v);
//var_dump($usuario);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_val = $_POST['usuario'];
    $nombre_val = $_POST['nombre'];
    $contrasena_val = $_POST['password'];
    $rol_val = $_POST['rol'];

    // Llamar a la función del gestor para actualizar
    GestionarUsuario::actualizarDatosUsuarioBD($usuario_val, $nombre_val, $contrasena_val, $rol_val);
    
    header("Location: UI-12_VisualizarUsuarios.php");
    exit;
    
}

?>

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
        <!-- Paso 4 del CU-17: La interfaz muestra la opción de Crear categoría. -->
        <!-- Menú lateral -->
        <aside class="menu-lateral" id="menuLateral">
            <nav>
                <a class="opcion-menu" href="daily_input.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="#">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="#">
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
                    

                    <form class="form-crear-concepto" method="POST">

                    <h1 style="text-align: center;">Editando usuario: </h1>
                        

                        
                        <div class="campo-formulario">
                            <label for="usuario">Usuario:</label>

                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" readonly>

                        </div>
                        
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>

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
                            <input type="password" id="password" name="password" placeholder = "Ingrese su contraseña" value="<?= htmlspecialchars($usuario['contrasena']) ?>" required>
                        </div>

                        <!-- Paso 12 del CU-16: El AC-02 selecciona la opción Crear. -->
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
