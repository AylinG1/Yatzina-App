/**
 * Sistema de Notificaciones de Puntos con Animación de Confeti
 * Muestra una ventana emergente cuando se registran puntos
 */

// Crear estilos para notificaciones
const estilosNotificacion = document.createElement('style');
estilosNotificacion.textContent = `
    .notificacion-puntos {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 60px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
        z-index: 9999;
        text-align: center;
        animation: popIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .notificacion-puntos h2 {
        font-size: 32px;
        margin: 0 0 15px 0;
        font-weight: 700;
    }

    .notificacion-puntos .puntos-cantidad {
        font-size: 48px;
        font-weight: 800;
        color: #ffd700;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        margin: 10px 0;
        animation: bounce 0.6s ease-in-out 0.3s;
    }

    .notificacion-puntos .puntos-texto {
        font-size: 18px;
        margin-top: 15px;
        opacity: 0.95;
    }

    .notificacion-puntos .emoji {
        font-size: 54px;
        margin-bottom: 10px;
        display: inline-block;
        animation: spin 1s ease-in-out;
    }

    @keyframes popIn {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    @keyframes bounce {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.15);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(-15deg);
        }
        50% {
            transform: rotate(15deg);
        }
        100% {
            transform: rotate(0deg);
        }
    }

    .confeti {
        position: fixed;
        width: 10px;
        height: 10px;
        pointer-events: none;
        z-index: 9998;
    }

    @keyframes caer {
        to {
            transform: translateY(100vh) rotateZ(360deg);
            opacity: 0;
        }
    }

    .overlay-oscuro {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 9998;
        animation: aparecer 0.3s ease;
    }

    @keyframes aparecer {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
`;
document.head.appendChild(estilosNotificacion);

/**
 * Mostrar notificación de puntos con confeti
 * @param {number} puntos - Cantidad de puntos ganados
 * @param {string} tipo - Tipo de logro ('leccion' o 'insignia')
 * @param {string} nombre - Nombre del logro (opcional)
 */
function mostrarNotificacionPuntos(puntos, tipo = 'leccion', nombre = '') {
    // Crear overlay oscuro
    const overlay = document.createElement('div');
    overlay.className = 'overlay-oscuro';
    document.body.appendChild(overlay);

    // Crear notificación
    const notificacion = document.createElement('div');
    notificacion.className = 'notificacion-puntos';
    
    let emoji = '🎉';
    let titulo = '¡Felicidades!';
    
    if (tipo === 'insignia') {
        emoji = '🏆';
        titulo = '¡Insignia Desbloqueada!';
    } else if (tipo === 'leccion') {
        emoji = '🎓';
        titulo = '¡Lección Completada!';
    }

    notificacion.innerHTML = `
        <div class="emoji">${emoji}</div>
        <h2>${titulo}</h2>
        <div class="puntos-cantidad">+${puntos} puntos</div>
        <div class="puntos-texto">${nombre ? `"${nombre}"` : ''}</div>
    `;
    
    document.body.appendChild(notificacion);

    // Generar confeti
    generarConfeti();

    // Reproducir sonido (opcional)
    reproducirSonido();

    // Eliminar notificación después de 3 segundos
    setTimeout(() => {
        notificacion.style.animation = 'popIn 0.5s ease-out reverse';
        overlay.style.animation = 'aparecer 0.3s ease reverse';
        
        setTimeout(() => {
            notificacion.remove();
            overlay.remove();
        }, 500);
    }, 3000);
}

/**
 * Generar partículas de confeti
 */
function generarConfeti() {
    const colores = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8'];
    const cantidad = 50;

    for (let i = 0; i < cantidad; i++) {
        const confeti = document.createElement('div');
        confeti.className = 'confeti';
        confeti.style.left = Math.random() * window.innerWidth + 'px';
        confeti.style.top = '-10px';
        confeti.style.backgroundColor = colores[Math.floor(Math.random() * colores.length)];
        confeti.style.animation = `caer ${2 + Math.random() * 1}s ease-in forwards`;
        confeti.style.animationDelay = Math.random() * 0.2 + 's';
        confeti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0%';
        
        document.body.appendChild(confeti);

        // Eliminar confeti del DOM después de la animación
        setTimeout(() => {
            confeti.remove();
        }, 3200);
    }
}

/**
 * Reproducir sonido de éxito (opcional)
 */
function reproducirSonido() {
    try {
        // Crear un sonido simple usando Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscilador = audioContext.createOscillator();
        const ganancia = audioContext.createGain();
        
        oscilador.connect(ganancia);
        ganancia.connect(audioContext.destination);
        
        // Frecuencias para crear una melodía simple
        const notas = [523.25, 659.25, 783.99]; // Do, Mi, Sol
        const tiempoInicio = audioContext.currentTime;
        
        notas.forEach((frecuencia, index) => {
            const tiempo = tiempoInicio + index * 0.1;
            oscilador.frequency.setValueAtTime(frecuencia, tiempo);
            ganancia.gain.setValueAtTime(0.3, tiempo);
            ganancia.gain.exponentialRampToValueAtTime(0.01, tiempo + 0.08);
        });
        
        oscilador.start(tiempoInicio);
        oscilador.stop(tiempoInicio + 0.3);
    } catch (e) {
        // Silenciosamente fallar si no hay soporte de audio
    }
}

// Escuchar evento de puntos registrados desde sistemaPuntos.js
document.addEventListener('puntosRegistrados', (event) => {
    const { puntos_ganados, tipo, nombre } = event.detail;
    
    // Mostrar notificación con confeti
    mostrarNotificacionPuntos(puntos_ganados, tipo, nombre);
});
