<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="css/principal.css">
    <link rel="stylesheet" href="css/configuracion.css">
    <link rel="stylesheet" href="css/log_reg.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="css/icons.css">

</head>
<body>

<div class="contenedor-principal" style="height: 100%">
    <!-- Cabecera -->
    <header class="barra-superior" style="background-color: #3862AA;">
        <!-- Parte izquierda oscura con el título -->
        <section class="seccion-izquierda">
            <h1 class="titulo-app" >On a budget</h1>
        </section>
    </header>
    
    <!-- Contenido principal -->
    <div class="contenedor-form">
        <section class="contenedor-tablas-reg">

            <article class="tabla" style = "width: 100%; height:100%">
                <form class="form-crear-concepto" action="guardar_concepto.php" method="POST">
                    <!-- Categoría -->

                    <h2 style="font-size: 3em; text-align: center;">Log in</h2>

                    <!-- Nombre -->
                    <div class="campo-formulario">
                        <label for="nombre">Usuario:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese su usuario" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                    </div>

                    

                    <!-- Botón -->
                    <div class="campo-formulario" style="display: flex; justify-content: center; gap: 20px;">
                        <button type="submit" class="boton-crear-usuario">Ingresar</button>
                    </div>

                    <p style="text-align: center;">
                        ¿No tienes cuenta? 
                        <a href="register.php" style="color: #3862AA; text-decoration: none; font-weight: bold;">
                            Regístrate
                        </a>
                    </p>
                </form>

            </article>            
        </section>

    </div>
</div>