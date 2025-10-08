<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="css/principal.css">
    <link rel="stylesheet" href="css/configuracion.css">
    <link rel="stylesheet" href="css/log_reg.css">
    <!-- CSS de íconos -->
    <link rel="stylesheet" href="css/icons.css">

</head>
<body>

<div class="contenedor-principal">
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

            <article class="tabla" style = "width: 100%;">
                <form class="form-crear-concepto" action="guardar_concepto.php" method="POST">
                    <!-- Categoría -->

                    <h2 style="font-size: 3em; text-align: center;">Registrar</h2>
 
                    <div class="campo-formulario">
                        <label for="nombre">Usuario:</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese su usuario" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="nombre">Contraseña:</label>
                        <input type="text" id="password" name="password" placeholder="Ingrese su contraseña" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="nombre">Confirmar contraseña:</label>
                        <input type="text" id="c_password" name="c_password" placeholder="Ingrese su confirmar contraseña" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="nombre">Contraseña familiar:</label>
                        <input type="text" id="f_password" name="f_password" placeholder="Ingrese contraseña familiar" required>
                    </div>

                    

                    <!-- Botón -->
                    <div class="campo-formulario" style="display: flex; justify-content: center; gap: 20px;">
                        <button type="button" class="boton-crear-usuario" onclick="window.location.href='log_in.php'">
                            Cancelar
                        </button>
                    </div>

                    <div class="campo-formulario" style="display: flex; justify-content: center; gap: 20px;">
                        <button type="submit" class="boton-crear-usuario">Registrar</button>
                    </div>


                    <p style="text-align: center;">
                        ¿No tienes familia? 
                        <a href="register_family.php" style="color: #3862AA; text-decoration: none; font-weight: bold;">
                            Registrar familia
                        </a>
                    </p>
                </form>

            </article>            
        </section>

    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Switch on/off
        const switchBtn = document.querySelector('.boton-switch input');
        if (switchBtn) {
            switchBtn.addEventListener('change', function() {
                console.log('Modo:', this.checked ? 'Personal' : 'Familiar');
            });
        }
    });
</script>

</body>
</html>

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
