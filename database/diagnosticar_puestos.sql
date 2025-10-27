-- Script de diagnóstico para verificar valores de puesto
-- Compatible con MySQL en servidor compartido DonWeb
-- Este script te ayudará a identificar problemas con espacios y caracteres invisibles

-- 1. Ver todos los puestos con su longitud (para detectar espacios extras)
SELECT 
    id,
    username,
    CONCAT('[', IFNULL(puesto, 'NULL'), ']') as puesto_con_corchetes,
    LENGTH(puesto) as longitud_puesto,
    CHAR_LENGTH(puesto) as caracteres_puesto,
    first_name,
    last_name
FROM users
WHERE puesto IS NOT NULL AND puesto != ''
ORDER BY id;

-- 2. Buscar caracteres no visibles (saltos de línea, tabulaciones, etc.)
-- Nota: HEX puede tener límites en algunos servidores compartidos
SELECT 
    id,
    username,
    puesto,
    first_name,
    last_name,
    LENGTH(puesto) as longitud,
    LENGTH(TRIM(puesto)) as longitud_sin_espacios
FROM users
WHERE puesto IS NOT NULL AND puesto != '';

-- 3. Detectar puestos con espacios o saltos de línea
SELECT 
    id,
    username,
    puesto,
    CASE 
        WHEN puesto LIKE '% ' THEN 'Espacio al final'
        WHEN puesto LIKE ' %' THEN 'Espacio al inicio'
        WHEN puesto LIKE '%
%' THEN 'Salto de línea'
        WHEN LENGTH(puesto) != LENGTH(TRIM(puesto)) THEN 'Espacios extras'
        ELSE 'OK'
    END as problema_detectado,
    first_name,
    last_name
FROM users
WHERE puesto IS NOT NULL AND puesto != '';

-- 4. Lista de usuarios con puestos problemáticos
SELECT 
    id,
    username,
    CONCAT('[', puesto, ']') as puesto_original,
    CONCAT('[', TRIM(REPLACE(REPLACE(puesto, CHAR(13), ''), CHAR(10), '')) , ']') as puesto_limpio,
    first_name,
    last_name
FROM users
WHERE puesto IS NOT NULL 
  AND puesto != ''
  AND (LENGTH(puesto) != LENGTH(TRIM(puesto)) 
       OR puesto LIKE '%
%'
       OR puesto LIKE '%	%');

-- 5. Lista de puestos únicos
SELECT 
    TRIM(REPLACE(REPLACE(puesto, CHAR(13), ''), CHAR(10), '')) as puesto_limpio,
    COUNT(*) as cantidad
FROM users
WHERE puesto IS NOT NULL AND puesto != ''
GROUP BY puesto_limpio
ORDER BY cantidad DESC;