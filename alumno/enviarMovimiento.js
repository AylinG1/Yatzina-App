// Script de ejemplo para que las páginas de alumno envíen movimientos al servidor.
async function enviarMovimiento(payload) {
    // payload: { alumno_id, tipo, detalle, pagina }
    try {
        const res = await fetch('/backend/guardar_movimiento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (!res.ok) {
            console.error('Error al enviar movimiento', res.status);
            return null;
        }
        const data = await res.json();
        if (data.status === 'ok') {
            return data.id;
        }
        console.error('Respuesta de error', data);
        return null;
    } catch (err) {
        console.error('Error de conexión al enviar movimiento', err);
        return null;
    }
}

// Ejemplo de uso en una página de alumno:
// enviarMovimiento({ alumno_id: 'A123', tipo: 'avance_leccion', detalle: 'completó ejercicio 2', pagina: window.location.pathname });
