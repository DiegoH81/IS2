<?php

// ------------------------------------------------------------
// UI-07: Cuenta Personal
// Caso de uso asociado: CU-05 Gestionar cuenta
// ------------------------------------------------------------

session_start();
require_once '../gtr/GTR-01_GestionarUsuario.php';
require_once '../gtr/GTR-04_Validar.php';

//<!-- Paso 1 del CU-05: El GTR-04 obtiene el usuario actual -->
$usuario = Validar::obtenerUsuarioActual();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deshabilitar']))
{
    GestionarUsuario::cambiarEstadoUsuarioBD($usuario->idUsuario, 0);
    //var_dump($usuario->idUsuario);
    header("Location: UI-01_InicioDeSesion.php");
}
?>


<!-- Paso 2-6 del CU-05: Carga el formulario, presentando los campos de usuario, contraseña, nombre,
                         y presenta la posiblidad de cambiar la visibilidad de las contraseñas-->
<!-- Paso 7 del CU-05: La UI-07 Muestra los datos obtenidos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta</title>
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
            <h2 class="subtitulo">Cuenta</h2>

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
                <a class="opcion-menu" href="UI-04_RegistroDiario.php">
                    <i class="icono icono-documento"></i>Registro Diario
                </a>
                <a class="opcion-menu" href="UI-05_Balance.php">
                    <i class="icono icono-grafico"></i>Balance
                </a>
                <a class="opcion-menu activa" href="UI-07_CuentaPersonal.php">
                    <i class="icono icono-persona"></i>Cuenta
                </a>
                <a class="opcion-menu" href="UI-10_VisualizarAgenda.php">
                    <i class="icono icono-grafico"></i>Agenda
                </a>
                <a class="opcion-menu" href="UI-11_VisualizarRanking.php">
                    <i class="icono icono-grafico"></i>Ranking
                </a>
                <a class="opcion-menu" href="UI-16_VisualizarConceptos.php">
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

            <section class="contenedor-tablas">
                <article class="tabla">
                    <header>
                        <h2 class="titulo-tabla">Editar cuenta</h2>
                        <div class="linea-separadora"></div>
                        <div class="linea-azul"></div>
                    </header>

                    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                    

                    <form class="form-crear-concepto" method="POST">

                    <h1 style="text-align: center;">Mi cuenta </h1>
                        

                        
                        <div class="campo-formulario">
                            <label for="usuario">Usuario:</label>

                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario->usuario) ?>" readonly>

                        </div>
                        
                        <div class="campo-formulario">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" placeholder = "Ingrese su contraseña" value="<?= htmlspecialchars($usuario->contrasena) ?>" readonly>
                        </div>
                        

                        <!-- Paso 12 del CU-16: El AC-02 selecciona la opción Crear. -->
                        <div style="text-align:center;">

                            <div class="grupo-botones">
                                <button type="submit" name="deshabilitar" class="boton-crear boton-cancelar" \>Deshabilitar</button>
                                <button type="button" class="boton-crear" onclick="window.location.href='UI-08_EditarCuentaPersonal.php'" style="background-color: #3862AA;">Editar Perfil</button>
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
