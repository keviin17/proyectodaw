-- ============================================================
-- VELORA SHOP — Nuevos productos (15 por sección)
-- Ejecutar DESPUÉS del script principal velora_shop.sql
-- ============================================================

-- Primero añadimos más categorías para tener variedad
INSERT INTO `categoria` (`nombre`, `descripcion`, `genero`, `activo`) VALUES
('Chaquetas Hombre',  'Chaquetas y abrigos para hombre',     'hombre', 1),
('Calzado Hombre',    'Zapatillas y zapatos para hombre',    'hombre', 1),
('Tops Mujer',        'Tops, blusas y camisas para mujer',   'mujer',  1),
('Pantalones Mujer',  'Pantalones y leggings para mujer',    'mujer',  1),
('Calzado Niño',      'Zapatillas y zapatos infantiles',     'niño',   1),
('Pantalones Niño',   'Pantalones y joggers infantiles',     'niño',   1);

-- ── HOMBRE (15 productos) ─────────────────────────────────
INSERT INTO `producto` (`nombre`, `descripcion`, `precio`, `precio_oferta`, `stock`, `imagen`, `id_categoria`, `destacado`, `activo`, `talla`, `color`) VALUES
('Camiseta Rayas Marineras',   'Camiseta clásica de rayas azul y blanco, algodón 100%', 22.99,  NULL,  35, NULL, 1, 0, 1, 'M,L,XL',     'Azul/Blanco'),
('Camiseta Estampado Geométrico','Camiseta con print geométrico minimalista',            26.99,  NULL,  28, NULL, 1, 1, 1, 'S,M,L',      'Negro'),
('Camiseta Polo Clásica',      'Polo de piqué con cuello, corte slim',                  34.99,  NULL,  40, NULL, 1, 0, 1, 'S,M,L,XL',   'Blanco'),
('Camiseta Tie-Dye Premium',   'Camiseta teñida a mano, edición limitada',              29.99,  24.99, 15, NULL, 1, 1, 1, 'M,L',        'Multicolor'),
('Camiseta Manga Larga Básica','Camiseta de manga larga en algodón suave',              21.99,  NULL,  45, NULL, 1, 0, 1, 'S,M,L,XL,XXL','Gris'),
('Pantalón Chino Beige',       'Chino slim fit en beige, tejido resistente',            44.99,  NULL,  30, NULL, 2, 1, 1, '30,32,34,36', 'Beige'),
('Vaquero Baggy 90s',          'Vaquero de corte ancho, tendencia retro',               59.99,  NULL,  22, NULL, 2, 1, 1, '28,30,32,34', 'Azul claro'),
('Pantalón Jogger Técnico',    'Jogger con cintura elástica, tejido técnico',           39.99,  34.99, 38, NULL, 2, 0, 1, 'S,M,L,XL',   'Negro'),
('Shorts Deportivos',          'Bermuda deportiva con bolsillos laterales',             24.99,  NULL,  50, NULL, 2, 0, 1, 'S,M,L,XL',   'Gris oscuro'),
('Pantalón Cargo Urbano',      'Cargo con múltiples bolsillos, estilo urbano',          54.99,  NULL,  18, NULL, 2, 1, 1, '30,32,34',   'Verde caqui'),
('Chaqueta Denim Clásica',     'Chaqueta vaquera de corte regular, lavado oscuro',      79.99,  NULL,  20, NULL, 6, 1, 1, 'S,M,L,XL',   'Azul oscuro'),
('Bomber Universitaria',       'Bomber universitaria con ribetes en contraste',         89.99,  74.99, 12, NULL, 6, 1, 1, 'M,L,XL',     'Verde/Negro'),
('Sudadera Hoodie Oversize',   'Sudadera con capucha, corte oversized',                 49.99,  NULL,  35, NULL, 6, 0, 1, 'M,L,XL,XXL', 'Gris claro'),
('Zapatillas Runner Pro',      'Zapatillas de running con amortiguación extra',         89.99,  NULL,  25, NULL, 7, 1, 1, '40,41,42,43,44','Blanco/Negro'),
('Sneakers Retro 80s',         'Zapatillas estilo retro, suela vulcanizada',            69.99,  59.99, 30, NULL, 7, 1, 1, '39,40,41,42,43','Blanco');

-- ── MUJER (15 productos) ─────────────────────────────────
INSERT INTO `producto` (`nombre`, `descripcion`, `precio`, `precio_oferta`, `stock`, `imagen`, `id_categoria`, `destacado`, `activo`, `talla`, `color`) VALUES
('Camiseta Crop Tie-Dye',      'Crop top tie-dye, perfect fit para verano',             19.99,  NULL,  40, NULL, 3, 1, 1, 'XS,S,M,L',   'Rosa/Lila'),
('Top Lencero Satinado',       'Top estilo lencero en satén, elegante y versátil',      29.99,  NULL,  25, NULL, 3, 1, 1, 'XS,S,M',     'Champán'),
('Camiseta Manga Globo',       'Camiseta con manga globo, estilo romántico',             27.99,  NULL,  30, NULL, 3, 0, 1, 'S,M,L',      'Blanco'),
('Top Deportivo Seamless',     'Top deportivo sin costuras, tejido de alto rendimiento',24.99,  19.99, 45, NULL, 9, 0, 1, 'XS,S,M,L',   'Negro'),
('Blusa Bohemia Estampada',    'Blusa suelta con estampado floral y lazada',             34.99,  NULL,  22, NULL, 9, 1, 1, 'S,M,L,XL',   'Multicolor'),
('Vestido Midi Flores',        'Vestido midi con estampado de flores, tirantes finos',  64.99,  NULL,  18, NULL, 4, 1, 1, 'XS,S,M,L',   'Verde flores'),
('Vestido Mini Punto',         'Vestido corto de punto acanalado, muy ceñido',          49.99,  44.99, 20, NULL, 4, 1, 1, 'XS,S,M',     'Camel'),
('Vestido Camisero Denim',     'Vestido tipo camisa vaquera con cinturón incluido',     59.99,  NULL,  15, NULL, 4, 0, 1, 'S,M,L',      'Azul denim'),
('Vestido Asimétrico Noche',   'Vestido de noche con escote asimétrico, elegante',      89.99,  NULL,  10, NULL, 4, 1, 1, 'XS,S,M,L',   'Negro'),
('Pantalón Palazzo Fluido',    'Palazzo de tejido fluido, cintura alta',                44.99,  NULL,  28, NULL, 10, 1, 1, 'XS,S,M,L',   'Terracota'),
('Legging Sport Efecto Push-Up','Legging deportivo con efecto push-up y compresión',    34.99,  29.99, 50, NULL, 10, 0, 1, 'XS,S,M,L,XL','Negro'),
('Pantalón Mom Jeans',         'Mom jeans con lavado vintage, tiro alto',               54.99,  NULL,  25, NULL, 10, 1, 1, '36,38,40,42','Azul lavado'),
('Pantalón Cargo Wide Leg',    'Cargo de pierna ancha con múltiples bolsillos',         49.99,  NULL,  20, NULL, 10, 0, 1, '36,38,40',   'Caqui'),
('Shorts Vaqueros Bordados',   'Shorts denim con bordado floral en los bolsillos',      39.99,  34.99, 22, NULL, 10, 1, 1, '36,38,40,42','Azul'),
('Falda Midi Plisada',         'Falda midi con pliegues, tejido satinado',              44.99,  NULL,  18, NULL, 4, 1, 1, 'XS,S,M,L',   'Crema');

-- ── NIÑO (15 productos) ──────────────────────────────────
INSERT INTO `producto` (`nombre`, `descripcion`, `precio`, `precio_oferta`, `stock`, `imagen`, `id_categoria`, `destacado`, `activo`, `talla`, `color`) VALUES
('Camiseta Unicornio Brillante','Camiseta con estampado de unicornio con purpurina',    14.99,  NULL,  55, NULL, 5, 1, 1, '4,6,8,10,12','Rosa'),
('Camiseta Superhéroe',        'Camiseta con capa simulada integrada',                  16.99,  NULL,  48, NULL, 5, 1, 1, '4,6,8,10',   'Azul'),
('Camiseta Mundo Espacial',    'Estampado de planetas y astronautas, fosforescente',    15.99,  NULL,  40, NULL, 5, 0, 1, '6,8,10,12',  'Negro'),
('Camiseta Fútbol Personalizable','Camiseta técnica estilo fútbol, transpirable',       17.99,  NULL,  60, NULL, 5, 0, 1, '6,8,10,12,14','Rojo/Blanco'),
('Camiseta Tie-Dye Niño',      'Camiseta tie-dye de colores vibrantes',                13.99,  11.99, 35, NULL, 5, 0, 1, '4,6,8,10',   'Multicolor'),
('Conjunto Jogger Estampado',  'Conjunto de sudadera y pantalón a juego',               29.99,  NULL,  30, NULL, 5, 1, 1, '4,6,8,10,12','Gris/Azul'),
('Pantalón Vaquero Elástico',  'Vaquero con cintura elástica, cómodo para jugar',       24.99,  NULL,  35, NULL, 11, 0, 1, '4,6,8,10,12','Azul'),
('Pantalón Cargo Niño',        'Cargo con bolsillos laterales, tejido resistente',      22.99,  NULL,  28, NULL, 11, 0, 1, '6,8,10,12',  'Verde'),
('Legging Deportivo Niña',     'Legging técnico para deporte escolar y extraescolar',   15.99,  13.99, 45, NULL, 11, 0, 1, '4,6,8,10,12','Negro/Rosa'),
('Pantalón Chandal con Rayas', 'Chándal con rayas laterales, muy cómodo',               19.99,  NULL,  40, NULL, 11, 1, 1, '4,6,8,10,12','Azul marino'),
('Shorts Estampado Tropical',  'Bermuda con estampado tropical, para el verano',        14.99,  NULL,  50, NULL, 11, 0, 1, '4,6,8,10',   'Multicolor'),
('Chaqueta Impermeable',       'Chubasquero impermeable con bolsillos y capucha',       34.99,  NULL,  20, NULL, 5, 1, 1, '4,6,8,10,12','Amarillo'),
('Vestido Tutú Tul',           'Vestido con falda de tul, perfecto para fiestas',       27.99,  NULL,  18, NULL, 5, 1, 1, '2,4,6,8',    'Rosa fucsia'),
('Pijama Estrellas',           'Pijama dos piezas con estampado de estrellas',          21.99,  18.99, 42, NULL, 5, 0, 1, '2,4,6,8,10', 'Azul cielo'),
('Zapatillas Flash Runner',    'Zapatillas con luces LED en la suela',                  39.99,  NULL,  25, NULL, 12, 1, 1, '24,26,28,30,32','Blanco/Azul');
