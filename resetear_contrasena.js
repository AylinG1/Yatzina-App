document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formResetearContrasena");
    const msg = document.getElementById("mensaje");
    const nuevaContrasenaInput = document.getElementById("nuevaContrasena");
    const confirmarContrasenaInput = document.getElementById("confirmarContrasena");

    // Obtener el token de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    // Verificar si hay un token en la URL
    if (!token) {
        msg.textContent = "Enlace inválido. No se proporcionó un token de restablecimiento.";
        msg.style.color = "red";
        form.querySelector('button[type="submit"]').disabled = true;
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nuevaContrasena = nuevaContrasenaInput.value.trim();
        const confirmarContrasena = confirmarContrasenaInput.value.trim();

        // Validar que ambos campos estén llenos
        if (!nuevaContrasena || !confirmarContrasena) {
            msg.textContent = "Por favor, completa todos los campos.";
            msg.style.color = "red";
            return;
        }

        // Validar longitud mínima
        if (nuevaContrasena.length < 8) {
            msg.textContent = "La contraseña debe tener al menos 8 caracteres.";
            msg.style.color = "red";
            return;
        }

        // Validar que las contraseñas coincidan
        if (nuevaContrasena !== confirmarContrasena) {
            msg.textContent = "Las contraseñas no coinciden.";
            msg.style.color = "red";
            return;
        }

        // Preparar los datos
        const datos = {
            token: token,
            nueva_contrasena: nuevaContrasena
        };

        msg.textContent = "Actualizando contraseña... ⏳";
        msg.style.color = "blue";

        try {
            const respuesta = await fetch("backend/procesar_reseteo.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.status === "ok") {
                msg.textContent = resultado.msg;
                msg.style.color = "green";
                form.reset();

                // Redirigir al login después de 3 segundos
                setTimeout(() => {
                    window.location.href = "iniciarsesion.html";
                }, 3000);
            } else {
                msg.textContent = resultado.msg;
                msg.style.color = "red";
            }
        } catch (error) {
            msg.textContent = "Error al procesar la solicitud: " + error.message;
            msg.style.color = "red";
        }
    });

    // Validación en tiempo real de coincidencia de contraseñas
    confirmarContrasenaInput.addEventListener("input", () => {
        if (confirmarContrasenaInput.value && nuevaContrasenaInput.value !== confirmarContrasenaInput.value) {
            confirmarContrasenaInput.setCustomValidity("Las contraseñas no coinciden");
        } else {
            confirmarContrasenaInput.setCustomValidity("");
        }
    });

    nuevaContrasenaInput.addEventListener("input", () => {
        if (confirmarContrasenaInput.value && nuevaContrasenaInput.value !== confirmarContrasenaInput.value) {
            confirmarContrasenaInput.setCustomValidity("Las contraseñas no coinciden");
        } else {
            confirmarContrasenaInput.setCustomValidity("");
        }
    });
});
