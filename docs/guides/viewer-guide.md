# 👁️ Guía del Viewer - Sistema de Votación

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Dashboard del Viewer](#dashboard-del-viewer)
4. [Funcionalidades de Solo Lectura](#funcionalidades-de-solo-lectura)
5. [Navegación del Sistema](#navegación-del-sistema)
6. [Limitaciones del Rol](#limitaciones-del-rol)
7. [Buenas Prácticas](#buenas-prácticas)
8. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## 🚀 Introducción

Como **Viewer** del Sistema de Votación, tienes acceso de **solo lectura** a la información del sistema. Tu rol está diseñado para permitir la consulta y visualización de datos sin capacidad de modificación.

### 🔑 Características del Rol Viewer

- ✅ **Acceso de solo lectura** al dashboard
- ✅ **Visualización de información** general del sistema
- ✅ **Consulta de estadísticas** básicas
- ❌ **NO edición** de ningún tipo
- ❌ **NO gestión de usuarios**
- ❌ **NO gestión de roles**
- ❌ **NO configuraciones** del sistema

---

## 🔐 Acceso al Sistema

### Credenciales de Viewer
- **URL:** `http://localhost/voto/`
- **Usuario:** `viewer1`
- **Contraseña:** `admin123`

### Proceso de Login
1. **Abrir** navegador web
2. **Navegar** a `http://localhost/voto/`
3. **Ingresar** credenciales de viewer
4. **Hacer clic** en "Iniciar Sesión"

```
┌─────────────────────────────────────┐
│         Iniciar Sesión              │
├─────────────────────────────────────┤
│ Usuario: [viewer1                ]  │
│ Contraseña: [•••••••••••••••••••]   │
│                                     │
│     [  Iniciar Sesión  ]            │
└─────────────────────────────────────┘
```

### Después del Login
Una vez autenticado, accederás al dashboard con funcionalidades de solo lectura.

---

## 🏠 Dashboard del Viewer

### Vista del Dashboard para Viewer

```
┌─────────────────────────────────────────────────────────────┐
│ 🏠 Dashboard - Sistema de Votación                          │
├─────────────────────────────────────────────────────────────┤
│ Menú: [Dashboard]                         [👤 Viewer ▼]    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ 👋 Bienvenido, Usuario Viewer                               │
│                                                             │
│ 📊 Información del Sistema (Solo Lectura)                  │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │Mi Información│ │Último Acceso│ │Estado Cuenta│            │
│ │Usuario Viewer│ │ Hoy 11:15   │ │   Activo    │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
│                                                             │
│ 📈 Estadísticas Generales                                  │
│ • Total de usuarios en el sistema: 3                       │
│ • Roles configurados: 3                                    │
│ • Sistema operativo desde: Sept 2025                       │
│                                                             │
│ 👁️ Información de Solo Lectura                             │
│ • Puedes consultar información general                     │
│ • No puedes realizar modificaciones                        │
│ • Para cambios, contacta al administrador                  │
└─────────────────────────────────────────────────────────────┘
```

### Características del Dashboard Viewer

#### ✅ Información Visible:
- **Estadísticas generales** del sistema
- **Tu información personal** básica
- **Estado de tu cuenta**
- **Historial de accesos** personal

#### 🔒 Elementos de Solo Lectura:
- **Sin botones de acción** (crear, editar, eliminar)
- **Sin formularios** de entrada de datos
- **Sin enlaces** a funciones administrativas
- **Información claramente marcada** como "Solo Lectura"

---

## 👁️ Funcionalidades de Solo Lectura

### Panel de Información Personal

#### Tu Perfil (Solo Lectura)
```
┌─────────────────────────────────────┐
│      Mi Información Personal        │
├─────────────────────────────────────┤
│ 👤 Nombre: Usuario Viewer           │
│ 🔑 Usuario: viewer1                 │
│ 📧 Email: viewer@sistema.com        │
│ 🛡️ Rol: Viewer (Solo Lectura)       │
│ ✅ Estado: Activo                   │
│ ⏰ Último acceso: Hoy 11:15         │
│                                     │
│ 📊 Mi Actividad:                    │
│ • Conexiones esta semana: 3         │
│ • Tiempo promedio de sesión: 15min  │
│ • Primera conexión: 10 Sept 2025   │
│                                     │
│ ℹ️ Nota: Esta información es de     │
│    solo lectura. Para cambios,      │
│    contacta al administrador.       │
└─────────────────────────────────────┘
```

### Estadísticas del Sistema

#### Información General Disponible
```
┌─────────────────────────────────────┐
│     Estadísticas del Sistema        │
├─────────────────────────────────────┤
│ 📊 Resumen General:                 │
│                                     │
│ 👥 Usuarios Totales: 3              │
│    • Administradores: 1             │
│    • Editores: 1                    │
│    • Viewers: 1                     │
│                                     │
│ 🛡️ Roles Configurados: 3            │
│    • Admin (Acceso completo)        │
│    • Editor (Acceso limitado)       │
│    • Viewer (Solo lectura)          │
│                                     │
│ 📅 Sistema Activo Desde:            │
│    Septiembre 2025                  │
│                                     │
│ 🔄 Última Actualización:            │
│    12 de Septiembre 2025            │
└─────────────────────────────────────┘
```

---

## 🧭 Navegación del Sistema

### Menú Principal Simplificado

#### Opciones Disponibles
```
┌─────────────────────────────────────┐
│ Navegación para Viewer              │
├─────────────────────────────────────┤
│ 🏠 Dashboard                        │
│    └─ Información general           │
│                                     │
│ 👤 Mi Perfil                        │
│    └─ Información personal          │
│                                     │
│ 🚪 Cerrar Sesión                    │
│    └─ Terminar sesión actual        │
└─────────────────────────────────────┘
```

### Menú de Usuario

#### Dropdown del Usuario
```
┌─────────────────────────────────────┐
│ 👤 Usuario Viewer ▼                 │
├─────────────────────────────────────┤
│ 👁️ Ver Mi Perfil                    │
│ ─────────────────────────────────   │
│ 🚪 Cerrar Sesión                    │
└─────────────────────────────────────┘
```

### URLs Accesibles

#### ✅ Rutas Permitidas:
- `/dashboard` - Dashboard principal
- `/logout` - Cerrar sesión
- Información personal (futuro)

#### ❌ Rutas Bloqueadas:
- `/users` - Gestión de usuarios
- `/users/create` - Crear usuarios
- `/users/edit` - Editar usuarios
- `/roles` - Gestión de roles
- Cualquier función administrativa

---

## 🚫 Limitaciones del Rol

### Restricciones de Acceso

#### ❌ Funciones Completamente Bloqueadas

**Gestión de Usuarios:**
- No aparece en el menú
- Acceso directo por URL redirige al dashboard
- No puedes ver listas de usuarios
- No puedes crear, editar o eliminar usuarios

**Gestión de Roles:**
- No aparece en el menú
- Sin acceso a configuración de roles
- No puedes asignar roles a usuarios

**Configuraciones del Sistema:**
- Sin acceso a panel de configuraciones
- No puedes modificar parámetros del sistema
- Sin acceso a logs o archivos de configuración

### Mensajes de Información

#### 💡 Avisos de Solo Lectura
```
┌─────────────────────────────────────┐
│         ℹ️ Información               │
├─────────────────────────────────────┤
│ Tu cuenta tiene permisos de solo    │
│ lectura. Puedes consultar           │
│ información pero no realizar        │
│ modificaciones.                     │
│                                     │
│ Para solicitar cambios o            │
│ actualizaciones, contacta al        │
│ administrador del sistema.          │
│                                     │
│        [Entendido]                  │
└─────────────────────────────────────┘
```

### Redirección Automática

Si intentas acceder a funciones restringidas:
1. **Se detecta** la falta de permisos
2. **Redirección automática** al dashboard
3. **Mensaje informativo** (opcional)
4. **No se muestran errores** confusos

---

## 📋 Buenas Prácticas

### Uso Responsable

#### ✅ Recomendaciones para Viewer
- **Revisar** la información disponible regularmente
- **Familiarizarse** con las estadísticas del sistema
- **Reportar** cualquier inconsistencia al administrador
- **Mantener** credenciales seguras
- **Cerrar sesión** al finalizar consultas

#### 🔒 Seguridad de la Cuenta
- **No compartir** credenciales con otros usuarios
- **Usar** conexión segura cuando sea posible
- **Verificar** URL del sistema antes de iniciar sesión
- **Reportar** actividad sospechosa inmediatamente

### Consulta Eficiente

#### 📊 Aprovechamiento de la Información
- **Revisar** estadísticas regularmente
- **Tomar notas** de información importante
- **Consultar** cambios en los datos del sistema
- **Identificar** patrones o tendencias

#### 🕒 Gestión del Tiempo
- **Planificar** consultas necesarias
- **Optimizar** tiempo de sesión
- **Cerrar sesión** cuando no esté en uso
- **Documentar** información relevante fuera del sistema

---

## 📊 Información Disponible

### Tipos de Datos que Puedes Consultar

#### 🔢 Estadísticas Numéricas
- Número total de usuarios del sistema
- Cantidad de roles configurados
- Fechas de creación y actualizaciones
- Estadísticas de tu propia actividad

#### 📈 Información de Tendencias
- Actividad general del sistema
- Frecuencia de uso
- Datos históricos básicos
- Estado general del sistema

#### 👤 Información Personal
- Tu perfil de usuario
- Historial de accesos personal
- Estado de tu cuenta
- Estadísticas de uso individual

### Formato de Presentación

#### 📋 Tablas de Solo Lectura
```
┌─────────────────────────────────────┐
│     Información del Sistema         │
├─────────────────────────────────────┤
│ Elemento          │ Valor    │ Estado │
│ ─────────────────│─────────│────────│
│ Total Usuarios   │    3     │ Activo │
│ Roles Config.    │    3     │ Activo │
│ Último Update    │ 12/09/25 │   OK   │
└─────────────────────────────────────┘
```

---

## ❓ Preguntas Frecuentes

### 🔍 Sobre el Rol Viewer

**P: ¿Qué significa "Solo Lectura"?**
R: Significa que puedes consultar y ver información, pero no puedes crear, editar o eliminar nada en el sistema.

**P: ¿Por qué no veo menús de "Usuarios" o "Roles"?**
R: Estos menús están restringidos para tu rol. Solo puedes acceder al dashboard y tu información personal.

**P: ¿Puedo solicitar permisos adicionales?**
R: Sí, puedes contactar al administrador para solicitar un cambio de rol si necesitas más funcionalidades.

### 🛠️ Problemas Técnicos

**P: ¿Qué hago si veo "Acceso Denegado"?**
R: Esto es normal si intentas acceder a funciones restringidas. Regresa al dashboard usando el menú.

**P: ¿Por qué no puedo cambiar mi información?**
R: Como viewer, no tienes permisos de edición. Contacta al administrador para cambios en tu perfil.

**P: ¿Puedo descargar o exportar información?**
R: Esta funcionalidad no está disponible actualmente. Puedes tomar notas manualmente de la información que necesites.

### 📊 Sobre la Información

**P: ¿Con qué frecuencia se actualiza la información?**
R: La información se actualiza en tiempo real cada vez que accedes al dashboard.

**P: ¿Puedo ver información histórica?**
R: Solo puedes ver información básica histórica de tu propia actividad y estadísticas generales del sistema.

**P: ¿La información que veo es completa?**
R: Ves un resumen de la información más relevante. Los administradores tienen acceso a información más detallada.

---

## 🆘 Soporte y Asistencia

### 📞 Contacto para Soporte

**Administrador del Sistema:**
- **Usuario**: admin
- **Email**: admin@sistema.com
- **Disponibilidad**: Horario laboral

### 📋 Información para Reportes

Al contactar soporte, incluye:
- **Tu usuario**: viewer1
- **Fecha y hora** del problema
- **Descripción detallada** de lo que intentabas hacer
- **Mensaje de error** (si aplica)
- **Navegador** que estás usando

### 📚 Recursos Adicionales

**Documentación:**
- Manual principal: `/docs/README.md`
- Guía técnica: `/docs/TECHNICAL.md`
- Otras guías de usuario en `/docs/guides/`

---

## 🔄 Actualizaciones del Sistema

### Notificaciones para Viewers

#### Cambios que te Afectan
- ✅ Nuevas estadísticas disponibles
- ✅ Mejoras en la interfaz de consulta
- ✅ Actualizaciones de seguridad
- ✅ Cambios en el formato de información

#### Cambios que NO te Afectan
- ❌ Nuevas funciones administrativas
- ❌ Cambios en gestión de usuarios
- ❌ Modificaciones en roles y permisos
- ❌ Configuraciones del sistema

### Cómo te Enterarás de Cambios

1. **Avisos en el dashboard** cuando hay actualizaciones
2. **Notificación del administrador** para cambios importantes
3. **Documentación actualizada** en la carpeta `/docs/`

---

## 📝 Resumen del Rol Viewer

### Lo que Puedes Hacer ✅
- Consultar dashboard principal
- Ver estadísticas generales del sistema
- Revisar tu información personal
- Consultar tu historial de actividad
- Cerrar sesión cuando termines

### Lo que NO Puedes Hacer ❌
- Crear, editar o eliminar usuarios
- Modificar roles o permisos
- Acceder a configuraciones del sistema
- Realizar cambios de cualquier tipo
- Ver información detallada de otros usuarios

### Tu Responsabilidad 🎯
- Usar el sistema de manera responsable
- Reportar problemas al administrador
- Mantener credenciales seguras
- Respetar las limitaciones del rol
- Aprovecha la información disponible para tus necesidades de consulta

---

*Guía del Viewer - Sistema de Votación v1.0.0*
*Tu rol de solo lectura te permite acceder a información valiosa del sistema de manera segura y controlada.*