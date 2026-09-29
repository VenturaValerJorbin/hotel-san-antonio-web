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
            const opcionPlan = $("plan_pension_id").selectedOptions[0];
            const precioHabitacion = parseFloat(opcion?.dataset.precio || 0);
            const precioPlan = parseFloat(opcionPlan?.dataset.precio || 0);
            const precio = precioHabitacion + precioPlan;
            const n = noches();
            const total = precio * n;
            const fraccionado = $("modalidad_pago").value === "fraccionado";
            const ahora = fraccionado ? Math.round(total * porcentaje) / 100 : total;

            $("r_habitacion").textContent = opcion?.value ? opcion.dataset.nombre : "—";
            $("r_piso").textContent = $("piso").value !== "0" ? "Piso " + $("piso").value : "Sin preferencia";
            $("r_ingreso").textContent = $("fecha_ingreso").value || "—";
            $("r_salida").textContent = $("fecha_salida").value || "—";
            $("r_noches").textContent = n ? n + (n === 1 ? " noche" : " noches") : "—";
            $("r_plan").textContent = opcionPlan ? opcionPlan.textContent.trim() : "—";
            $("planDescripcion").textContent = opcionPlan?.dataset.descripcion || "";
            $("r_tarifa").textContent = soles(precio);
            $("r_total").textContent = soles(total);
            $("r_ahora_txt").textContent = fraccionado ? "Pagas ahora (" + porcentaje + " %)" : "Pagas ahora (100 %)";
            $("r_ahora").textContent = soles(ahora);
            $("r_saldo").textContent = soles(total - ahora);
        };

        // La salida debe ser al menos un dia despues de la llegada. Si esta vacia o quedo
        // invalida con la nueva llegada, se autocompleta a "un dia despues"; el huesped puede
        // cambiarla despues para quedarse mas noches.
        const limitarSalida = () => {
            const ingreso = $("fecha_ingreso").value;
            if (!ingreso) return;
            const minimo = new Date(ingreso);
            minimo.setDate(minimo.getDate() + 1);
            const minimoTexto = minimo.toISOString().slice(0, 10);
            $("fecha_salida").min = minimoTexto;
            if (!$("fecha_salida").value || $("fecha_salida").value < minimoTexto) {
                $("fecha_salida").value = minimoTexto;
            }
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
            plan_pension_id: $("plan_pension_id").value,
            fecha_ingreso: $("fecha_ingreso").value,
            fecha_salida: $("fecha_salida").value,
            nombre_completo: $("nombre_completo").value.trim(),
            tipo_documento: $("tipo_documento").value,
            numero_documento: $("numero_documento").value.trim().toUpperCase(),
            telefono: $("telefono").value.trim(),
            correo: $("correo").value.trim(),
            modalidad_pago: $("modalidad_pago").value,
            metodo_pago: form.querySelector("input[name=metodo_pago]:checked")?.value || "",
            acepta: $("acepta").checked,
        });

        const reglas = {
            tipo_id: (d) => d.tipo_id ? null : "Selecciona una habitación.",
            plan_pension_id: (d) => d.plan_pension_id ? null : "Elige un plan de alimentación.",
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
            correo: (d) => (!d.correo || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.correo)) ? null : "Ingresa un correo válido, o deja el campo vacío.",
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

        const validarCampo = (campo) => mostrarError(campo, reglas[campo](leerDatos()));

        // Ajusta el marcador segun el tipo de documento elegido (DNI: 8 digitos, pasaporte: hasta 12)
        const actualizarDocumento = () => {
            const esDni = $("tipo_documento").value === "DNI";
            $("numero_documento").maxLength = esDni ? 8 : 12;
            $("numero_documento").placeholder = esDni ? "Ej. 12345678" : "Ej. AB123456";
        };
        actualizarDocumento();
        $("tipo_documento").addEventListener("change", () => { actualizarDocumento(); validarCampo("numero_documento"); });

        // Revalida un campo apenas el huesped lo corrige, sin esperar a reenviar el formulario
        ["tipo_id", "fecha_ingreso", "fecha_salida", "nombre_completo", "numero_documento", "telefono", "correo"].forEach((campo) => {
            $(campo).addEventListener("input", () => { if ($(campo).classList.contains("is-invalid")) validarCampo(campo); });
        });
        $("plan_pension_id").addEventListener("change", () => validarCampo("plan_pension_id"));
        $("modalidad_pago").addEventListener("change", () => validarCampo("modalidad_pago"));
        form.querySelectorAll("input[name=metodo_pago]").forEach((radio) => {
            radio.addEventListener("change", () => validarCampo("metodo_pago"));
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

        // ---- Verificar puntos: el pago fraccionado solo se habilita si el documento realmente
        // tiene los puntos necesarios (el servidor vuelve a comprobarlo igual al confirmar) ----
        const selectModalidad = $("modalidad_pago");
        const opcionFraccionada = selectModalidad.querySelector('option[value="fraccionado"]');
        const btnVerificar = $("btnVerificarPuntos");
        const linkVerPuntos = $("linkVerPuntos");

        const actualizarLinkPuntos = () => {
            if (!linkVerPuntos) return;
            const numero = $("numero_documento").value.trim();
            const base = linkVerPuntos.dataset.base + "#consulta";
            linkVerPuntos.href = numero
                ? base.replace("#consulta", "?tipo_documento=" + encodeURIComponent($("tipo_documento").value)
                    + "&numero_documento=" + encodeURIComponent(numero) + "#consulta")
                : base;
        };
        actualizarLinkPuntos();
        $("tipo_documento").addEventListener("change", actualizarLinkPuntos);
        $("numero_documento").addEventListener("input", actualizarLinkPuntos);

        if (opcionFraccionada && btnVerificar) {
            const resultado = $("resultadoVerificacion");
            const puntosNecesarios = parseInt(opcionFraccionada.dataset.puntosRequeridos || "0", 10);

            const invalidarVerificacion = () => {
                opcionFraccionada.disabled = true;
                if (selectModalidad.value === "fraccionado") { selectModalidad.value = "completo"; actualizar(); }
                resultado.textContent = "";
            };
            $("tipo_documento").addEventListener("change", invalidarVerificacion);
            $("numero_documento").addEventListener("input", invalidarVerificacion);

            btnVerificar.addEventListener("click", async () => {
                if (!validarCampo("tipo_documento") || !validarCampo("numero_documento")) {
                    resultado.className = "small text-danger";
                    resultado.textContent = "Revisa tu tipo y número de documento antes de verificar.";
                    return;
                }
                const tipoDocumento = $("tipo_documento").value;
                const numeroDocumento = $("numero_documento").value.trim();
                resultado.className = "small text-muted";
                resultado.textContent = "Verificando…";
                btnVerificar.disabled = true;
                try {
                    const url = form.dataset.consultaPuntos + "?tipo_documento=" + encodeURIComponent(tipoDocumento)
                        + "&numero_documento=" + encodeURIComponent(numeroDocumento);
                    const respuesta = await fetch(url);
                    const datos = await respuesta.json();
                    if (!respuesta.ok || !datos.ok) throw new Error();
                    const alcanza = datos.encontrado && datos.puntos >= puntosNecesarios;
                    opcionFraccionada.disabled = !alcanza;
                    resultado.className = "small " + (alcanza ? "text-success" : "text-muted");
                    resultado.textContent = datos.encontrado
                        ? "Tienes " + datos.puntos + " puntos."
                            + (alcanza ? " Ya puedes elegir el pago fraccionado." : " Necesitas " + puntosNecesarios + " puntos.")
                        : "No encontramos estadías previas con ese documento. Empiezas con 0 puntos.";
                } catch {
                    resultado.className = "small text-danger";
                    resultado.textContent = "No se pudo verificar. Intenta nuevamente.";
                } finally {
                    btnVerificar.disabled = false;
                }
            });
        }
    }

    // ---- Consulta de puntos (publico/puntos.php): el huesped ve su saldo con su documento ----
    const formConsulta = document.getElementById("formConsultaPuntos");
    if (formConsulta) {
        const tipoSel = document.getElementById("consulta_tipo_documento");
        const numInput = document.getElementById("consulta_numero_documento");
        const resultado = document.getElementById("resultadoConsultaPuntos");
        const url = formConsulta.dataset.consulta;

        // El DNI es siempre de 8 digitos exactos: no dejar escribir de mas (igual que en Reservar)
        const actualizarLongitud = () => {
            const esDni = tipoSel.value === "DNI";
            numInput.maxLength = esDni ? 8 : 12;
            numInput.placeholder = esDni ? "Ej. 12345678" : "Ej. AB123456";
        };
        // Si el documento cambia despues de consultar, el resultado anterior ya no corresponde
        const limpiarResultado = () => { resultado.innerHTML = ""; };
        actualizarLongitud();
        tipoSel.addEventListener("change", () => { actualizarLongitud(); limpiarResultado(); });
        numInput.addEventListener("input", limpiarResultado);

        const consultar = async () => {
            const tipo = tipoSel.value;
            const numero = numInput.value.trim().toUpperCase();
            const valido = tipo === "DNI" ? /^\d{8}$/.test(numero) : /^(?=.*[A-Z])[A-Z0-9]{6,12}$/.test(numero);
            if (!valido) {
                resultado.innerHTML = '<div class="text-danger small">Ingresa un '
                    + (tipo === "DNI" ? "DNI de 8 dígitos" : "pasaporte válido (6 a 12 letras o números, con al menos una letra)") + ".</div>";
                return;
            }
            resultado.innerHTML = '<div class="text-muted small">Consultando…</div>';
            try {
                const respuesta = await fetch(url + "?tipo_documento=" + encodeURIComponent(tipo) + "&numero_documento=" + encodeURIComponent(numero));
                const datos = await respuesta.json();
                if (!respuesta.ok || !datos.ok) throw new Error();
                resultado.innerHTML = datos.encontrado
                    ? '<div class="sa-caja-crema d-inline-block"><span class="sa-nivel-puntos">' + datos.puntos + " <small>puntos</small></span></div>"
                    : '<div class="sa-caja-crema small d-inline-block">Aún no registramos estadías con ese documento. Empiezas con 0 puntos.</div>';
            } catch {
                resultado.innerHTML = '<div class="text-danger small">No se pudo consultar. Intenta nuevamente.</div>';
            }
        };

        formConsulta.addEventListener("submit", (evento) => { evento.preventDefault(); consultar(); });

        // Si se llega desde el formulario de reserva con el documento ya escrito, se completa y consulta sola
        const parametros = new URLSearchParams(location.search);
        if (parametros.get("numero_documento")) {
            if (parametros.get("tipo_documento") === "PASAPORTE") tipoSel.value = "PASAPORTE";
            actualizarLongitud();
            numInput.value = parametros.get("numero_documento");
            consultar();
        }
    }

    // ---- Formulario de contacto: validacion en el navegador, mismas reglas que el servidor
    // (src/Controller/Contacto.php), sin esperar a que la pagina se recargue ----
    const formContacto = document.getElementById("formContacto");
    if (formContacto) {
        const $c = (id) => document.getElementById(id);

        const leerDatosContacto = () => ({
            nombre: $c("nombre").value.trim(),
            correo: $c("correo").value.trim(),
            telefono: $c("telefono").value.trim(),
            asunto: $c("asunto").value,
            mensaje: $c("mensaje").value.trim(),
        });

        const reglasContacto = {
            nombre: (d) => /^[\p{L}][\p{L} '.-]{2,99}$/u.test(d.nombre) ? null : "Escribe tu nombre (solo letras).",
            correo: (d) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.correo) ? null : "Correo electrónico no válido.",
            telefono: (d) => /^\+?\d{7,15}$/.test(d.telefono) ? null : "Celular no válido (solo números, 7 a 15 dígitos).",
            asunto: (d) => d.asunto ? null : "Selecciona un asunto.",
            mensaje: (d) => (d.mensaje.length >= 10 && d.mensaje.length <= 500) ? null : "El mensaje debe tener entre 10 y 500 caracteres.",
        };

        const mostrarErrorContacto = (campo, mensaje) => {
            const contenedor = formContacto.querySelector('[data-error="' + campo + '"]');
            if (contenedor) {
                contenedor.textContent = mensaje || "";
                contenedor.classList.toggle("d-block", Boolean(mensaje));
            }
            const control = $c(campo);
            if (control) control.classList.toggle("is-invalid", Boolean(mensaje));
            return !mensaje;
        };

        const validarCampoContacto = (campo) => mostrarErrorContacto(campo, reglasContacto[campo](leerDatosContacto()));

        // Los campos de texto se revalidan al escribir (solo si ya estaban marcados en rojo);
        // el "asunto" es un select, se revalida apenas cambia (igual que modalidad_pago en Reservar)
        ["nombre", "correo", "telefono", "mensaje"].forEach((campo) => {
            $c(campo).addEventListener("input", () => { if ($c(campo).classList.contains("is-invalid")) validarCampoContacto(campo); });
        });
        $c("asunto").addEventListener("change", () => validarCampoContacto("asunto"));

        formContacto.addEventListener("submit", (evento) => {
            const datos = leerDatosContacto();
            let primerError = null;
            for (const campo in reglasContacto) {
                if (!mostrarErrorContacto(campo, reglasContacto[campo](datos)) && !primerError) primerError = campo;
            }
            if (primerError) {
                evento.preventDefault();
                $c(primerError)?.scrollIntoView({ behavior: "smooth", block: "center" });
                $c(primerError)?.focus();
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
