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
        let param_name = '';
        switch(tipoEntidad) {
            case 'concepto':
                url = '../ui/UI-16_VisualizarConceptos.php';
                param_name = 'concepto_id'
                break;
            case 'usuario':
                url = '../ui/UI-12_VisualizarUsuarios.php';
                param_name = 'id_usuario'
                break;
            case 'categoria':
                url = '../ui/UI-20_VisualizarCategoria.php';
                param_name = 'idcategoria'
                break;
        }

        console.log("ID:", entidadIdActual);
        console.log("n_estado:", nuevoEstado);
        console.log("Tipo:", param_name);

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `${param_name}=${entidadIdActual}&estado=${nuevoEstado}&ajax=1`
        })
        .then(res => res.text())
        .then(data => console.log(data))
        .then(data => {
            if(data.success) {
                modal.style.display = 'none';
                location.reload();
            }
        });
    };


    console.log("ID:ASDASDSA");
    

    btnNo.onclick = () => {
        modal.style.display = 'none';
    };
});
