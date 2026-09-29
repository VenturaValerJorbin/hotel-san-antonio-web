// JavaScript de la interfaz. La validacion real esta en el servidor (PHP);
// aqui solo se mejora la experiencia: resumen de la reserva en vivo y copiar la direccion.
(function () {
    "use strict";

    // ---- Formulario de reserva: resumen de pago en vivo ----
    const form = document.getElementById("formReserva");
    if (form) {
        const $ = (id) => document.getElementById(id);
        const soles = (n) => "S/ " + n.toFixed(2);
        const porcentaje = parseFloat(form.dataset.porcentaje || "50");

        const noches = () => {
            const dias = (new Date($("fecha_salida").value) - new Date($("fecha_ingreso").value)) / 86400000;
            return dias > 0 ? dias : 0;
        };

        const actualizar = () => {
            const opcion = $("tipo_id").selectedOptions[0];
            const precio = parseFloat(opcion?.dataset.precio || 0);
            const n = noches();
            const total = precio * n;
            const fraccionado = form.querySelector("input[name=modalidad_pago]:checked")?.value === "fraccionado";
            const ahora = fraccionado ? Math.round(total * porcentaje) / 100 : total;

            $("r_habitacion").textContent = opcion?.value ? opcion.dataset.nombre : "—";
            $("r_piso").textContent = $("piso").value !== "0" ? "Piso " + $("piso").value : "Sin preferencia";
            $("r_ingreso").textContent = $("fecha_ingreso").value || "—";
            $("r_salida").textContent = $("fecha_salida").value || "—";
            $("r_noches").textContent = n ? n + (n === 1 ? " noche" : " noches") : "—";
            $("r_tarifa").textContent = soles(precio);
            $("r_total").textContent = soles(total);
            $("r_ahora_txt").textContent = fraccionado ? "Pagas ahora (" + porcentaje + " %)" : "Pagas ahora (100 %)";
            $("r_ahora").textContent = soles(ahora);
            $("r_saldo").textContent = soles(total - ahora);
        };

        // La salida debe ser al menos un dia despues de la llegada
        const limitarSalida = () => {
            const ingreso = $("fecha_ingreso").value;
            if (!ingreso) return;
            const minimo = new Date(ingreso);
            minimo.setDate(minimo.getDate() + 1);
            $("fecha_salida").min = minimo.toISOString().slice(0, 10);
        };

        // Al cambiar de habitacion, el selector de piso ofrece solo los pisos donde existe ese tipo
        // (los pisos vienen del servidor en data-pisos de cada opcion, ej. "1,3")
        $("tipo_id").addEventListener("change", () => {
            const pisos = ($("tipo_id").selectedOptions[0]?.dataset.pisos || "").split(",").filter(Boolean);
            const actual = $("piso").value;
            $("piso").innerHTML = '<option value="0">Sin preferencia</option>'
                + pisos.map((p) => '<option value="' + p + '">Piso ' + p + "</option>").join("");
            $("piso").value = pisos.includes(actual) ? actual : "0";
        });

        form.addEventListener("input", () => { limitarSalida(); actualizar(); });
        form.addEventListener("change", actualizar);
        limitarSalida();
        actualizar();

        // ---- Validacion en el navegador: mismas reglas que el servidor (src/Controller/Reserva.php) ----
        // El servidor vuelve a validar todo (es la validacion real); esto solo marca cada campo
        // al instante, sin esperar a que la pagina se recargue.
        const hoy = $("fecha_ingreso").min;

        const leerDatos = () => ({
            tipo_id: $("tipo_id").value,
            fecha_ingreso: $("fecha_ingreso").value,
            fecha_salida: $("fecha_salida").value,
            nombre_completo: $("nombre_completo").value.trim(),
            tipo_documento: $("tipo_documento").value,
            numero_documento: $("numero_documento").value.trim().toUpperCase(),
            telefono: $("telefono").value.trim(),
            modalidad_pago: form.querySelector("input[name=modalidad_pago]:checked")?.value || "",
            metodo_pago: form.querySelector("input[name=metodo_pago]:checked")?.value || "",
            acepta: $("acepta").checked,
        });

        const reglas = {
            tipo_id: (d) => d.tipo_id ? null : "Selecciona una habitación.",
            fecha_ingreso: (d) => (!d.fecha_ingreso || d.fecha_ingreso < hoy) ? "Ingresa una fecha de llegada válida (hoy o posterior)." : null,
            fecha_salida: (d) => {
                if (!d.fecha_salida || d.fecha_salida <= d.fecha_ingreso) return "La salida debe ser posterior a la llegada.";
                const noches = (new Date(d.fecha_salida) - new Date(d.fecha_ingreso)) / 86400000;
                return noches > 30 ? "La estadía máxima es de 30 noches." : null;
            },
            nombre_completo: (d) => (/^[\p{L}][\p{L} '.-]{3,158}$/u.test(d.nombre_completo) && d.nombre_completo.includes(" "))
                ? null : "Escribe tu nombre y apellido (solo letras).",
            tipo_documento: (d) => ["DNI", "PASAPORTE"].includes(d.tipo_documento) ? null : "Elige un tipo de documento.",
            numero_documento: (d) => {
                if (d.tipo_documento === "DNI") return /^\d{8}$/.test(d.numero_documento) ? null : "El DNI debe tener exactamente 8 dígitos.";
                if (d.tipo_documento === "PASAPORTE") return /^(?=.*[A-Z])[A-Z0-9]{6,12}$/.test(d.numero_documento) ? null : "El pasaporte debe tener de 6 a 12 letras o números, con al menos una letra.";
                return null; // el error de tipo_documento ya avisa
            },
            telefono: (d) => /^\+?\d{7,15}$/.test(d.telefono) ? null : "Celular no válido (solo números, 7 a 15 dígitos).",
            modalidad_pago: (d) => d.modalidad_pago ? null : "Elige cómo pagar.",
            metodo_pago: (d) => d.metodo_pago ? null : "Elige un método de pago.",
            acepta: (d) => d.acepta ? null : "Debes aceptar los términos y condiciones.",
        };

        const mostrarError = (campo, mensaje) => {
            const contenedor = form.querySelector('[data-error="' + campo + '"]');
            if (contenedor) {
                contenedor.textContent = mensaje || "";
                contenedor.classList.toggle("d-block", Boolean(mensaje));
            }
            const control = $(campo);
            if (control) control.classList.toggle("is-invalid", Boolean(mensaje));
            return !mensaje;
        };

        const validarCampo = (campo) => !mostrarError(campo, reglas[campo](leerDatos()));

        // Ajusta el marcador segun el tipo de documento elegido (DNI: 8 digitos, pasaporte: hasta 12)
        const actualizarDocumento = () => {
            const esDni = $("tipo_documento").value === "DNI";
            $("numero_documento").maxLength = esDni ? 8 : 12;
            $("numero_documento").placeholder = esDni ? "Ej. 12345678" : "Ej. AB123456";
        };
        actualizarDocumento();
        $("tipo_documento").addEventListener("change", () => { actualizarDocumento(); validarCampo("numero_documento"); });

        // Revalida un campo apenas el huesped lo corrige, sin esperar a reenviar el formulario
        ["tipo_id", "fecha_ingreso", "fecha_salida", "nombre_completo", "numero_documento", "telefono"].forEach((campo) => {
            $(campo).addEventListener("input", () => { if ($(campo).classList.contains("is-invalid")) validarCampo(campo); });
        });
        form.querySelectorAll("input[name=modalidad_pago], input[name=metodo_pago]").forEach((radio) => {
            radio.addEventListener("change", () => validarCampo(radio.name));
        });
        $("acepta").addEventListener("change", () => validarCampo("acepta"));

        form.addEventListener("submit", (evento) => {
            const datos = leerDatos();
            let primerError = null;
            for (const campo in reglas) {
                if (!mostrarError(campo, reglas[campo](datos)) && !primerError) primerError = campo;
            }
            if (primerError) {
                evento.preventDefault();
                const el = $(primerError) || form.querySelector('[name="' + primerError + '"]');
                el?.scrollIntoView({ behavior: "smooth", block: "center" });
                el?.focus();
            }
        });
    }

    // ---- Boton "Copiar direccion" ----
    document.querySelectorAll("[data-copiar]").forEach((boton) => {
        boton.addEventListener("click", () => {
            navigator.clipboard?.writeText(boton.dataset.copiar).then(() => {
                const texto = boton.innerHTML;
                boton.textContent = "¡Copiado!";
                setTimeout(() => (boton.innerHTML = texto), 1500);
            });
        });
    });
})();
