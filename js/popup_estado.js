document.addEventListener('DOMContentLoaded', () => {
    let entidadIdActual = null;
    let estadoActual = null;
    let tipoEntidad = null; // 'concepto', 'usuario', 'categoria'
    
    const btnSi = document.getElementById('btnSi');
    const btnNo = document.getElementById('btnNo');
    const modal = document.getElementById('modalConfirmar');

    // Función genérica para abrir el modal
    window.abrirModal = function(id, estado, tipo) {
        entidadIdActual = id;
        estadoActual = estado; // '1' o '0'
        tipoEntidad = tipo;    // 'concepto', 'usuario', 'categoria'
        modal.style.display = 'block';
    };

    btnSi.onclick = () => {
        const nuevoEstado = estadoActual === '1' ? 0 : 1;

        // Determinar URL según tipo de entidad
        let url = '';
        switch(tipoEntidad) {
            case 'concepto':
                url = '../ui/UI-16_VisualizarConceptos.php';
                break;
            case 'usuario':
                url = '../ui/UI-12_VisualizarUsuarios.php';
                break;
            case 'categoria':
                url = '../ui/UI-20_VisualizarCategoria.php';
                break;
        }

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${entidadIdActual}&estado=${nuevoEstado}&ajax=1`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                modal.style.display = 'none';
                location.reload();
            }
        });
    };

    btnNo.onclick = () => {
        modal.style.display = 'none';
    };
});
