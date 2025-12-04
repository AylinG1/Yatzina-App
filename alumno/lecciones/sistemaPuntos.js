/**
 * Sistema de Puntos para Lecciones
 * Este script maneja el registro automático de puntos cuando los alumnos completan lecciones
 */

async function registrarPuntosLeccion(nombreLeccion, puntos = 100) {
    try {
        const response = await fetch('../../backend/calcularPuntos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                tipo: 'leccion',
                id_referencia: nombreLeccion,
                puntos: puntos
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('✅ Puntos registrados:', data.puntos_ganados);
            console.log('📊 Puntos totales:', data.puntos_totales);
            
            // Disparar evento personalizado para que otras partes de la página se actualicen
            const event = new CustomEvent('puntosRegistrados', {
                detail: {
                    puntos_ganados: data.puntos_ganados,
                    puntos_totales: data.puntos_totales,
                    tipo: 'leccion',
                    nombre: nombreLeccion
                }
            });
            document.dispatchEvent(event);
            
            return data;
        } else {
            // Si ya existen puntos, no es un error
            if (data.message.includes('Ya obtuviste')) {
                console.log('⚠️', data.message);
                return { success: false, alreadyEarned: true };
            }
            console.error('❌ Error:', data.message);
            return data;
        }
    } catch (error) {
        console.error('❌ Error registrando puntos:', error);
        return { success: false, error: error.message };
    }
}

// Escuchar evento de puntos registrados para actualizar la interfaz
document.addEventListener('puntosRegistrados', (event) => {
    const { puntos_ganados, puntos_totales, tipo } = event.detail;
    console.log(`🎉 ${tipo === 'leccion' ? 'Lección completada' : 'Insignia obtenida'}!`);
    console.log(`Puntos ganados: ${puntos_ganados}, Total: ${puntos_totales}`);
});
