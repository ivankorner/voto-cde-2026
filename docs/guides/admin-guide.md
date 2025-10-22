# 👑 Guía del Administrador - Sistema de Votación

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Dashboard de Administrador](#dashboard-de-administrador)
4. [Gestión de Usuarios](#gestión-de-usuarios)
5. [Gestión de Roles](#gestión-de-roles)
6. [Configuraciones del Sistema](#configuraciones-del-sistema)
7. [Buenas Prácticas](#buenas-prácticas)
8. [Solución de Problemas](#solución-de-problemas)

---

## 🚀 Introducción

Como **Administrador** del Sistema de Votación, tienes acceso completo a todas las funcionalidades del sistema. Puedes gestionar usuarios, roles, configuraciones y monitorear la actividad del sistema.

### 🔑 Responsabilidades del Administrador

- ✅ **Gestión completa de usuarios** (crear, editar, eliminar)
- ✅ **Administración de roles y permisos**
- ✅ **Configuración del sistema**
- ✅ **Monitoreo de actividad**
- ✅ **Mantenimiento de seguridad**
- ✅ **Backup y recuperación de datos**

---

## 🔐 Acceso al Sistema

### Credenciales de Administrador
- **URL:** `http://localhost/voto/`
- **Usuario:** `admin`
- **Contraseña:** `Estatanteria12022`

### Proceso de Login
1. **Abrir** navegador web
2. **Navegar** a `http://localhost/voto/`
3. **Ingresar** credenciales de administrador
4. **Hacer clic** en "Iniciar Sesión"

```
┌─────────────────────────────────────┐
│         Iniciar Sesión              │
├─────────────────────────────────────┤
│ Usuario: [admin                  ]  │
│ Contraseña: [•••••••••••••••••••]   │
│                                     │
│     [  Iniciar Sesión  ]            │
└─────────────────────────────────────┘
```

---

## 🏠 Dashboard de Administrador

### Vista General del Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ 🏠 Dashboard - Sistema de Votación                          │
├─────────────────────────────────────────────────────────────┤
│ Menú: [Dashboard] [Usuarios] [Roles]    [👤 Admin ▼]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ 📊 Estadísticas del Sistema                                 │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │Total Usuarios│ │Usuarios Act.│ │Total Roles  │            │
│ │      3       │ │      3      │ │      3      │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
│                                                             │
│ 🔗 Accesos Rápidos                                         │
│ [➕ Nuevo Usuario] [⚙️ Gestionar Roles] [📊 Reportes]       │
│                                                             │
│ 📈 Actividad Reciente                                       │
│ • Usuario 'editor1' se conectó - hace 2 horas              │
│ • Nuevo usuario 'viewer1' creado - hace 1 día              │
│ • Configuración actualizada - hace 2 días                  │
└─────────────────────────────────────────────────────────────┘
```

### Elementos del Dashboard

#### 📊 Estadísticas Principales
- **Total de Usuarios**: Número total de usuarios registrados
- **Usuarios Activos**: Usuarios con estado "activo"
- **Total de Roles**: Número de roles configurados
- **Última Actividad**: Registro de acciones recientes

#### 🔗 Accesos Rápidos
- **Nuevo Usuario**: Crear usuario directamente
- **Gestionar Roles**: Acceso rápido a roles
- **Reportes**: Ver estadísticas detalladas

---

## 👥 Gestión de Usuarios

### Lista de Usuarios

```
┌─────────────────────────────────────────────────────────────┐
│ 👥 Gestión de Usuarios                                       │
├─────────────────────────────────────────────────────────────┤
│ [➕ Nuevo Usuario]                           [🔍 Buscar...]  │
├─────────────────────────────────────────────────────────────┤
│ │ID│ Usuario │    Email         │   Rol   │ Estado │ Acción│
│ ├──┼─────────┼──────────────────┼─────────┼────────┼───────┤
│ │1 │ admin   │admin@sistema.com │ Admin   │ Activo │[✏️][🗑️]│
│ │2 │ editor1 │editor@sistema.com│ Editor  │ Activo │[✏️][🗑️]│
│ │3 │ viewer1 │viewer@sistema.com│ Viewer  │ Activo │[✏️][🗑️]│
└─────────────────────────────────────────────────────────────┘
```

### Crear Nuevo Usuario

#### Pasos para Crear Usuario
1. **Hacer clic** en "➕ Nuevo Usuario"
2. **Completar** formulario:
   - Nombre de usuario (único)
   - Email (único)
   - Contraseña
   - Nombre y apellido
   - Rol
   - Estado (Activo/Inactivo)
3. **Hacer clic** en "Guardar"

#### Formulario de Creación
```
┌─────────────────────────────────────┐
│        Crear Nuevo Usuario          │
├─────────────────────────────────────┤
│ Usuario*:     [________________]    │
│ Email*:       [________________]    │
│ Contraseña*:  [________________]    │
│ Nombre*:      [________________]    │
│ Apellido*:    [________________]    │
│ Rol*:         [Admin ▼]             │
│ Estado:       [☑️ Activo]            │
│                                     │
│ [Cancelar]          [Guardar]       │
└─────────────────────────────────────┘
```

### Editar Usuario

#### Proceso de Edición
1. **Hacer clic** en "✏️" junto al usuario
2. **Modificar** campos necesarios
3. **Guardar** cambios

#### Campos Editables
- ✅ Nombre de usuario
- ✅ Email
- ✅ Nombre y apellido
- ✅ Rol asignado
- ✅ Estado (activo/inactivo)
- ⚠️ Contraseña (opcional)

### Eliminar Usuario

#### Pasos para Eliminar
1. **Hacer clic** en "🗑️" junto al usuario
2. **Confirmar** eliminación
3. El usuario será **eliminado permanentemente**

⚠️ **Importante**: No puedes eliminar tu propio usuario (admin)

---

## 🛡️ Gestión de Roles

### Lista de Roles

```
┌─────────────────────────────────────────────────────────────┐
│ 🛡️ Gestión de Roles                                         │
├─────────────────────────────────────────────────────────────┤
│ [➕ Nuevo Rol]                               [🔍 Buscar...]  │
├─────────────────────────────────────────────────────────────┤
│ │ID│   Nombre   │      Descripción           │ Estado │Acción│
│ ├──┼────────────┼────────────────────────────┼────────┼──────┤
│ │1 │ admin      │ Administrador completo     │ Activo │[✏️][🗑️]│
│ │2 │ editor     │ Editor de contenido        │ Activo │[✏️][🗑️]│
│ │3 │ viewer     │ Solo lectura               │ Activo │[✏️][🗑️]│
└─────────────────────────────────────────────────────────────┘
```

### Tipos de Roles Predefinidos

#### 🔴 Administrador (admin)
- **Descripción**: Acceso completo al sistema
- **Permisos**:
  - ✅ Dashboard
  - ✅ Gestión de usuarios
  - ✅ Gestión de roles
  - ✅ Configuraciones del sistema

#### 🟡 Editor (editor)
- **Descripción**: Editor de contenido con acceso limitado
- **Permisos**:
  - ✅ Dashboard
  - ❌ Gestión de usuarios
  - ❌ Gestión de roles

#### 🟢 Viewer (viewer)
- **Descripción**: Solo lectura del sistema
- **Permisos**:
  - ✅ Dashboard (solo lectura)
  - ❌ Gestión de usuarios
  - ❌ Gestión de roles

### Crear Nuevo Rol

#### Formulario de Creación de Rol
```
┌─────────────────────────────────────┐
│         Crear Nuevo Rol             │
├─────────────────────────────────────┤
│ Nombre*:      [________________]    │
│ Descripción*: [________________]    │
│               [________________]    │
│ Estado:       [☑️ Activo]            │
│                                     │
│ Permisos:                           │
│ ☑️ Dashboard                         │
│ ☐ Gestión de usuarios               │
│ ☐ Gestión de roles                  │
│ ☐ Configuraciones                   │
│                                     │
│ [Cancelar]          [Guardar]       │
└─────────────────────────────────────┘
```

---

## ⚙️ Configuraciones del Sistema

### Panel de Configuraciones

#### Configuraciones Generales
```
┌─────────────────────────────────────┐
│     Configuraciones del Sistema     │
├─────────────────────────────────────┤
│ Nombre del Sistema:                 │
│ [Sistema de Votación           ]    │
│                                     │
│ URL Base:                           │
│ [http://localhost/voto/        ]    │
│                                     │
│ Zona Horaria:                       │
│ [America/Santiago ▼]                │
│                                     │
│ [Guardar Configuración]             │
└─────────────────────────────────────┘
```

#### Configuraciones de Seguridad
```
┌─────────────────────────────────────┐
│      Seguridad del Sistema          │
├─────────────────────────────────────┤
│ Tiempo de sesión (minutos):         │
│ [30                            ]    │
│                                     │
│ Intentos de login máximos:          │
│ [5                             ]    │
│                                     │
│ ☑️ Requerir contraseñas seguras      │
│ ☑️ Habilitar logs de actividad       │
│ ☐ Autenticación de dos factores     │
│                                     │
│ [Aplicar Configuración]             │
└─────────────────────────────────────┘
```

---

## 📋 Buenas Prácticas

### Gestión de Usuarios

#### ✅ Recomendaciones
- **Revisar regularmente** la lista de usuarios activos
- **Usar contraseñas seguras** para todos los usuarios
- **Asignar roles apropiados** según responsabilidades
- **Desactivar** usuarios que ya no necesitan acceso
- **Hacer backup** antes de eliminar usuarios

#### ❌ Evitar
- No eliminar usuarios con datos históricos importantes
- No compartir credenciales de administrador
- No asignar permisos de admin innecesariamente
- No usar contraseñas predecibles

### Gestión de Roles

#### ✅ Mejores Prácticas
- **Crear roles específicos** para diferentes funciones
- **Asignar permisos mínimos** necesarios (principio de menor privilegio)
- **Documentar** qué hace cada rol
- **Revisar permisos** regularmente

### Seguridad

#### 🔒 Medidas de Seguridad
- **Cambiar contraseñas** regularmente
- **Monitorear logs** de actividad
- **Hacer backup** de la base de datos
- **Mantener actualizado** el sistema
- **Verificar accesos** sospechosos

---

## 🆘 Solución de Problemas

### Problemas Comunes

#### ❌ No puedo crear usuarios
**Posibles causas:**
- Email o username ya existe
- Campos obligatorios vacíos
- Problemas de conexión a BD

**Soluciones:**
1. Verificar que email y username sean únicos
2. Completar todos los campos obligatorios
3. Revisar conexión a base de datos

#### ❌ Usuario no puede iniciar sesión
**Posibles causas:**
- Contraseña incorrecta
- Usuario inactivo
- Problema de sesión

**Soluciones:**
1. Verificar estado del usuario (debe estar activo)
2. Resetear contraseña si es necesario
3. Verificar que el rol tenga permisos adecuados

#### ❌ Error al eliminar usuario
**Posibles causas:**
- Intentando eliminar usuario propio
- Usuario tiene datos relacionados
- Problema de permisos

**Soluciones:**
1. No puedes eliminar tu propio usuario
2. Desactivar usuario en lugar de eliminar
3. Verificar permisos de administrador

### Comandos de Diagnóstico

#### Verificar Estado del Sistema
```sql
-- Verificar usuarios activos
SELECT COUNT(*) as usuarios_activos 
FROM users 
WHERE status = 'active';

-- Verificar roles disponibles
SELECT * FROM roles WHERE status = 'active';

-- Ver última actividad
SELECT username, last_login 
FROM users 
ORDER BY last_login DESC 
LIMIT 5;
```

### Contacto de Soporte

#### 📞 Información de Soporte
- **Email técnico**: admin@sistema.com
- **Documentación**: `/docs/README.md`
- **Logs del sistema**: `/logs/`

---

## 📝 Lista de Verificación del Administrador

### Tareas Diarias
- [ ] Revisar usuarios conectados
- [ ] Verificar logs de errores
- [ ] Comprobar actividad sospechosa

### Tareas Semanales
- [ ] Backup de base de datos
- [ ] Revisar usuarios inactivos
- [ ] Actualizar documentación

### Tareas Mensuales
- [ ] Auditoría de permisos
- [ ] Limpieza de logs antiguos
- [ ] Revisión de configuraciones

---

*Guía del Administrador - Sistema de Votación v1.0.0*