console.log("Yatzina cargado correctamente :)");
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formRegistro");
    const msg = document.getElementById("mensaje");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const usuarioVal = document.getElementById("usuario").value.trim();
        const correoVal = document.getElementById("correo").value.trim();
        const contraseñaVal = document.getElementById("contraseña").value;

        // Normalizar y pasar a minúsculas (eliminar diacríticos para comparaciones)
        const normalizeLowerSafe = (s) => {
            try {
                return String(s).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            } catch (err) {
                return String(s).toLowerCase();
            }
        };

        const forbidden = ['tonto', 'feo', 'malo', 'estúpido'];
        const forbiddenNorm = forbidden.map(w => normalizeLowerSafe(w));

        if (!usuarioVal) {
            msg.textContent = 'El usuario es obligatorio.';
            msg.style.color = 'red';
            return;
        }

        const usuarioNorm = normalizeLowerSafe(usuarioVal);
        if (forbiddenNorm.includes(usuarioNorm)) {
            msg.textContent = 'El nombre de usuario no puede contener palabras ofensivas.';
            msg.style.color = 'red';
            return;
        }

        // Validación básica de correo
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(correoVal)) {
            msg.textContent = 'Introduce un correo electrónico válido.';
            msg.style.color = 'red';
            return;
        }

        // Contraseña: al menos 8 caracteres, al menos una letra y un número
        const passwordPattern = /(?=.*[A-Za-z])(?=.*\d).{8,}/;
        if (!passwordPattern.test(contraseñaVal)) {
            msg.textContent = 'La contraseña debe tener al menos 8 caracteres, incluyendo una letra y un número.';
            msg.style.color = 'red';
            return;
        }

        const datos = {
            usuario: usuarioVal,
            correo: correoVal,
            contraseña: contraseñaVal
        };

        let resultado = { status: 'error', msg: 'No se ejecutó la solicitud' };
        try {
            // Usar ruta relativa al backend. Ajusta si tu servidor expone otra ruta/host.
            const respuesta = await fetch("backend/registrar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            // Si la respuesta no es 2xx, leer texto para diagnosticar
            const text = await respuesta.text();
            try {
                resultado = JSON.parse(text);
            } catch (err) {
                // No llegó JSON — mostrar texto crudo para depuración
                resultado = { status: 'error', msg: 'Respuesta no JSON: ' + text };
            }
        } catch (err) {
            resultado = { status: 'error', msg: 'Error de red: ' + err.message };
        }

        if (resultado.status === "ok") {
            // Ocultar mensaje global
            msg.style.display = 'none';

            // Mostrar modal de éxito
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.classList.remove('hidden');
            }

            // Asegurar que el botón de cerrar oculta el modal y redirige
            const closeBtn = document.getElementById('closeModalBtn');
            if (closeBtn) {
                // Reemplaza cualquier listener anterior y maneja cierre + redirección
                closeBtn.onclick = (ev) => {
                    if (successModal) successModal.classList.add('hidden');
                    // Redirigir a la página especificada (selector_rol.html después del registro)
                    const redirectTo = resultado.redirect_to || 'selector_rol.html';
                    window.location.replace(redirectTo);
                };
            }

            form.reset();
        } else {
            msg.textContent = "Error: " + resultado.msg;
            msg.style.color = "red";
            msg.style.display = '';
        }
    });
});
