-- Limpiar valores de puesto con espacios y saltos de línea
-- Compatible con MySQL en servidor compartido DonWeb
-- Este script elimina espacios en blanco, saltos de línea y caracteres invisibles

-- IMPORTANTE: Hacer backup antes de ejecutar
-- mysqldump -u usuario -p base_datos users > backup_users.sql

-- Paso 1: Limpiar saltos de línea (CHAR(10) = \n, CHAR(13) = \r) y tabulaciones (CHAR(9))
UPDATE users 
SET puesto = TRIM(REPLACE(REPLACE(REPLACE(puesto, CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) 
WHERE puesto IS NOT NULL;

-- Paso 2: Verificar los valores después de la limpieza
SELECT id, username, CONCAT('[', puesto, ']') as puesto_limpio, first_name, last_name 
FROM users 
WHERE puesto IS NOT NULL AND puesto != ''
ORDER BY id;

-- Paso 3: Normalizar valores específicos (asegurar consistencia)
UPDATE users SET puesto = 'Presidente' 
WHERE puesto IS NOT NULL 
  AND puesto != '' 
  AND puesto LIKE '%Presidente%' 
  AND puesto NOT LIKE '%Vice%';

UPDATE users SET puesto = 'Vice Presidente' 
WHERE puesto IS NOT NULL 
  AND puesto != '' 
  AND puesto LIKE '%Vice%';

UPDATE users SET puesto = 'Concejal' 
WHERE puesto IS NOT NULL 
  AND puesto != '' 
  AND puesto LIKE '%Concejal%';

UPDATE users SET puesto = 'Secretario' 
WHERE puesto IS NOT NULL 
  AND puesto != '' 
  AND puesto LIKE '%Secretario%' 
  AND puesto NOT LIKE '%Pro%';

UPDATE users SET puesto = 'Pro Secretario' 
WHERE puesto IS NOT NULL 
  AND puesto != '' 
  AND puesto LIKE '%Pro%Secretario%';

-- Paso 4: Limpiar valores vacíos (convertir a NULL)
UPDATE users SET puesto = NULL WHERE puesto = '' OR puesto = ' ';

-- Paso 5: Resultado final ordenado por jerarquía
SELECT 
    id, 
    username, 
    puesto, 
    first_name, 
    last_name,
    CASE puesto
        WHEN 'Presidente' THEN '1-Presidente'
        WHEN 'Vice Presidente' THEN '2-Vice Presidente'
        WHEN 'Concejal' THEN '3-Concejal'
        WHEN 'Secretario' THEN '4-Secretario'
        WHEN 'Pro Secretario' THEN '5-Pro Secretario'
        ELSE '6-Otro'
    END as orden_jerarquico
FROM users 
ORDER BY orden_jerarquico, id;