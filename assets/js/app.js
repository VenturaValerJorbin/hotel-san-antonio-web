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
