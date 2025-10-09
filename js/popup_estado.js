document.addEventListener('DOMContentLoaded', () => {
    let conceptoIdActual = null;
    let estadoActual = null;
    const btnSi = document.getElementById('btnSi');
    const btnNo = document.getElementById('btnNo');
    const modal = document.getElementById('modalConfirmar');

    // Función para abrir el modal
    window.abrirModal = function(id, estado) {
    conceptoIdActual = id;
    estadoActual = estado; // '1' o '0'
    modal.style.display = 'block';
    };

    btnSi.onclick = () => {
        const nuevoEstado = estadoActual === '1' ? 0 : 1;

        fetch('../ui/UI-16_VisualizarConceptos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_concepto=${conceptoIdActual}&estado=${nuevoEstado}&ajax=1`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                modal.style.display = 'none';
                location.reload();
            }
        });
    };

    // Botón "No": cerrar modal sin cambios
    btnNo.onclick = () => {
        modal.style.display = 'none';
    };
});
