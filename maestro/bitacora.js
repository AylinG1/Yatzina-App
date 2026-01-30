(() => {
    const mensaje = document.getElementById('mensaje');
    const filtroAlumno = document.getElementById('filtroAlumno');
    const btnFiltrar = document.getElementById('btnFiltrar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const intervalSelect = document.getElementById('intervalSelect');
    const tbody = document.querySelector('#logTable tbody');

    let intervaloId = null;

    function renderRows(rows) {
        tbody.innerHTML = '';
        if (!rows || rows.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="5" style="text-align:center;color:#666;">No hay movimientos</td>';
            tbody.appendChild(tr);
            return;
        }
        rows.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${r.created_at}</td><td>${escapeHtml(r.alumno_id)}</td><td>${escapeHtml(r.tipo)}</td><td>${escapeHtml(r.detalle)}</td><td>${escapeHtml(r.pagina)}</td>`;
            tbody.appendChild(tr);
        });
    }

    function escapeHtml(str){
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"]+/g, function(match){
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[match]);
        });
    }

    async function fetchMovs(){
        const alumno = filtroAlumno.value.trim();
        let url = '/backend/obtener_movimientos.php?limit=200';
        if (alumno) url += '&alumno_id=' + encodeURIComponent(alumno);
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Error HTTP ' + res.status);
            const data = await res.json();
            if (data.status === 'ok') {
                renderRows(data.data);
                mensaje.textContent = '';
            } else {
                mensaje.textContent = data.msg || 'Error al obtener datos';
            }
        } catch (err) {
            mensaje.textContent = 'Error de conexión: ' + err.message;
        }
    }

    function startPolling(){
        stopPolling();
        const s = parseInt(intervalSelect.value, 10) * 1000;
        fetchMovs();
        intervaloId = setInterval(fetchMovs, s);
    }

    function stopPolling(){
        if (intervaloId) {
            clearInterval(intervaloId);
            intervaloId = null;
        }
    }

    btnFiltrar.addEventListener('click', () => { startPolling(); });
    btnLimpiar.addEventListener('click', () => { filtroAlumno.value = ''; startPolling(); });
    intervalSelect.addEventListener('change', () => { startPolling(); });

    // Si viene alumno_id en la URL, precargar el filtro
    try {
        const params = new URLSearchParams(window.location.search);
        const aid = params.get('alumno_id');
        if (aid) filtroAlumno.value = aid;
    } catch (e) {
        // no hacer nada si URLSearchParams falla
    }

    // inicio
    startPolling();
})();
