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

                    <h2 style="font-size: 3em; text-align: center;">Registrar familia</h2>
 
                    <div class="campo-formulario">
                        <label for="nombre">Apellido de la familia:</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="password-familiar" style="display: block; margin-bottom: 5px;">Contraseña familiar:</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="text" id="password-familiar" name="password-familiar" 
                                placeholder="Genera una contraseña" readonly style="flex: 1;">
                            <button type="button" id="generar-btn" 
                                style="background-color: #3862AA; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">
                                Generar
                            </button>
                        </div>
                    </div>
                    

                    <!-- Botón -->
                    <div class="campo-formulario" style="display: flex; justify-content: center; gap: 20px;">
                        <button type="button" class="boton-crear-usuario" onclick="window.location.href='register.php'">
                            Cancelar
                        </button>
                    </div>

                    <div class="campo-formulario" style="display: flex; justify-content: center; gap: 20px;">
                        <button type="submit" class="boton-crear-usuario">Registrar</button>
                    </div>
                </form>

            </article>            
        </section>

    </div>
</div>

<script>
    document.getElementById("generar-btn").addEventListener("click", function() {
        // Generador de contraseña tipo SSH (segura)
        const caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}[]:;<>,.?/~`-=";
        let contraseña = "";
        for (let i = 0; i < 16; i++) {
            contraseña += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }

        // Asignar la contraseña al input
        const input = document.getElementById("password-familiar");
        input.value = contraseña;
    });
</script>
