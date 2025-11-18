document.addEventListener('DOMContentLoaded', function() {
    // Obtener los elementos del DOM
    const filtroBoton = document.getElementById('filtro-semanal'); // Botón que muestra el filtro de fechas
    const contenedorFechas = document.getElementById('filtro-fechas'); // Contenedor de las fechas
    const fechaInicio = document.getElementById('fecha-inicio'); // Input de fecha de inicio
    const fechaFin = document.getElementById('fecha-fin'); // Input de fecha de fin
    const botonAplicar = document.getElementById('aplicar-fechas'); // Botón para aplicar las fechas

    // Setear las fechas por defecto (hoy)
    const hoy = new Date().toISOString().split('T')[0];  // 'YYYY-MM-DD'
    fechaInicio.value = hoy;  // Asignar la fecha de hoy al input de fecha inicio
    fechaFin.value = hoy;  // Asignar la fecha de hoy al input de fecha fin

    // Mostrar el contenedor de fechas al hacer clic en el botón
    filtroBoton.addEventListener('click', function() {
        contenedorFechas.style.display = (contenedorFechas.style.display === 'none' || contenedorFechas.style.display === '') ? 'block' : 'none';
    });

    // Acción cuando el usuario aplica el filtro de fechas
    botonAplicar.addEventListener('click', function() {
        const fechaInicioValor = fechaInicio.value;
        const fechaFinValor = fechaFin.value;

        if (fechaInicioValor && fechaFinValor) {
            console.log('Rango de fechas:', fechaInicioValor, 'a', fechaFinValor);

            // Redirigir a la misma página con los parámetros de fecha
            // Tomamos el modo desde el checkbox (personal/familiar)
            const modo = document.querySelector('.boton-switch input').checked ? 'familiar' : 'personal';

            // Redirigir a la página con los parámetros de fecha
            window.location.href = `UI-05_Balance.php?modo=${modo}&fecha_inicio=${fechaInicioValor}&fecha_fin=${fechaFinValor}`;
        } else {
            alert('Por favor, selecciona un rango de fechas válido');
        }

        // Ocultar el contenedor de las fechas después de aplicar
        contenedorFechas.style.display = 'none';
    });
});
