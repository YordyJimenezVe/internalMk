-- ============================================================================
-- REGISTRO DE 26 NUEVAS CÁMARAS Y ACTUALIZACIÓN INTEGRAL DE AÑOS
-- Fecha: 2026-10-29
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- 1. CAMBIO: MODIFICAR LAS 4 CÁMARAS JEEP 3.7 KJ A KK (LADO IZQUIERDO) CON AÑO
-- ----------------------------------------------------------------------------
UPDATE `inventarios`
SET 
  `item` = 'CÁMARA JEEP CHEROKEE KK 3.7L',
  `modelo` = 'CHEROKEE KK 3.7L',
  `observation` = 'LADO IZQUIERDO',
  `año` = '2008',
  `updated_at` = NOW()
WHERE `id` IN (732, 733, 734, 735);

-- ----------------------------------------------------------------------------
-- 2. ASIGNACIÓN DE AÑO A TODAS LAS CÁMARAS EXISTENTES QUE NO TENÍAN AÑO
-- ----------------------------------------------------------------------------
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = 'SANTA FE 2.7' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2010' WHERE tipo = 'CÁMARA' AND modelo = 'CRUZE' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2004' WHERE tipo = 'CÁMARA' AND modelo = 'COLORADO' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2012' WHERE tipo = 'CÁMARA' AND modelo = 'ORLANDO' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = 'ASTRA' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = 'LUV D-MAX 3.5L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2011' WHERE tipo = 'CÁMARA' AND modelo = 'MODELO NUEVO' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2006' WHERE tipo = 'CÁMARA' AND modelo = 'SONATA / SORENTO' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2007' WHERE tipo = 'CÁMARA' AND modelo = '6.0 REF. 0821' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1999' WHERE tipo = 'CÁMARA' AND modelo = '5.3 REF. 882' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2007' WHERE tipo = 'CÁMARA' AND modelo = '6.0 REF. 823' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = '5.3 REF. 243' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = '5.3 REF. 317' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1999' WHERE tipo = 'CÁMARA' AND modelo = '5.3 REF. 862' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1999' WHERE tipo = 'CÁMARA' AND modelo = '5.3 REF. 706' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1991' WHERE tipo = 'CÁMARA' AND modelo = '3000GT 3.0L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = 'GRAND VITARA 6 CILINDROS' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2002' WHERE tipo = 'CÁMARA' AND modelo = 'VQ 3.5L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1995' WHERE tipo = 'CÁMARA' AND modelo = '2RZ' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1988' WHERE tipo = 'CÁMARA' AND modelo = '4A' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1985' WHERE tipo = 'CÁMARA' AND modelo = '22R' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1996' WHERE tipo = 'CÁMARA' AND modelo = '3BZ / 3RZ' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = 'L8J1' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1996' WHERE tipo = 'CÁMARA' AND modelo = '5VZ 3.4L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2000' WHERE tipo = 'CÁMARA' AND modelo = '1NZ' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1992' WHERE tipo = 'CÁMARA' AND modelo = 'CAMRY 2.2' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = 'TUCSON 2.0' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1978' WHERE tipo = 'CÁMARA' AND modelo = '231 MALIBU' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1996' WHERE tipo = 'CÁMARA' AND modelo = '262' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2008' WHERE tipo = 'CÁMARA' AND modelo = 'G4KG' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2011' WHERE tipo = 'CÁMARA' AND modelo = 'EXPLORER MODELO NUEVO' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1998' WHERE tipo = 'CÁMARA' AND modelo = 'VITARA J18' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2000' WHERE tipo = 'CÁMARA' AND modelo = 'TUNDRA' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = 'GRAND VITARA H25' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = 'RANGER / ECOSPORT' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2003' WHERE tipo = 'CÁMARA' AND modelo = 'MAZDA 6' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2006' WHERE tipo = 'CÁMARA' AND modelo = 'G4KC 2.4L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2007' WHERE tipo = 'CÁMARA' AND modelo = 'CAPTIVA V6' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1997' WHERE tipo = 'CÁMARA' AND modelo = '4.6L 2V' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1997' WHERE tipo = 'CÁMARA' AND modelo = '5.4L 2V' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2005' WHERE tipo = 'CÁMARA' AND modelo = '4.6L 3V' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2011' WHERE tipo = 'CÁMARA' AND modelo = 'EXPLORER 3.5L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2001' WHERE tipo = 'CÁMARA' AND modelo = 'ESCAPE 3.0L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2002' WHERE tipo = 'CÁMARA' AND modelo = 'EXPLORER 4.0L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1998' WHERE tipo = 'CÁMARA' AND modelo = 'ESTEEM' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '1996' WHERE tipo = 'CÁMARA' AND modelo = '262 4.3L' AND (año IS NULL OR año = '');
UPDATE inventarios SET año = '2013' WHERE tipo = 'CÁMARA' AND modelo = 'MALIBU 2.5L' AND (año IS NULL OR año = '');

-- ----------------------------------------------------------------------------
-- 3. REGISTRO DE LAS 26 NUEVAS CÁMARAS (IDs 1001 AL 1026)
-- ----------------------------------------------------------------------------
INSERT INTO `inventarios` (
    `id`, `origen`, `item`, `tipo`, `marca`, `modelo`, `serial`, `serial_image_path`, `año`, 
    `codInv`, `expediente`, `condicion`, `status`, `price`, `costo`, `costo_importacion_unitario`, 
    `prorrateo_gastos`, `porcentaje_utilidad`, `tasa_bcv`, `fecha_tasa_bcv`, `price_sale`, 
    `container_id`, `created_at`, `updated_at`, `categorie`, `cantidad`, `observation`
) VALUES
(1001, 'IMPORTADO', 'CÁMARA JEEP CHEROKEE 5.7L 4G', 'CÁMARA', 'JEEP', 'CHEROKEE 5.7L 4G', NULL, NULL, '2009', '1001', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO DERECHO'),
(1002, 'IMPORTADO', 'CÁMARA JEEP CHEROKEE 5.7L 4G', 'CÁMARA', 'JEEP', 'CHEROKEE 5.7L 4G', NULL, NULL, '2009', '1002', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1003, 'IMPORTADO', 'CÁMARA TOYOTA 4A', 'CÁMARA', 'TOYOTA', '4A', NULL, NULL, '1988', '1003', '255095', 'APLICA', 'DISPONIBLE', '15858.24', 40.00, 13215.20, 0.00, 20.00, 330.3800, '2026-01-01', '55.68', 25, NOW(), NOW(), NULL, NULL, NULL),
(1004, 'IMPORTADO', 'CÁMARA FORD SUPER DUTY 6.2L', 'CÁMARA', 'FORD', 'SUPER DUTY 6.2L', NULL, NULL, '2011', '1004', '260723', 'APLICA', 'DISPONIBLE', '57604.75', 60.00, 44311.35, 0.00, 10.00, 736.9330, '2026-07-23', '75.40', 27, NOW(), NOW(), NULL, NULL, NULL),
(1005, 'IMPORTADO', 'CÁMARA FORD SUPER DUTY 6.2L', 'CÁMARA', 'FORD', 'SUPER DUTY 6.2L', NULL, NULL, '2011', '1005', '260723', 'APLICA', 'DISPONIBLE', '57604.75', 60.00, 44311.35, 0.00, 10.00, 736.9330, '2026-07-23', '75.40', 27, NOW(), NOW(), NULL, NULL, NULL),
(1006, 'IMPORTADO', 'CÁMARA FORD SUPER DUTY 6.2L', 'CÁMARA', 'FORD', 'SUPER DUTY 6.2L', NULL, NULL, '2011', '1006', '260723', 'APLICA', 'DISPONIBLE', '57604.75', 60.00, 44311.35, 0.00, 10.00, 736.9330, '2026-07-23', '75.40', 27, NOW(), NOW(), NULL, NULL, NULL),
(1007, 'IMPORTADO', 'CÁMARA CHEVROLET LUV DMAX', 'CÁMARA', 'CHEVROLET', 'LUV DMAX', NULL, NULL, '2005', '1007', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, NULL),
(1008, 'IMPORTADO', 'CÁMARA CHEVROLET LUV DMAX', 'CÁMARA', 'CHEVROLET', 'LUV DMAX', NULL, NULL, '2005', '1008', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, NULL),
(1009, 'IMPORTADO', 'CÁMARA FORD RANGER / ECOSPORT', 'CÁMARA', 'FORD', 'RANGER / ECOSPORT', NULL, NULL, '2005', '1009', '255095', 'APLICA', 'DISPONIBLE', '15858.24', 40.00, 13215.20, 0.00, 20.00, 330.3800, '2026-01-01', '55.68', 25, NOW(), NOW(), NULL, NULL, 'SOLO CAPARAZÓN'),
(1010, 'IMPORTADO', 'CÁMARA FORD FORTALEZA 4.2L 6C', 'CÁMARA', 'FORD', 'FORTALEZA 4.2L 6C', NULL, NULL, '1997', '1010', '260723', 'APLICA', 'DISPONIBLE', '40531.32', 50.00, 36846.65, 0.00, 10.00, 736.9330, '2026-07-23', '63.80', 27, NOW(), NOW(), NULL, NULL, 'LADO DERECHO'),
(1011, 'IMPORTADO', 'CÁMARA FORD FORTALEZA 4.2L 6C', 'CÁMARA', 'FORD', 'FORTALEZA 4.2L 6C', NULL, NULL, '1997', '1011', '260723', 'APLICA', 'DISPONIBLE', '40531.32', 50.00, 36846.65, 0.00, 10.00, 736.9330, '2026-07-23', '63.80', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1012, 'IMPORTADO', 'CÁMARA CHEVROLET 6.0 REF. 823', 'CÁMARA', 'CHEVROLET', '6.0 REF. 823', NULL, NULL, '2007', '1012', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'REFERENCIA 823'),
(1013, 'IMPORTADO', 'CÁMARA CHEVROLET 6.0 REF. 823', 'CÁMARA', 'CHEVROLET', '6.0 REF. 823', NULL, NULL, '2007', '1013', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'REFERENCIA 823'),
(1014, 'IMPORTADO', 'CÁMARA FORD 5.4L 2V', 'CÁMARA', 'FORD', '5.4L 2V', NULL, NULL, '1997', '1014', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1015, 'IMPORTADO', 'CÁMARA FORD 5.4L 2V', 'CÁMARA', 'FORD', '5.4L 2V', NULL, NULL, '1997', '1015', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1016, 'IMPORTADO', 'CÁMARA FORD 4.6L 2V', 'CÁMARA', 'FORD', '4.6L 2V', NULL, NULL, '1997', '1016', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1017, 'IMPORTADO', 'CÁMARA FORD 4.6L 2V', 'CÁMARA', 'FORD', '4.6L 2V', NULL, NULL, '1997', '1017', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1018, 'IMPORTADO', 'CÁMARA TOYOTA TACOMA', 'CÁMARA', 'TOYOTA', 'TACOMA', NULL, NULL, '2005', '1018', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, 'LADO DERECHO'),
(1019, 'IMPORTADO', 'CÁMARA TOYOTA TACOMA', 'CÁMARA', 'TOYOTA', 'TACOMA', NULL, NULL, '2005', '1019', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1020, 'IMPORTADO', 'CÁMARA JEEP CHEROKEE KJ 3.7L', 'CÁMARA', 'JEEP', 'CHEROKEE KJ 3.7L', NULL, NULL, '2002', '1020', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1021, 'IMPORTADO', 'CÁMARA TOYOTA 2GR', 'CÁMARA', 'TOYOTA', '2GR', NULL, NULL, '2006', '1021', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, 'EMPACADURA DE AMIANTO LADO DERECHO'),
(1022, 'IMPORTADO', 'CÁMARA TOYOTA 2GR', 'CÁMARA', 'TOYOTA', '2GR', NULL, NULL, '2006', '1022', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, 'EMPACADURA DE AMIANTO LADO IZQUIERDO'),
(1023, 'IMPORTADO', 'CÁMARA TOYOTA 2AZ', 'CÁMARA', 'TOYOTA', '2AZ', NULL, NULL, '2002', '1023', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, NULL),
(1024, 'IMPORTADO', 'CÁMARA FORD 4.6L 3V', 'CÁMARA', 'FORD', '4.6L 3V', NULL, NULL, '2005', '1024', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO DERECHO'),
(1025, 'IMPORTADO', 'CÁMARA FORD 4.6L 3V', 'CÁMARA', 'FORD', '4.6L 3V', NULL, NULL, '2005', '1025', '260723', 'APLICA', 'DISPONIBLE', '64200.86', 80.00, 58364.42, 0.00, 10.00, 736.9330, '2026-07-23', '101.06', 27, NOW(), NOW(), NULL, NULL, 'LADO IZQUIERDO'),
(1026, 'IMPORTADO', 'CÁMARA FORD EXPLORER 3.5L', 'CÁMARA', 'FORD', 'EXPLORER 3.5L', NULL, NULL, '2011', '1026', '255095', 'APLICA', 'DISPONIBLE', '19822.80', 50.00, 16519.00, 0.00, 20.00, 330.3800, '2026-01-01', '69.60', 25, NOW(), NOW(), NULL, NULL, NULL);

-- ----------------------------------------------------------------------------
-- 4. ACTUALIZACIÓN DEL CONTEO DE CÁMARAS EN LA TABLA CONTAINERS
-- ----------------------------------------------------------------------------
-- Contenedor 25 (CAJU-2025): 125 declaradas + 11 nuevas = 136
UPDATE `containers` SET `camaras` = 136, `updated_at` = NOW() WHERE `id` = 25;

-- Contenedor 27 (SUDU-8941007): 69 declaradas + 15 nuevas = 84
UPDATE `containers` SET `camaras` = 84, `updated_at` = NOW() WHERE `id` = 27;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
