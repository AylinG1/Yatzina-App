document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formLogin");
    const msg = document.getElementById("mensaje");

    if (!form) {
        console.error("Formulario de login no encontrado.");
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const credencialVal = document.getElementById("credencial").value.trim();
        const contrasenaVal = document.getElementById("contrasena").value;

        if (!credencialVal || !contrasenaVal) {
            msg.textContent = 'Por favor, ingresa tu usuario/correo y contraseña.';
            msg.style.color = 'red';
            return;
        }

        const datos = {
            credencial: credencialVal,
            contrasena: contrasenaVal
        };

        // Usa la ruta relativa o la ruta completa si tu proyecto está bajo un subdirectorio en XAMPP
        const fetchUrl = "backend/iniciarsesion.php"; 

        msg.textContent = "Verificando credenciales... ⏳";
        msg.style.color = "blue";
        
        try {
            const respuesta = await fetch(fetchUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            // Leer la respuesta como texto primero para manejar errores no-JSON
            const responseText = await respuesta.text();
            
            if (!respuesta.ok) {
                // Si el servidor respondió con 4xx o 5xx, mostrar error genérico
                throw new Error("Error del servidor: " + respuesta.status);
            }

            const resultado = JSON.parse(responseText);

            if (resultado.status === "ok") {
                msg.textContent = resultado.msg;
                msg.style.color = "green";
                form.reset();
                
                // Redirigir a la página especificada según el rol (selector_rol.html, interfaz_alumno.html, interfaz_maestro.html)
                const redirectTo = resultado.redirect_to || 'index.html';
                setTimeout(() => {
                    window.location.replace(redirectTo);
                }, 1000);

            } else {
                msg.textContent = "Error: " + resultado.msg;
                msg.style.color = "red";
            }
        } catch (error) {
            // Maneja errores de red o parseo JSON
            msg.textContent = "Error de red o respuesta inválida: " + error.message;
            msg.style.color = "red";
        }
    });
});