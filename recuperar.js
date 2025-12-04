document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formRecuperar");
    const msg = document.getElementById("mensaje");
    const emailInput = document.getElementById("emailRecuperar");

    if (!form) {
        console.error("Formulario de recuperación no encontrado.");
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();

        if (!email) {
            msg.textContent = "Por favor, ingresa tu correo electrónico.";
            msg.style.color = "red";
            return;
        }

        // Validar formato de correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            msg.textContent = "Por favor, ingresa un correo electrónico válido.";
            msg.style.color = "red";
            return;
        }

        msg.textContent = "Enviando correo... ⏳";
        msg.style.color = "#2bee4b";

        try {
            const respuesta = await fetch("backend/recuperar_contrasena.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email: email })
            });

            const resultado = await respuesta.json();

            if (resultado.status === "ok") {
                msg.textContent = "✅ " + resultado.msg;
                msg.style.color = "#2bee4b";
                form.reset();

                // Opcional: Redirigir al login después de 5 segundos
                setTimeout(() => {
                    window.location.href = "iniciarsesion.html";
                }, 5000);
            } else {
                msg.textContent = "❌ " + resultado.msg;
                msg.style.color = "#ef4444";
            }
        } catch (error) {
            msg.textContent = "❌ Error al procesar la solicitud. Por favor, intenta nuevamente.";
            msg.style.color = "#ef4444";
            console.error("Error:", error);
        }
    });
});
