document.addEventListener('DOMContentLoaded', function() {
    const botonFiltro = document.getElementById('filtro-semanal');
    const contenedorFechas = document.getElementById('filtro-fechas');
    const botonAplicar = document.getElementById('aplicar-fechas');
    
    // Toggle del contenedor de fechas
    botonFiltro.addEventListener('click', function() {
        if (contenedorFechas.style.display === 'none') {
            contenedorFechas.style.display = 'flex';
            botonFiltro.style.backgroundColor = '#4a7ba7';
        } else {
            contenedorFechas.style.display = 'none';
            botonFiltro.style.backgroundColor = '#3d5a6c';
        }
    });
    
    // Aplicar fechas
    botonAplicar.addEventListener('click', function() {
        const fechaInicio = document.getElementById('fecha-inicio').value;
        const fechaFin = document.getElementById('fecha-fin').value;
        
        if (!fechaInicio || !fechaFin) {
            alert('Por favor selecciona ambas fechas');
            return;
        }
        
        if (fechaInicio > fechaFin) {
            alert('La fecha de inicio no puede ser posterior a la fecha de fin');
            return;
        }
        
        // Obtener parámetros actuales
        const urlParams = new URLSearchParams(window.location.search);
        const modo = urlParams.get('modo') || 'familiar';
        
        // Redirigir con las nuevas fechas
        window.location.href = `UI-05_Balance.php?modo=${modo}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    });
    
    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (!botonFiltro.contains(event.target) && !contenedorFechas.contains(event.target)) {
            if (contenedorFechas.style.display === 'flex') {
                contenedorFechas.style.display = 'none';
                botonFiltro.style.backgroundColor = '#3d5a6c';
            }
        }
    });
});