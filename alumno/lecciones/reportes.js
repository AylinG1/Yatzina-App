document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('reportesTbody');
    const searchInput = document.getElementById('searchInput');

    let alumnos = [];

    function formatTime(isoString) {
        if (!isoString) return '-';
        const dt = new Date(isoString);
        return dt.toLocaleString();
    }

    function estadoPorFecha(isoString) {
        if (!isoString) return 'Desconocido';
        const dt = new Date(isoString);
        const diff = (Date.now() - dt.getTime()) / 1000; // segundos
        if (diff < 60 * 10) return 'Activo'; // 10 minutos
        if (diff < 60 * 60) return 'Reciente';
        return 'Desconectado';
    }

    function render() {
        const filter = (searchInput.value || '').toLowerCase();
        tbody.innerHTML = '';
        const filtered = alumnos.filter(a => {
            if (!filter) return true;
            return (a.nombreCompleto && a.nombreCompleto.toLowerCase().includes(filter)) || (a.id && String(a.id).includes(filter));
        });

        if (filtered.length === 0) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors';
            tr.innerHTML = '<td class="px-6 py-5" colspan="7" style="text-align:center;color:#666">No se encontraron alumnos</td>';
            tbody.appendChild(tr);
            return;
        }

        filtered.forEach(a => {
            const last = a.lastMovimiento || {};
            const hora = last.created_at ? formatTime(last.created_at) : '-';
            const estado = last.created_at ? estadoPorFecha(last.created_at) : 'Desconocido';
            const pagina = last.pagina || (last.detalle ? last.detalle : '-');

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors';
            tr.innerHTML = `
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center overflow-hidden">
                            <img alt="Profile" src="/alumno/../../assets/default-avatar.png" onerror="this.style.display='none'" />
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">${escapeHtml(a.nombreCompleto || ('Usuario ' + a.id))}</p>
                            <p class="text-xs text-slate-500 italic">ID: ${escapeHtml(a.id)}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-xs font-bold">Estudiante</span>
                </td>
                <td class="px-6 py-5">
                    <div class="text-sm">
                        <p class="text-slate-900 dark:text-slate-200">${escapeHtml(hora)}</p>
                        <p class="text-slate-400 text-xs">${escapeHtml(last.tipo || '')}</p>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full ${estado === 'Activo' ? 'bg-primary animate-pulse' : 'bg-slate-300 dark:bg-slate-700'}"></div>
                        <span class="text-sm font-medium ${estado === 'Activo' ? 'text-primary' : 'text-slate-500'}">${escapeHtml(estado)}</span>
                    </div>
                </td>
                <td class="px-6 py-5 font-mono text-xs text-slate-500">-</td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <span class="material-symbols-outlined text-sm text-primary">auto_stories</span>
                        ${escapeHtml(pagina)}
                    </div>
                </td>
                <td class="px-6 py-5">
                    <button data-id="${escapeHtml(a.id)}" class="p-2 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors btnVerDetalle">
                        <span class="material-symbols-outlined text-slate-400">more_horiz</span>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        });

        // attach detail button handlers
        document.querySelectorAll('.btnVerDetalle').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                const id = ev.currentTarget.getAttribute('data-id');
                // Abrir la bitácora del maestro en nueva pestaña y pasar id en query
                window.open(`/maestro/bitacora.html?alumno_id=${encodeURIComponent(id)}`, '_blank');
            });
        });
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"]/g, function(m) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]); });
    }

    async function loadAlumnos() {
        try {
            const res = await fetch('/backend/obtenerAlumnosMaestro.php');
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Error al obtener alumnos');

            alumnos = data.alumnos || [];

            // For each alumno, fetch last movimiento
            await Promise.all(alumnos.map(async (a) => {
                try {
                    const r = await fetch(`/backend/obtener_movimientos.php?alumno_id=${encodeURIComponent(a.id)}&limit=1`);
                    if (!r.ok) return a.lastMovimiento = null;
                    const jr = await r.json();
                    if (jr.status === 'ok' && jr.data && jr.data.length > 0) {
                        a.lastMovimiento = jr.data[0];
                    } else {
                        a.lastMovimiento = null;
                    }
                } catch (err) {
                    a.lastMovimiento = null;
                }
            }));

            render();
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-5">Error: ${escapeHtml(err.message)}</td></tr>`;
        }
    }

    searchInput.addEventListener('input', () => render());

    loadAlumnos();
});
