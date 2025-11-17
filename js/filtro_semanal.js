document.addEventListener('DOMContentLoaded', function() {
    const botonFiltro = document.getElementById('filtro-semanal');
    const contenedorFechas = document.getElementById('filtro-fechas');
    const botonAplicar = document.getElementById('aplicar-fechas');

    // Mostrar el contenedor de las fechas cuando se haga clic en "Corte semanal"
    botonFiltro.addEventListener('click', function() {
        contenedorFechas.style.display = (contenedorFechas.style.display === 'none' || contenedorFechas.style.display === '') ? 'block' : 'none';
    });

    // Acción cuando se aplica el rango de fechas
    botonAplicar.addEventListener('click', function() {
        const fechaInicio = document.getElementById('fecha-inicio').value;
        const fechaFin = document.getElementById('fecha-fin').value;

        if (fechaInicio && fechaFin) {
            console.log('Rango de fechas:', fechaInicio, 'a', fechaFin);
            // Aquí puedes enviar estos datos a PHP o hacer lo que necesites con las fechas seleccionadas
            // Por ejemplo, puedes redirigir o actualizar la vista con el rango de fechas seleccionado
        } else {
            alert('Por favor, selecciona un rango de fechas válido');
        }

        // Ocultar el contenedor después de aplicar
        contenedorFechas.style.display = 'none';
    });
});
