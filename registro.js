document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formRegistro");
    const usuarioInput = document.getElementById("usuario");
    const correoInput = document.getElementById("correo");
    const contrasenaInput = document.getElementById("contrasena");
    const confirmarContrasenaInput = document.getElementById("confirmar_contrasena");
    const msg = document.getElementById("mensaje");
    const msgUsuario = document.getElementById("msg_usuario");
    const msgCorreo = document.getElementById("msg_correo");
    const msgContrasena = document.getElementById("msg_contrasena");

    if (!form) {
        console.error("Formulario de registro no encontrado.");
        return;
    }

    let usuarioDisponible = false;
    let correoDisponible = false;
    let debounceTimers = {};

    // Helpers para aplicar estilos de validación inline (más seguros que depender solo de clases)
    function setValid(el) {
        if (!el) return;
        el.style.borderColor = '#10b981';
        el.style.boxShadow = '0 0 0 3px rgba(16,185,129,0.08)';
    }

    function setInvalid(el) {
        if (!el) return;
        el.style.borderColor = '#ef4444';
        el.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.08)';
    }

    function clearValidation(el) {
        if (!el) return;
        el.style.borderColor = '';
        el.style.boxShadow = '';
    }
    // Función para verificar disponibilidad con debounce
    const verificarDisponibilidad = (tipo, valor, inputElement) => {
        if (debounceTimers[tipo]) clearTimeout(debounceTimers[tipo]);

        if (valor.length === 0) {
            clearValidation(inputElement);
            if (tipo === "usuario" && msgUsuario) msgUsuario.textContent = "";
            if (tipo === "correo" && msgCorreo) msgCorreo.textContent = "";
            // mark availability false when empty
            if (tipo === "usuario") usuarioDisponible = false;
            if (tipo === "correo") correoDisponible = false;
            return;
        }

        debounceTimers[tipo] = setTimeout(async () => {
            try {
                const respuesta = await fetch("backend/verificar_disponibilidad.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ tipo: tipo, valor: valor })
                });

                const resultado = await respuesta.json();

                if (resultado.status === "disponible") {
                    clearValidation(inputElement);
                    setValid(inputElement);

                    // Clear field-specific message
                    if (tipo === "usuario") {
                        usuarioDisponible = true;
                        if (msgUsuario) { msgUsuario.textContent = ""; }
                    } else if (tipo === "correo") {
                        correoDisponible = true;
                        if (msgCorreo) { msgCorreo.textContent = ""; }
                    }
                } else if (resultado.status === "existe") {
                    clearValidation(inputElement);
                    setInvalid(inputElement);

                    if (tipo === "usuario") {
                        usuarioDisponible = false;
                        if (msgUsuario) { msgUsuario.textContent = "❌ " + resultado.msg + ". Por favor, elige otro."; }
                    } else if (tipo === "correo") {
                        correoDisponible = false;
                        if (msgCorreo) { msgCorreo.textContent = "❌ " + resultado.msg + ". Por favor, usa otro."; }
                    }
                }
            } catch (error) {
                console.error("Error al verificar disponibilidad:", error);
            }
        }, 500); // Espera 500ms después de que el usuario deja de escribir
    };

    // Validación de usuario
    usuarioInput.addEventListener("input", () => {
        const usuario = usuarioInput.value.trim();
        if (usuario.length >= 3) {
            verificarDisponibilidad("usuario", usuario, usuarioInput);
        } else if (usuario.length > 0) {
            clearValidation(usuarioInput);
            setInvalid(usuarioInput);
            if (msgUsuario) { msgUsuario.textContent = "❌ El nombre de usuario debe tener al menos 3 caracteres."; }
        } else {
            clearValidation(usuarioInput);
            if (msgUsuario) { msgUsuario.textContent = ""; }
        }
    });

    // Validación de correo
    correoInput.addEventListener("input", () => {
        const correo = correoInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (correo.length > 0 && emailRegex.test(correo)) {
            verificarDisponibilidad("correo", correo, correoInput);
        } else if (correo.length > 0) {
            clearValidation(correoInput);
            setInvalid(correoInput);
            if (msgCorreo) { msgCorreo.textContent = "❌ Por favor, ingresa un correo electrónico válido."; }
        } else {
            clearValidation(correoInput);
            if (msgCorreo) { msgCorreo.textContent = ""; }
        }
    });

    // Validación en tiempo real: Contraseñas coinciden
    confirmarContrasenaInput.addEventListener("input", () => {
        if (confirmarContrasenaInput.value && contrasenaInput.value !== confirmarContrasenaInput.value) {
            setInvalid(confirmarContrasenaInput);
            setInvalid(contrasenaInput);
            if (msgContrasena) { msgContrasena.textContent = "❌ Las contraseñas no coinciden"; msgContrasena.style.color = 'red'; }
        } else if (confirmarContrasenaInput.value === contrasenaInput.value && contrasenaInput.value !== "") {
            setValid(confirmarContrasenaInput);
            setValid(contrasenaInput);
            if (msgContrasena) { msgContrasena.textContent = "✅ Las contraseñas coinciden"; msgContrasena.style.color = 'green'; }
        } else {
            clearValidation(confirmarContrasenaInput);
            clearValidation(contrasenaInput);
            if (msgContrasena) { msgContrasena.textContent = ""; }
        }
    });

    contrasenaInput.addEventListener("input", () => {
        if (confirmarContrasenaInput.value) {
            if (contrasenaInput.value !== confirmarContrasenaInput.value) {
                setInvalid(confirmarContrasenaInput);
                setInvalid(contrasenaInput);
                if (msgContrasena) { msgContrasena.textContent = "❌ Las contraseñas no coinciden"; msgContrasena.style.color = 'red'; }
            } else {
                setValid(confirmarContrasenaInput);
                setValid(contrasenaInput);
                if (msgContrasena) { msgContrasena.textContent = "✅ Las contraseñas coinciden"; msgContrasena.style.color = 'green'; }
            }
        }
    });

    // Envío del formulario
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const usuario = usuarioInput.value.trim();
        const correo = correoInput.value.trim();
        const contrasena = contrasenaInput.value;
        const confirmarContrasena = confirmarContrasenaInput.value;

        // Validaciones básicas
        if (!usuario || !correo || !contrasena || !confirmarContrasena) {
            msg.textContent = "❌ Por favor, completa todos los campos.";
            msg.style.color = "red";
            return;
        }

        // Validar disponibilidad
        if (!usuarioDisponible) {
            msg.textContent = "❌ El nombre de usuario no está disponible.";
            msg.style.color = "red";
            setInvalid(usuarioInput);
            usuarioInput.focus();
            return;
        }

        if (!correoDisponible) {
            msg.textContent = "❌ El correo electrónico no está disponible.";
            msg.style.color = "red";
            setInvalid(correoInput);
            correoInput.focus();
            return;
        }

        // Validar que las contraseñas coincidan
        if (contrasena !== confirmarContrasena) {
            msg.textContent = "❌ Las contraseñas no coinciden. Por favor, verifica.";
            msg.style.color = "red";
            contrasenaInput.focus();
            return;
        }

        // Validar longitud mínima de contraseña
        if (contrasena.length < 8) {
            msg.textContent = "❌ La contraseña debe tener al menos 8 caracteres.";
            msg.style.color = "red";
            return;
        }

        // Validar formato de correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            msg.textContent = "❌ Por favor, ingresa un correo electrónico válido.";
            msg.style.color = "red";
            return;
        }

        // Validar longitud del usuario
        if (usuario.length < 3) {
            msg.textContent = "❌ El nombre de usuario debe tener al menos 3 caracteres.";
            msg.style.color = "red";
            return;
        }

        // Preparar datos
        const datos = {
            usuario: usuario,
            correo: correo,
            contrasena: contrasena
        };

        msg.textContent = "⏳ Creando tu cuenta...";
        msg.style.color = "blue";

        try {
            const respuesta = await fetch("backend/registrar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.status === "ok") {
                msg.textContent = "✅ " + resultado.msg;
                msg.style.color = "green";
                form.reset();
                
                // Limpiar estilos inline de validación
                clearValidation(usuarioInput);
                clearValidation(correoInput);
                clearValidation(contrasenaInput);
                clearValidation(confirmarContrasenaInput);

                // Redirigir después de 2 segundos
                setTimeout(() => {
                    const redirectTo = resultado.redirect_to || 'iniciarsesion.html';
                    window.location.replace(redirectTo);
                }, 2000);
            } else {
                msg.textContent = "❌ " + resultado.msg;
                msg.style.color = "red";
            }
        } catch (error) {
            msg.textContent = "❌ Error al crear la cuenta. Por favor, intenta nuevamente.";
            msg.style.color = "red";
            console.error("Error:", error);
        }
    });
});
