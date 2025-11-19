<?php

// ------------------------------------------------------------
// UI-22: Editar categoria
// Caso de uso asociado: CU-10-2 Editar categoría
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-09_GestionarCategoria.php';

if (!isset($_GET['idcategoria']))
{
    die("No se especificó la categoria");
}
$id_categoria = $_GET['idcategoria'];

$categoria = GestionarCategoria::obtenerCategoriaIdBD($id_categoria);
//var_dump($categoria);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_val = $_POST['nombre'];
    $descripcion_val = $_POST['descripcion'];

    //<!-- Paso 8 del CU-10-2: El GTR-09 actualiza la categoría en la BD. -->
    GestionarCategoria::actualizarCategoriaBD($id_categoria, $nombre_val, $descripcion_val);
    
    //<!-- Paso 9 del CU-10-2: Se redirige el AC-02 hacia la UI-20. -->
    header("Location: UI-20_VisualizarCategoria.php");
    exit;
    
}
?>

<!-- Paso 1 del CU-10-2: La UI-22 se carga y presenta los campos nombre y descripción. -->

<!-- Paso 6-7 del CU-10-2: La UI-22 valida los campos. -->
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
                        <a class="opcion-submenu" href="UI-12_VisualizarUsuarios.php">
                            <i></i>Usuarios
                        </a>
                    <?php endif; ?>
                    <a class="opcion-submenu" href="UI-16_VisualizarConceptos.php">
                        <i></i>Conceptos
                    </a>
                    <a class="opcion-submenu activa" href="UI-20_VisualizarCategoria.php">
                        <i></i>Categorías
                    </a>
                </nav>
            </aside>

            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Editar categoria</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <form class="form-crear-concepto" method="POST">

                        <h1 style="text-align: center;">Editar categoría</h1>
                        
                        <!-- Paso 3-4 del CU-10-2: El usuario modifica el nombre y descripción. -->
                        <div class="campo-formulario">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese nombre" value="<?= htmlspecialchars($categoria->nombre) ?>" required>
                        </div>

                        <div class="campo-formulario">
                            <label for="descripcion">Descripción:</label>
                            <textarea 
                                rows="5" 
                                cols="40" 
                                name="descripcion" 
                                id="descripcion" 
                                placeholder="Ingrese la descripción" 
                                spellcheck="false"
                            ><?= htmlspecialchars($categoria->descripcion) ?></textarea>
                        </div>

                        <!-- Paso 2 del CU-10-2: La UI-22 se carga y presenta los botones de guardar y cancelar. -->
                        
                        <!-- Paso 5 del CU-10-2: El AC-02 selecciona la opción guardar. -->
                        <div style="text-align:center;">
                            <div class="grupo-botones">
                                <button type="button" class="boton-crear boton-cancelar" onclick="window.location.href='UI-20_VisualizarCategoria.php'">Cancelar</button>
                                <button type="submit" class="boton-crear">Guardar categoria</button>
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
