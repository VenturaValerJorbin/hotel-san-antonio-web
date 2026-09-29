-- =====================================================================
-- Hotel Turistico San Antonio - Aplicacion web (Desarrollo de Aplicaciones Web II)
-- Base de datos completa para las 3 unidades del curso.
--   Unidad I  : tipo_habitacion, habitacion, huesped, reserva, pago, puntos, contacto, carta
--   Unidad II : rol, usuario (login y RBAC)
--   Unidad III: pedido, comprobante (room service, reportes)
-- Convenciones pensadas para migrar luego a un framework (ORM):
--   id autoincremental, created_at / updated_at, borrado logico con "activo".
-- Normalizada a 3FN: los valores calculados (noches, total, saldo, puntos) salen de vistas.
-- =====================================================================

DROP DATABASE IF EXISTS daw2_hotel_san_antonio;
CREATE DATABASE daw2_hotel_san_antonio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE daw2_hotel_san_antonio;

-- Fuerza utf8mb4 en esta sesion sin importar la codificacion por defecto de quien importe el
-- archivo (phpMyAdmin, la terminal de MySQL, etc.). Sin esto, un cliente con otra codificacion
-- por defecto puede guardar mal las tildes y enies de los datos de ejemplo.
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- HABITACIONES
-- ---------------------------------------------------------------------
CREATE TABLE tipo_habitacion (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(40)   NOT NULL UNIQUE,
    descripcion    VARCHAR(255)  NOT NULL,
    detalle        TEXT          NULL,
    capacidad      TINYINT UNSIGNED NOT NULL,
    precio_noche   DECIMAL(8,2)  NOT NULL CHECK (precio_noche > 0),
    activo         TINYINT(1)    NOT NULL DEFAULT 1,
    created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE foto_tipo_habitacion (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_id  INT UNSIGNED NOT NULL,
    ruta     VARCHAR(255) NOT NULL,
    orden    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_foto_tipo FOREIGN KEY (tipo_id) REFERENCES tipo_habitacion(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE habitacion (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero       VARCHAR(5)   NOT NULL UNIQUE,
    piso         TINYINT UNSIGNED NOT NULL,
    tipo_id      INT UNSIGNED NOT NULL,
    estado       ENUM('disponible','ocupada','limpieza','mantenimiento') NOT NULL DEFAULT 'disponible',
    descripcion  VARCHAR(255) NULL,
    activo       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_habitacion_tipo FOREIGN KEY (tipo_id) REFERENCES tipo_habitacion(id)
) ENGINE=InnoDB;

CREATE TABLE servicio (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(50) NOT NULL UNIQUE,
    icono   VARCHAR(30) NULL
) ENGINE=InnoDB;

-- Los servicios dependen del TIPO de habitacion (ej. la Simple no incluye agua caliente)
CREATE TABLE tipo_servicio (
    tipo_id      INT UNSIGNED NOT NULL,
    servicio_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (tipo_id, servicio_id),
    CONSTRAINT fk_ts_tipo     FOREIGN KEY (tipo_id)     REFERENCES tipo_habitacion(id) ON DELETE CASCADE,
    CONSTRAINT fk_ts_servicio FOREIGN KEY (servicio_id) REFERENCES servicio(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- USUARIOS DEL PANEL (Unidad II: login y RBAC)
-- ---------------------------------------------------------------------
CREATE TABLE rol (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE usuario (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id      INT UNSIGNED NOT NULL,
    nombres     VARCHAR(80)  NOT NULL,
    correo      VARCHAR(100) NOT NULL UNIQUE,
    clave       VARCHAR(255) NOT NULL,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES rol(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- HUESPEDES Y PROGRAMA DE PUNTOS
-- ---------------------------------------------------------------------
CREATE TABLE huesped (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_documento    ENUM('DNI','PASAPORTE','CE') NOT NULL DEFAULT 'DNI',
    numero_documento  VARCHAR(20)  NOT NULL,
    nombre_completo   VARCHAR(160) NOT NULL,
    correo            VARCHAR(100) NULL,
    telefono          VARCHAR(20)  NOT NULL,
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_huesped_documento (tipo_documento, numero_documento)
) ENGINE=InnoDB;

-- Reglas del programa de puntos editables por el administrador (no van fijas en el codigo)
CREATE TABLE parametro (
    clave        VARCHAR(50)  PRIMARY KEY,
    valor        VARCHAR(50)  NOT NULL,
    descripcion  VARCHAR(150) NOT NULL,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- RESTAURANTE (carta publica y room service de la Unidad III)
-- ---------------------------------------------------------------------
CREATE TABLE categoria_producto (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- precio NULL = plato "a la carta" cuyo precio se consulta en el restaurante
CREATE TABLE producto (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id  INT UNSIGNED NOT NULL,
    nombre        VARCHAR(80)  NOT NULL,
    descripcion   VARCHAR(200) NULL,
    precio        DECIMAL(7,2) NULL CHECK (precio IS NULL OR precio > 0),
    foto          VARCHAR(255) NULL,
    activo        TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categoria_producto(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- RESERVAS Y PAGOS
-- ---------------------------------------------------------------------
-- Plan de pension (regimen alimenticio): igual que en cualquier sistema hotelero real, el huesped
-- elige cuantas comidas quiere incluidas en su estadia. Con media pension o pension completa puede
-- pedir cualquier plato de la carta (no un menu fijo): no se factura plato por plato, ya esta
-- pagado por noche en la reserva. El precio se suma por noche (no por huesped: la reserva no pide
-- cuantos son en el grupo).
CREATE TABLE plan_pension (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(40)  NOT NULL,
    descripcion       VARCHAR(200) NULL,
    precio_por_noche  DECIMAL(6,2) NOT NULL DEFAULT 0,   -- precios de ejemplo, por confirmar con el hotel
    orden             TINYINT UNSIGNED NOT NULL DEFAULT 0,
    activo            TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE reserva (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo               VARCHAR(12)  NOT NULL UNIQUE,
    huesped_id           INT UNSIGNED NOT NULL,
    habitacion_id        INT UNSIGNED NOT NULL,
    fecha_ingreso        DATE         NOT NULL,
    fecha_salida         DATE         NOT NULL,
    num_huespedes        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    precio_noche         DECIMAL(8,2) NOT NULL,             -- foto del precio al reservar
    plan_pension_id      INT UNSIGNED NOT NULL DEFAULT 1,   -- 1 = Solo alojamiento
    precio_plan_pension  DECIMAL(6,2) NOT NULL DEFAULT 0,   -- foto del precio del plan al reservar
    monto_descuento      DECIMAL(9,2) NOT NULL DEFAULT 0,   -- descuento por canje de puntos
    monto_adelanto       DECIMAL(9,2) NOT NULL,             -- lo que se cobra online al reservar
    modalidad_pago       ENUM('completo','fraccionado') NOT NULL DEFAULT 'completo',
    estado               ENUM('pendiente','confirmada','checkin','checkout','cancelada','no_show') NOT NULL DEFAULT 'pendiente',
    observaciones        VARCHAR(255) NULL,
    usuario_id           INT UNSIGNED NULL,
    created_at           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_huesped     FOREIGN KEY (huesped_id)      REFERENCES huesped(id),
    CONSTRAINT fk_reserva_habitacion  FOREIGN KEY (habitacion_id)   REFERENCES habitacion(id),
    CONSTRAINT fk_reserva_usuario     FOREIGN KEY (usuario_id)      REFERENCES usuario(id),
    CONSTRAINT fk_reserva_plan_pension FOREIGN KEY (plan_pension_id) REFERENCES plan_pension(id),
    CONSTRAINT ck_reserva_fechas CHECK (fecha_salida > fecha_ingreso),
    INDEX idx_reserva_fechas (habitacion_id, fecha_ingreso, fecha_salida)
) ENGINE=InnoDB;

-- Una reserva puede tener varios pagos: adelanto online + saldo al llegar (o un pago total)
CREATE TABLE pago (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reserva_id           INT UNSIGNED NOT NULL,
    tipo                 ENUM('adelanto','saldo','total') NOT NULL,
    monto                DECIMAL(9,2) NOT NULL CHECK (monto > 0),
    metodo               ENUM('tarjeta','yape','transferencia','efectivo') NOT NULL,
    estado               ENUM('pendiente','aprobado','rechazado','reembolsado') NOT NULL DEFAULT 'pendiente',
    pasarela             VARCHAR(30)  NULL,
    codigo_transaccion   VARCHAR(80)  NULL,
    motivo_reembolso     VARCHAR(255) NULL,
    fecha_pago           DATETIME     NULL,
    fecha_reembolso      DATETIME     NULL,
    usuario_id           INT UNSIGNED NULL,
    created_at           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_reserva FOREIGN KEY (reserva_id) REFERENCES reserva(id),
    CONSTRAINT fk_pago_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(id)
) ENGINE=InnoDB;

-- Catalogo de recompensas del programa de puntos (editable por el administrador)
--   pago_fraccionado : beneficio permanente, no gasta puntos (valor = % de adelanto)
--   descuento        : canje, valor = % de descuento sobre la estadia
--   producto         : canje, entrega un producto (ej. desayuno)
--   noche_gratis     : canje, una noche de cortesia
-- Catalogo ACUMULATIVO por niveles de puntos (como los planes de una suscripcion: cada nivel
-- superior incluye lo del anterior y le suma algo mas). "pago_fraccionado" no consume puntos
-- (es un beneficio permanente mientras se mantengan); los demas si se canjean (ver canje_recompensa).
CREATE TABLE recompensa (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre             VARCHAR(80)  NOT NULL,
    descripcion        VARCHAR(200) NULL,
    puntos_requeridos  INT UNSIGNED NOT NULL,
    tipo               ENUM('pago_fraccionado','descuento','producto','noche_gratis','plan_pension') NOT NULL,
    valor              DECIMAL(5,2) NULL,
    producto_id        INT UNSIGNED NULL,
    plan_pension_id    INT UNSIGNED NULL,
    consume_puntos     TINYINT(1)   NOT NULL DEFAULT 1,
    activo             TINYINT(1)   NOT NULL DEFAULT 1,
    created_at         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_recompensa_producto     FOREIGN KEY (producto_id)     REFERENCES producto(id),
    CONSTRAINT fk_recompensa_plan_pension FOREIGN KEY (plan_pension_id) REFERENCES plan_pension(id)
) ENGINE=InnoDB;

CREATE TABLE canje_recompensa (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    huesped_id    INT UNSIGNED NOT NULL,
    recompensa_id INT UNSIGNED NOT NULL,
    reserva_id    INT UNSIGNED NULL,
    estado        ENUM('pendiente','entregado','anulado') NOT NULL DEFAULT 'pendiente',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_canje_huesped    FOREIGN KEY (huesped_id)    REFERENCES huesped(id),
    CONSTRAINT fk_canje_recompensa FOREIGN KEY (recompensa_id) REFERENCES recompensa(id),
    CONSTRAINT fk_canje_reserva    FOREIGN KEY (reserva_id)    REFERENCES reserva(id)
) ENGINE=InnoDB;

-- Historial de puntos (ganado = positivo, canjeado = negativo). El saldo se calcula, no se guarda.
CREATE TABLE movimiento_puntos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    huesped_id   INT UNSIGNED NOT NULL,
    reserva_id   INT UNSIGNED NULL,
    canje_id     INT UNSIGNED NULL,
    tipo         ENUM('ganado','canjeado','ajuste') NOT NULL,
    puntos       INT          NOT NULL,
    descripcion  VARCHAR(150) NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mp_huesped FOREIGN KEY (huesped_id) REFERENCES huesped(id),
    CONSTRAINT fk_mp_reserva FOREIGN KEY (reserva_id) REFERENCES reserva(id),
    CONSTRAINT fk_mp_canje   FOREIGN KEY (canje_id)   REFERENCES canje_recompensa(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- ROOM SERVICE Y COMPROBANTES (Unidad III)
-- ---------------------------------------------------------------------
CREATE TABLE pedido (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reserva_id     INT UNSIGNED NULL,
    tipo           ENUM('room_service','restaurante') NOT NULL,
    estado         ENUM('pendiente','preparando','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_reserva FOREIGN KEY (reserva_id) REFERENCES reserva(id)
) ENGINE=InnoDB;

CREATE TABLE detalle_pedido (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id        INT UNSIGNED NOT NULL,
    producto_id      INT UNSIGNED NOT NULL,
    cantidad         SMALLINT UNSIGNED NOT NULL,
    precio_unitario  DECIMAL(7,2) NOT NULL,
    CONSTRAINT fk_dp_pedido   FOREIGN KEY (pedido_id)   REFERENCES pedido(id) ON DELETE CASCADE,
    CONSTRAINT fk_dp_producto FOREIGN KEY (producto_id) REFERENCES producto(id)
) ENGINE=InnoDB;

CREATE TABLE comprobante (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reserva_id  INT UNSIGNED NULL,
    pedido_id   INT UNSIGNED NULL,
    tipo        ENUM('boleta','factura') NOT NULL DEFAULT 'boleta',
    serie       VARCHAR(4)   NOT NULL,
    numero      INT UNSIGNED NOT NULL,
    monto       DECIMAL(9,2) NOT NULL,
    emitido_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_comprobante (serie, numero),
    CONSTRAINT fk_comp_reserva FOREIGN KEY (reserva_id) REFERENCES reserva(id),
    CONSTRAINT fk_comp_pedido  FOREIGN KEY (pedido_id)  REFERENCES pedido(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- RECOMENDACIONES TURISTICAS Y CONTACTO
-- ---------------------------------------------------------------------
CREATE TABLE lugar_turistico (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(80)  NOT NULL,
    categoria    VARCHAR(30)  NOT NULL,
    descripcion  VARCHAR(255) NOT NULL,
    foto         VARCHAR(255) NULL,
    activo       TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE mensaje_contacto (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    correo      VARCHAR(100) NOT NULL,
    telefono    VARCHAR(20)  NOT NULL,
    asunto      ENUM('reserva','consulta','sugerencia','reclamo') NOT NULL,
    mensaje     VARCHAR(500) NOT NULL,
    leido       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Limite de intentos por IP en consultas/formularios publicos sensibles (evita fuerza bruta y
-- spam). No guarda quien es la persona, solo su IP, la accion y cuando. Las filas viejas se
-- borran solas (ver Dao\LimiteIntento::limpiarAntiguos), no hay tarea programada en el proyecto.
CREATE TABLE limite_intento (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip         VARCHAR(45)  NOT NULL,
    accion     VARCHAR(40)  NOT NULL,
    creado_en  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_limite (ip, accion, creado_en)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- VISTAS: valores calculados (asi las tablas se mantienen en 3FN)
-- ---------------------------------------------------------------------
CREATE VIEW vista_reserva AS
SELECT r.*,
       DATEDIFF(r.fecha_salida, r.fecha_ingreso)                                                     AS noches,
       DATEDIFF(r.fecha_salida, r.fecha_ingreso) * (r.precio_noche + r.precio_plan_pension) - r.monto_descuento AS monto_total,
       COALESCE(p.pagado, 0)                                                                         AS monto_pagado,
       DATEDIFF(r.fecha_salida, r.fecha_ingreso) * (r.precio_noche + r.precio_plan_pension) - r.monto_descuento
           - COALESCE(p.pagado, 0)                                                                   AS saldo_pendiente
FROM reserva r
LEFT JOIN (SELECT reserva_id, SUM(monto) AS pagado
           FROM pago WHERE estado = 'aprobado' GROUP BY reserva_id) p ON p.reserva_id = r.id;

CREATE VIEW vista_puntos_huesped AS
SELECT h.id AS huesped_id, COALESCE(SUM(m.puntos), 0) AS puntos
FROM huesped h
LEFT JOIN movimiento_puntos m ON m.huesped_id = h.id
GROUP BY h.id;

CREATE VIEW vista_pedido AS
SELECT p.*, COALESCE(SUM(d.cantidad * d.precio_unitario), 0) AS total
FROM pedido p
LEFT JOIN detalle_pedido d ON d.pedido_id = p.id
GROUP BY p.id;

-- =====================================================================
-- DATOS INICIALES
-- =====================================================================
INSERT INTO rol (nombre) VALUES ('administrador'), ('recepcionista');

-- Clave de prueba de ambos usuarios: Admin123*  (guardada con password_hash)
INSERT INTO usuario (rol_id, nombres, correo, clave) VALUES
(1, 'Administrador San Antonio', 'admin@sanantonio.pe',
    '$2y$10$wghTX6l0xeUQDUKKuBHiVuF6.xwUad30MKKYj7c65d2Hhg9l6Q2w6'),
(2, 'Recepción Turno Día', 'recepcion@sanantonio.pe',
    '$2y$10$wghTX6l0xeUQDUKKuBHiVuF6.xwUad30MKKYj7c65d2Hhg9l6Q2w6');

-- Los 6 tipos de habitacion y sus tarifas. Solo la Simple no incluye agua caliente.
INSERT INTO tipo_habitacion (nombre, descripcion, detalle, capacidad, precio_noche) VALUES
('Simple', 'Habitación sencilla y cómoda para una persona.',
 'La habitación Simple del Hotel San Antonio ofrece un ambiente tranquilo y funcional, ideal para viajeros que buscan descanso y buen precio en el corazón de Bagua.', 1, 100.00),
('Ejecutiva', 'Habitación ejecutiva con escritorio y agua caliente para una persona.',
 'Pensada para quienes viajan por trabajo: escritorio, silla y aire acondicionado en un ambiente ordenado y silencioso, con baño privado con agua caliente para un mayor confort después de la jornada.', 1, 130.00),
('Matrimonial', 'Cama de dos plazas para dos personas.',
 'Habitación matrimonial amplia y acogedora, ideal para parejas que visitan Bagua por turismo o descanso.', 2, 130.00),
('Doble', 'Dos camas para dos personas.',
 'Habitación con dos camas individuales, perfecta para amigos, familiares o compañeros de viaje.', 2, 150.00),
('Suite', 'Suite con mayor espacio para dos personas.',
 'La Suite ofrece más espacio y comodidad, con un ambiente elegante para quienes desean una estadía especial.', 2, 150.00),
('King', 'Amplia y cómoda, perfecta para una estadía de descanso.',
 'La habitación King del Hotel San Antonio ofrece un ambiente amplio, elegante y acogedor, ideal para quienes buscan comodidad y tranquilidad en el corazón de la Amazonía. Disfruta de una cama king, espacios bien iluminados y una vista privilegiada a la naturaleza de Bagua.', 2, 180.00);

-- 17 habitaciones en 3 pisos (cantidad por confirmar con el hotel).
-- El tipo se indica por su nombre, asi el orden de los tipos no afecta a las habitaciones.
INSERT INTO habitacion (numero, piso, tipo_id)
SELECT h.numero, h.piso, t.id
FROM (
    SELECT '101' AS numero, 1 AS piso, 'Simple' AS tipo UNION ALL SELECT '102', 1, 'Ejecutiva' UNION ALL SELECT '103', 1, 'Ejecutiva'
    UNION ALL SELECT '104', 1, 'Simple' UNION ALL SELECT '105', 1, 'Ejecutiva' UNION ALL SELECT '106', 1, 'Ejecutiva'
    UNION ALL SELECT '201', 2, 'Matrimonial' UNION ALL SELECT '202', 2, 'Doble' UNION ALL SELECT '203', 2, 'Suite'
    UNION ALL SELECT '204', 2, 'King' UNION ALL SELECT '205', 2, 'Matrimonial' UNION ALL SELECT '206', 2, 'Doble'
    UNION ALL SELECT '301', 3, 'Simple' UNION ALL SELECT '302', 3, 'Ejecutiva' UNION ALL SELECT '303', 3, 'Matrimonial'
    UNION ALL SELECT '304', 3, 'Doble' UNION ALL SELECT '305', 3, 'Suite'
) h
JOIN tipo_habitacion t ON t.nombre = h.tipo
ORDER BY h.numero;

INSERT INTO servicio (nombre, icono) VALUES
('Baño privado','bi-droplet'),('Aire acondicionado','bi-snow'),('TV','bi-tv'),('Armario','bi-door-closed'),
('Escritorio','bi-laptop'),('Wi-Fi gratis','bi-wifi'),('Cochera gratis','bi-car-front'),('Agua caliente','bi-thermometer-half');

-- Todos los tipos: bano, aire, TV, armario, escritorio, Wi-Fi y cochera (Avance 01)
INSERT INTO tipo_servicio (tipo_id, servicio_id)
SELECT t.id, s.id FROM tipo_habitacion t CROSS JOIN servicio s WHERE s.nombre <> 'Agua caliente';
-- Agua caliente: todos los tipos menos la Simple
INSERT INTO tipo_servicio (tipo_id, servicio_id)
SELECT t.id, s.id FROM tipo_habitacion t CROSS JOIN servicio s
WHERE s.nombre = 'Agua caliente' AND t.nombre <> 'Simple';

-- Fotos de cada tipo de habitacion. Las imagenes estan en assets/img/ y aqui solo se guarda su ruta
-- (relativa a la raiz del proyecto) y el orden en que aparecen en el carrusel.
-- tipo_id 1 = Simple
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(1, 'assets/img/economic-room/economic-room1.avif', 1),
(1, 'assets/img/economic-room/economic-room2.avif', 2),
(1, 'assets/img/economic-room/economic-room3.avif', 3),
(1, 'assets/img/economic-room/economic-room4.avif', 4),
(1, 'assets/img/economic-room/economic-room5.avif', 5);

-- tipo_id 2 = Ejecutiva
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(2, 'assets/img/deluxe/hiabitacion_deluxe1.avif', 1),
(2, 'assets/img/deluxe/hiabitacion_deluxe2.avif', 2),
(2, 'assets/img/deluxe/hiabitacion_deluxe3.avif', 3),
(2, 'assets/img/deluxe/hiabitacion_deluxe4.avif', 4),
(2, 'assets/img/deluxe/hiabitacion_deluxe5.avif', 5),
(2, 'assets/img/deluxe/hiabitacion_deluxe6.avif', 6),
(2, 'assets/img/deluxe/hiabitacion_deluxe7.avif', 7),
(2, 'assets/img/deluxe/hiabitacion_deluxe8.avif', 8),
(2, 'assets/img/deluxe/9hiabitacion_deluxe.avif', 9);

-- tipo_id 3 = Matrimonial
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(3, 'assets/img/deluxe-queen/habitacion_queen1.avif', 1),
(3, 'assets/img/deluxe-queen/habitacion_queen2.avif', 2),
(3, 'assets/img/deluxe-queen/habitacion_queen3.avif', 3),
(3, 'assets/img/deluxe-queen/habitacion_queen4.avif', 4);

-- tipo_id 4 = Doble
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(4, 'assets/img/doble/habitacion-doble1.avif', 1),
(4, 'assets/img/doble/habitacion-doble2.avif', 2),
(4, 'assets/img/doble/habitacion-doble3.avif', 3),
(4, 'assets/img/doble/habitacion-doble4.avif', 4),
(4, 'assets/img/doble/habitacion-doble5.avif', 5),
(4, 'assets/img/doble/habitacion-doble6.avif', 6),
(4, 'assets/img/doble/habitacion-doble7.avif', 7),
(4, 'assets/img/doble/habitacion-doble8.avif', 8),
(4, 'assets/img/doble/habitacion-doble9.avif', 9),
(4, 'assets/img/doble/habitacion-doble10.avif', 10),
(4, 'assets/img/doble/habitacion-doble11.avif', 11),
(4, 'assets/img/doble/habitacion-doble12.avif', 12),
(4, 'assets/img/doble/habitacion-doble13.avif', 13),
(4, 'assets/img/doble/habitacion-doble14.avif', 14),
(4, 'assets/img/doble/habitacion-doble15.avif', 15),
(4, 'assets/img/doble/habitacion-doble16.avif', 16),
(4, 'assets/img/doble/habitacion-doble17.avif', 17),
(4, 'assets/img/doble/habitacion-doble18.avif', 18);

-- tipo_id 5 = Suite
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(5, 'assets/img/deluxe-suite/1deluxe-suite.avif', 1),
(5, 'assets/img/deluxe-suite/2deluxe-suite.avif', 2),
(5, 'assets/img/deluxe-suite/3deluxe-suite.avif', 3);

-- tipo_id 6 = King
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(6, 'assets/img/junior-suite/unior-suite1.avif', 1),
(6, 'assets/img/junior-suite/unior-suite2.avif', 2),
(6, 'assets/img/junior-suite/unior-suite3.avif', 3),
(6, 'assets/img/junior-suite/unior-suite4.avif', 4),
(6, 'assets/img/junior-suite/unior-suite5.avif', 5),
(6, 'assets/img/junior-suite/unior-suite6.avif', 6),
(6, 'assets/img/junior-suite/unior-suite7.avif', 7);

INSERT INTO parametro (clave, valor, descripcion) VALUES
('soles_por_punto',   '10', 'Soles gastados por cada punto ganado'),
('puntos_bienvenida', '0',  'Puntos otorgados al registrarse un huesped nuevo');

-- Planes de pension (regimen alimenticio). Precios de ejemplo, por confirmar con el hotel.
INSERT INTO plan_pension (nombre, descripcion, precio_por_noche, orden) VALUES
('Solo alojamiento', 'Solo la habitacion, sin comidas incluidas.', 0.00, 1),
('Alojamiento y desayuno', 'Incluye el desayuno para los huespedes de la habitacion.', 15.00, 2),
('Media pensión', 'Desayuno y una comida mas (almuerzo o cena, a eleccion), cualquier plato de la carta.', 35.00, 3),
('Pensión completa', 'Desayuno, almuerzo y cena incluidos, cualquier plato de la carta.', 55.00, 4);

INSERT INTO categoria_producto (nombre) VALUES ('Menú del día'),('Platos a la carta'),('Desayunos');

INSERT INTO producto (categoria_id, nombre, descripcion, precio) VALUES
(1,'Menú S/ 12','Sopa del día, segundo con guarnicion y bebida natural.',12.00),
(1,'Menú S/ 16','Sopa del día, segundo con guarnicion, bebida natural y postre del día.',16.00),
(2,'Cecina con patacones','Tradicional sabor amazónico, acompañada de patacones dorados.',NULL),
(2,'Chaufa amazónico','Nuestro toque selvático del clásico chaufa, con ingredientes de la región.',NULL),
(2,'Tilapia','Fresca y sabrosa, preparada al momento.',NULL),
(2,'Trucha','Deliciosa trucha de la región, con el inconfundible sabor amazónico.',NULL),
(2,'Pato','Una especialidad de la selva peruana, con sabor único y tradicional.',NULL),
(2,'Gallina','Receta tradicional, preparada con el auténtico sabor de nuestra tierra.',NULL),
(3,'Desayuno regional','Desayuno con productos de la región.',NULL);

-- Niveles ACUMULATIVOS y PERMANENTES (como el pago fraccionado: se revisan en vivo contra el
-- saldo actual, nunca se gastan puntos; no hay canje, se aplican solos al reservar).
-- 100 = fraccionado + desayuno (S/15 de descuento fijo); 150 suma pension completa de un dia
-- (S/55 fijo); 300 suma 10% de descuento; 500 sube ese descuento a 15% (reemplaza al de 300,
-- no se suman). Ver Bo\Reserva::descuentoPorPuntos.
INSERT INTO recompensa (nombre, descripcion, puntos_requeridos, tipo, valor, producto_id, plan_pension_id, consume_puntos) VALUES
('Pago fraccionado','Reserva pagando solo el 50 % ahora y el resto al llegar.',100,'pago_fraccionado',50,NULL,NULL,0),
('Desayuno de cortesía','Descuento fijo en tu reserva, equivalente a un desayuno, en cada estadía.',100,'producto',15.00,
    (SELECT id FROM producto WHERE nombre = 'Desayuno regional'),NULL,0),
('Pensión completa por un día','Descuento fijo en tu reserva, equivalente a un día de pensión completa, en cada estadía.',150,'plan_pension',55.00,NULL,
    (SELECT id FROM plan_pension WHERE nombre = 'Pensión completa'),0),
('10 % de descuento','Descuento sobre el costo de la estadía.',300,'descuento',10,NULL,NULL,0),
('15 % de descuento','El mayor descuento del programa, para nuestros huéspedes más frecuentes.',500,'descuento',15,NULL,NULL,0);

-- La columna foto guarda la ruta de la imagen (relativa a la raiz del proyecto)
INSERT INTO lugar_turistico (nombre, categoria, descripcion, foto) VALUES
('Pongo de Rentema','Naturaleza','Impresionante cañón del río Marañón, con paisajes únicos y gran belleza natural.','assets/img/recomendaciones/pongo-rentema.avif'),
('Sitio Arqueológico Las Juntas','Arqueología','Importante centro ceremonial prehispánico con historia y vistas privilegiadas.','assets/img/recomendaciones/las-juntas.avif'),
('Catarata Tsuntsuntsa','Cascada','Espectacular caída de agua rodeada de vegetación, ideal para los amantes de la naturaleza.','assets/img/recomendaciones/tsuntsuntsa.avif'),
('Catarata Nueva Esperanza (Numparket)','Cascada','Un paraíso natural de aguas cristalinas, perfecto para la aventura y el descanso.','assets/img/recomendaciones/nueva-esperanza.avif'),
('Cataratas del Bijao','Cascada','Conjunto de hermosas caídas de agua y pozas naturales en un entorno selvático.','assets/img/recomendaciones/bijao.avif'),
('Plaza de Armas de Bagua','Cultura','El corazón de la ciudad, con su iglesia, áreas verdes y el encanto de la vida local.','assets/img/recomendaciones/plaza-armas-bagua.avif');

-- Huespedes de prueba: Carlos ya tiene puntos suficientes para pagar el 50 %
INSERT INTO huesped (tipo_documento, numero_documento, nombre_completo, correo, telefono) VALUES
('DNI','70000001','Carlos Pérez Díaz','carlos@example.com','999111222'),
('DNI','70000002','María López Rojas','maria@example.com','999333444');

INSERT INTO movimiento_puntos (huesped_id, tipo, puntos, descripcion) VALUES
(1, 'ajuste', 150, 'Puntos iniciales de prueba');

-- Fotos de los platos de la carta (los platos ya existen mas arriba; aqui solo se les asigna la imagen)
UPDATE producto SET foto = 'assets/img/restaurante/secina con patacones.jpg' WHERE nombre = 'Cecina con patacones';
UPDATE producto SET foto = 'assets/img/restaurante/Chaufa amazónico.jpg'     WHERE nombre = 'Chaufa amazónico';
UPDATE producto SET foto = 'assets/img/restaurante/Tilapia.jpg'              WHERE nombre = 'Tilapia';
UPDATE producto SET foto = 'assets/img/restaurante/Trucha.jpg'               WHERE nombre = 'Trucha';
UPDATE producto SET foto = 'assets/img/restaurante/Pato.jpg'                 WHERE nombre = 'Pato';
UPDATE producto SET foto = 'assets/img/restaurante/Gallina.jpg'              WHERE nombre = 'Gallina';