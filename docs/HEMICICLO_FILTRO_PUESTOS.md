# Filtrado de Puestos en el Hemiciclo

## Cambio Implementado

Se modificó la visualización del hemiciclo para mostrar **solo** ciertos puestos democráticos, ocultando otros roles administrativos.

## Reglas de Visualización

### Posición #1 (Presidencial - Arriba)
**Solo pueden aparecer:**
- ✅ **Presidente** (prioridad absoluta)
- ✅ **Vice Presidente** (solo si Presidente ausente)
- ❌ Ningún otro puesto puede ocupar esta posición

### Posiciones #2-7 (Semicírculo)
**Solo pueden aparecer:**
- ✅ **Concejales** únicamente
- ❌ Secretario NO visible
- ❌ Pro Secretario NO visible
- ❌ Cualquier otro puesto NO visible

## Puestos Ocultos

Los siguientes puestos **NO** se muestran en el hemiciclo:
- Secretario
- Pro Secretario
- Usuarios sin puesto asignado
- Cualquier otro puesto personalizado

## Ejemplo Visual

```
     [P]  ← Presidente o Vice Presidente
   /       \
[C] [C] [C] [C] [C] [C] ← Solo Concejales

Leyenda:
P = Presidente/Vice Presidente
C = Concejal
```

## Lógica Implementada

### PHP (Renderizado Inicial)
```php
// Separar miembros por puesto
if ($miembro['puesto'] === 'Presidente') {
    $presidente = $miembro;
} elseif ($miembro['puesto'] === 'Vice Presidente') {
    $vicePresidente = $miembro;
} elseif ($miembro['puesto'] === 'Concejal') {
    $concejales[] = $miembro;
}
// Los demás puestos NO se agregan a ningún array
```

### JavaScript (Actualizaciones Tiempo Real)
```javascript
// Misma lógica aplicada en las actualizaciones cada 10 segundos
if (miembro.puesto === 'Presidente') {
    presidente = miembro;
} else if (miembro.puesto === 'Vice Presidente') {
    vicePresidente = miembro;
} else if (miembro.puesto === 'Concejal') {
    concejales.push(miembro);
}
// Los demás puestos NO se agregan
```

## Contadores Actualizados

Los contadores ahora reflejan solo los miembros visibles:

- **Presentes**: Cuenta solo Presidente/Vice + Concejales visibles
- **Ausentes**: Calcula basándose en 7 posiciones - miembros visibles
- **Total**: Suma de Presidente (0 o 1) + Vice (0 o 1) + Concejales

## Archivos Modificados

- `/app/views/votacion/vista_publica.php`
  - Lógica PHP de filtrado (líneas ~1905-1945)
  - Lógica JavaScript de filtrado (líneas ~2450-2480)
  - Contadores de leyenda (líneas ~2020-2030, ~2595-2605)
  - Badge de información (líneas ~2610-2625)

## Casos de Uso

### Caso 1: Presidente + 5 Concejales presentes
```
Posición #1: Presidente
Posiciones #2-6: 5 Concejales
Posición #7: Vacante
Contador: 6 presentes, 1 ausente
```

### Caso 2: Solo Vice Presidente + 3 Concejales presentes
```
Posición #1: Vice Presidente
Posiciones #2-4: 3 Concejales
Posiciones #5-7: Vacantes
Contador: 4 presentes, 3 ausentes
```

### Caso 3: Secretario presente (pero no Presidente ni Vice)
```
Posición #1: Vacante (Secretario NO se muestra)
Posiciones #2-7: Vacantes o Concejales
El Secretario NO aparece en ninguna posición
```

## Beneficios

✅ **Claridad democrática**: Solo se muestran autoridades electas
✅ **Jerarquía visual**: Presidente/Vice presidiendo, Concejales en hemiciclo
✅ **Roles administrativos ocultos**: Secretarios no confunden la visualización
✅ **Consistencia**: Misma lógica en carga inicial y actualizaciones

## Notas Importantes

- Los usuarios con puestos ocultos **siguen estando** en la sesión
- Pueden votar normalmente si tienen el rol correcto (Editor)
- Solo afecta la **visualización pública** del hemiciclo
- El contador de quórum sigue usando el total real de presentes (no solo visibles)