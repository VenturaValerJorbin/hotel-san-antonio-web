-- =====================================================================
-- Fotos de habitaciones - Hotel San Antonio
-- Ejecutar DESPUES de haber importado daw2_hotel_san_antonio.sql
-- phpMyAdmin -> tu base de datos -> pestaña SQL -> pegar y ejecutar
-- =====================================================================

DELETE FROM foto_tipo_habitacion;

-- tipo_id 1 = Simple                       -> assets/img/economic-room/
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(1, 'assets/img/economic-room/economic-room1.avif', 1),
(1, 'assets/img/economic-room/economic-room2.avif', 2),
(1, 'assets/img/economic-room/economic-room3.avif', 3),
(1, 'assets/img/economic-room/economic-room4.avif', 4),
(1, 'assets/img/economic-room/economic-room5.avif', 5);

-- tipo_id 2 = Ejecutiva sin agua caliente  -> assets/img/deluxe/
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

-- tipo_id 4 = Matrimonial                  -> assets/img/deluxe-queen/
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(4, 'assets/img/deluxe-queen/habitacion_queen1.avif', 1),
(4, 'assets/img/deluxe-queen/habitacion_queen2.avif', 2),
(4, 'assets/img/deluxe-queen/habitacion_queen3.avif', 3),
(4, 'assets/img/deluxe-queen/habitacion_queen4.avif', 4);

-- tipo_id 5 = Doble                        -> assets/img/doble/
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(5, 'assets/img/doble/habitacion-doble1.avif', 1),
(5, 'assets/img/doble/habitacion-doble2.avif', 2),
(5, 'assets/img/doble/habitacion-doble3.avif', 3),
(5, 'assets/img/doble/habitacion-doble4.avif', 4),
(5, 'assets/img/doble/habitacion-doble5.avif', 5),
(5, 'assets/img/doble/habitacion-doble6.avif', 6),
(5, 'assets/img/doble/habitacion-doble7.avif', 7),
(5, 'assets/img/doble/habitacion-doble8.avif', 8),
(5, 'assets/img/doble/habitacion-doble9.avif', 9),
(5, 'assets/img/doble/habitacion-doble10.avif', 10),
(5, 'assets/img/doble/habitacion-doble11.avif', 11),
(5, 'assets/img/doble/habitacion-doble12.avif', 12),
(5, 'assets/img/doble/habitacion-doble13.avif', 13),
(5, 'assets/img/doble/habitacion-doble14.avif', 14),
(5, 'assets/img/doble/habitacion-doble15.avif', 15),
(5, 'assets/img/doble/habitacion-doble16.avif', 16),
(5, 'assets/img/doble/habitacion-doble17.avif', 17),
(5, 'assets/img/doble/habitacion-doble18.avif', 18);

-- tipo_id 6 = Suite                        -> assets/img/deluxe-suite/
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(6, 'assets/img/deluxe-suite/1deluxe-suite.avif', 1),
(6, 'assets/img/deluxe-suite/2deluxe-suite.avif', 2),
(6, 'assets/img/deluxe-suite/3deluxe-suite.avif', 3);

-- tipo_id 7 = King                         -> assets/img/junior-suite/
INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(7, 'assets/img/junior-suite/unior-suite1.avif', 1),
(7, 'assets/img/junior-suite/unior-suite2.avif', 2),
(7, 'assets/img/junior-suite/unior-suite3.avif', 3),
(7, 'assets/img/junior-suite/unior-suite4.avif', 4),
(7, 'assets/img/junior-suite/unior-suite5.avif', 5),
(7, 'assets/img/junior-suite/unior-suite6.avif', 6),
(7, 'assets/img/junior-suite/unior-suite7.avif', 7);


INSERT INTO foto_tipo_habitacion (tipo_id, ruta, orden) VALUES
(3, 'assets/img/deluxe/hiabitacion_deluxe1.avif', 1),
(3, 'assets/img/deluxe/hiabitacion_deluxe2.avif', 2),
(3, 'assets/img/deluxe/hiabitacion_deluxe3.avif', 3),
(3, 'assets/img/deluxe/hiabitacion_deluxe4.avif', 4),
(3, 'assets/img/deluxe/hiabitacion_deluxe5.avif', 5),
(3, 'assets/img/deluxe/hiabitacion_deluxe6.avif', 6),
(3, 'assets/img/deluxe/hiabitacion_deluxe7.avif', 7),
(3, 'assets/img/deluxe/hiabitacion_deluxe8.avif', 8),
(3, 'assets/img/deluxe/9hiabitacion_deluxe.avif', 9);