# 📊 Diagramas del Sistema - Sistema de Votación

## 🎯 Índice de Diagramas

1. [🏗️ Diagrama de Arquitectura MVC](#-diagrama-de-arquitectura-mvc)
2. [🗄️ Diagrama de Base de Datos (ERD)](#-diagrama-de-base-de-datos-erd)
3. [🔐 Flujo de Autenticación](#-flujo-de-autenticación)
4. [👥 Mapas de Navegación por Rol](#-mapas-de-navegación-por-rol)
5. [🔄 Diagramas de Secuencia](#-diagramas-de-secuencia)
6. [📁 Estructura de Archivos Visual](#-estructura-de-archivos-visual)

---

## 🏗️ Diagrama de Arquitectura MVC

### Arquitectura General del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND (Views)                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │    Auth     │  │  Dashboard  │  │    Users    │             │
│  │   Views     │  │    Views    │  │   Views     │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │    Roles    │  │   Layouts   │  │   Errors    │             │
│  │   Views     │  │   (main.php)│  │   Views     │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
├─────────────────────────────────────────────────────────────────┤
│                       BACKEND (Controllers)                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │    Auth     │  │  Dashboard  │  │    User     │             │
│  │ Controller  │  │ Controller  │  │ Controller  │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │    Role     │  │   Error     │                              │
│  │ Controller  │  │ Controller  │                              │
│  └─────────────┘  └─────────────┘                              │
├─────────────────────────────────────────────────────────────────┤
│                        MODELS (Data)                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │    User     │  │    Role     │                              │
│  │   Model     │  │   Model     │                              │
│  └─────────────┘  └─────────────┘                              │
├─────────────────────────────────────────────────────────────────┤
│                          CORE SYSTEM                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │   Router    │  │ Controller  │  │    Model    │             │
│  │    Core     │  │    Base     │  │    Base     │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
├─────────────────────────────────────────────────────────────────┤
│                         DATABASE                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │    users    │  │    roles    │  │user_sessions│             │
│  │    table    │  │    table    │  │    table    │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                 │
│  ┌─────────────┐                                               │
│  │activity_logs│                                               │
│  │    table    │                                               │
│  └─────────────┘                                               │
└─────────────────────────────────────────────────────────────────┘
```

### Flujo de Datos MVC

```
User Request → Router → Controller → Model → Database
                ↓
    Browser ← View ← Controller ← Model ← Database
```

---

## 🗄️ Diagrama de Base de Datos (ERD)

### Relaciones entre Tablas

```
┌─────────────────────────────────────────────────────────────────┐
│                      ESQUEMA DE BASE DE DATOS                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────┐         ┌─────────────────────┐
│       ROLES         │         │       USERS         │
├─────────────────────┤         ├─────────────────────┤
│ • id (PK)          │◄────────┤ • id (PK)          │
│ • name             │         │ • username         │
│ • description      │         │ • email            │
│ • created_at       │         │ • password_hash    │
│ • updated_at       │         │ • role_id (FK)     │
└─────────────────────┘         │ • created_at       │
                                │ • updated_at       │
                                │ • last_login       │
                                └─────────────────────┘
                                         │
                                         │
                                         ▼
                                ┌─────────────────────┐
                                │   USER_SESSIONS     │
                                ├─────────────────────┤
                                │ • id (PK)          │
                                │ • user_id (FK)     │
                                │ • session_token    │
                                │ • created_at       │
                                │ • expires_at       │
                                │ • is_active        │
                                └─────────────────────┘

                                ┌─────────────────────┐
                                │   ACTIVITY_LOGS     │
                                ├─────────────────────┤
                                │ • id (PK)          │
                                │ • user_id (FK)     │
                                │ • action           │
                                │ • description      │
                                │ • ip_address       │
                                │ • user_agent       │
                                │ • created_at       │
                                └─────────────────────┘
```

### Detalles de Relaciones

```
users.role_id → roles.id (FOREIGN KEY)
user_sessions.user_id → users.id (FOREIGN KEY)
activity_logs.user_id → users.id (FOREIGN KEY)
```

---

## 🔐 Flujo de Autenticación

### Proceso de Login

```
┌─────────────┐
│   Usuario   │
│  ingresa    │
│ credenciales│
└──────┬──────┘
       │
       ▼
┌─────────────┐    NO    ┌─────────────┐
│ Validación  │─────────►│   Error:    │
│credenciales │          │ Credenciales│
│   válidas?  │          │  inválidas  │
└──────┬──────┘          └─────────────┘
       │ SÍ
       ▼
┌─────────────┐
│  Crear      │
│  sesión     │
│  (session)  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ Registrar   │
│ último      │
│ acceso      │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ Redireccionar│
│ según rol:  │
│ • Admin     │
│ • Editor    │
│ • Viewer    │
└─────────────┘
```

### Control de Acceso por Rol

```
┌─────────────┐
│  Request    │
│  del User   │
└──────┬──────┘
       │
       ▼
┌─────────────┐    NO    ┌─────────────┐
│ ¿Usuario    │─────────►│ Redirigir a │
│ logueado?   │          │   login     │
└──────┬──────┘          └─────────────┘
       │ SÍ
       ▼
┌─────────────┐
│ Verificar   │
│ rol del     │
│ usuario     │
└──────┬──────┘
       │
       ▼
┌─────────────┐    NO    ┌─────────────┐
│ ¿Tiene      │─────────►│ Error 403   │
│ permisos?   │          │ Acceso      │
└──────┬──────┘          │ Denegado    │
       │ SÍ              └─────────────┘
       ▼
┌─────────────┐
│ Procesar    │
│ solicitud   │
└─────────────┘
```

---

## 👥 Mapas de Navegación por Rol

### 👑 Navegación del Administrador

```
┌─────────────────────────────────────────────────────────────────┐
│                      DASHBOARD ADMIN                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │  Dashboard  │  │   Usuarios  │  │    Roles    │             │
│  │   (inicio)  │  │   (CRUD)    │  │   (CRUD)    │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │Configuración│  │   Logout    │                              │
│  │   Sistema   │  │             │                              │
│  └─────────────┘  └─────────────┘                              │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                        USUARIOS CRUD                           │
├─────────────────────────────────────────────────────────────────┤
│  • Listar todos los usuarios                                   │
│  • Crear nuevos usuarios                                       │
│  • Editar usuarios existentes                                  │
│  • Eliminar usuarios                                           │
│  • Cambiar roles de usuarios                                   │
│  • Ver historial de actividad                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         ROLES CRUD                             │
├─────────────────────────────────────────────────────────────────┤
│  • Listar todos los roles                                      │
│  • Crear nuevos roles                                          │
│  • Editar roles existentes                                     │
│  • Eliminar roles                                              │
│  • Definir permisos por rol                                    │
└─────────────────────────────────────────────────────────────────┘
```

### 📝 Navegación del Editor

```
┌─────────────────────────────────────────────────────────────────┐
│                      DASHBOARD EDITOR                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │  Dashboard  │  │   Logout    │                              │
│  │   (inicio)  │  │             │                              │
│  └─────────────┘  └─────────────┘                              │
│                                                                 │
│         🚫 NO PUEDE VER: Usuarios, Roles                       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    FUNCIONALIDADES EDITOR                      │
├─────────────────────────────────────────────────────────────────┤
│  • Ver dashboard con información limitada                      │
│  • Acceso a funcionalidades de edición (si implementadas)      │
│  • Ver su propio perfil                                        │
│  • Cambiar su propia contraseña                                │
└─────────────────────────────────────────────────────────────────┘
```

### 👁️ Navegación del Viewer

```
┌─────────────────────────────────────────────────────────────────┐
│                      DASHBOARD VIEWER                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │  Dashboard  │  │   Logout    │                              │
│  │ (solo lectura)│ │             │                              │
│  └─────────────┘  └─────────────┘                              │
│                                                                 │
│         🚫 NO PUEDE VER: Usuarios, Roles                       │
│         🚫 NO PUEDE EDITAR: Ningún contenido                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    FUNCIONALIDADES VIEWER                      │
├─────────────────────────────────────────────────────────────────┤
│  • Ver dashboard con información de solo lectura               │
│  • Consultar datos sin capacidad de modificación               │
│  • Ver reportes y estadísticas (si implementados)              │
│  • Ver su propio perfil (solo lectura)                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Diagramas de Secuencia

### Secuencia de Login

```
Usuario    Controller    Model    Database    Session
  │           │           │          │          │
  │ POST      │           │          │          │
  │ login ───►│           │          │          │
  │           │ validate  │          │          │
  │           │ user ────►│          │          │
  │           │           │ SELECT   │          │
  │           │           │ user ───►│          │
  │           │           │ user ◄───│          │
  │           │ user ◄────│          │          │
  │           │ verify    │          │          │
  │           │ password  │          │          │
  │           │           │          │          │
  │           │ start     │          │          │
  │           │ session ──┼──────────┼─────────►│
  │           │           │          │          │
  │           │ UPDATE    │          │          │
  │           │ last_login│          │          │
  │           │ ─────────►│          │          │
  │           │           │ UPDATE ──►          │
  │           │           │          │          │
  │ redirect  │           │          │          │
  │ ◄─────────│           │          │          │
```

### Secuencia de Verificación de Rol

```
Usuario    Controller    Session    Database
  │           │           │          │
  │ request   │           │          │
  │ page ────►│           │          │
  │           │ check     │          │
  │           │ session ──►          │
  │           │ user_id ◄─│          │
  │           │           │          │
  │           │ get user  │          │
  │           │ role ─────┼─────────►│
  │           │ role ◄────┼──────────│
  │           │           │          │
  │           │ verify    │          │
  │           │ permission│          │
  │           │           │          │
  │ allow/    │           │          │
  │ deny ◄────│           │          │
```

---

## 📁 Estructura de Archivos Visual

### Estructura del Proyecto

```
voto/
├── 📄 index.php                    # Punto de entrada
├── 📄 README.md                   # Documentación principal
├── 📄 .htaccess                   # Configuración Apache
│
├── 📁 app/                        # Aplicación principal
│   ├── 📁 controllers/            # Controladores MVC
│   │   ├── 📄 AuthController.php      # Autenticación
│   │   ├── 📄 DashboardController.php # Dashboard
│   │   ├── 📄 ErrorController.php     # Manejo errores
│   │   ├── 📄 RoleController.php      # Gestión roles
│   │   └── 📄 UserController.php      # Gestión usuarios
│   │
│   ├── 📁 models/                 # Modelos de datos
│   │   ├── 📄 Role.php               # Modelo de roles
│   │   └── 📄 User.php               # Modelo de usuarios
│   │
│   └── 📁 views/                  # Vistas/Templates
│       ├── 📁 auth/               # Vistas autenticación
│       │   └── 📄 login.php           # Formulario login
│       ├── 📁 dashboard/          # Vistas dashboard
│       │   └── 📄 index.php           # Dashboard principal
│       ├── 📁 errors/             # Vistas de error
│       │   └── 📄 404.php             # Error 404
│       ├── 📁 layouts/            # Layouts base
│       │   └── 📄 main.php            # Layout principal
│       ├── 📁 roles/              # Vistas de roles
│       │   ├── 📄 create.php          # Crear rol
│       │   ├── 📄 edit.php            # Editar rol
│       │   └── 📄 index.php           # Listar roles
│       └── 📁 users/              # Vistas de usuarios
│           ├── 📄 create.php          # Crear usuario
│           ├── 📄 edit.php            # Editar usuario
│           └── 📄 index.php           # Listar usuarios
│
├── 📁 assets/                     # Recursos estáticos
│   ├── 📁 css/                   # Hojas de estilo
│   │   └── 📄 style.css              # Estilos principales
│   └── 📁 js/                    # JavaScript
│       └── 📄 script.js              # Scripts principales
│
├── 📁 config/                     # Configuraciones
│   ├── 📄 config.php                # Configuración general
│   └── 📄 database.php             # Configuración BD
│
├── 📁 core/                       # Sistema base
│   ├── 📄 Controller.php            # Controlador base
│   ├── 📄 Model.php                # Modelo base
│   └── 📄 Router.php               # Enrutador
│
├── 📁 database/                   # Base de datos
│   └── 📄 schema.sql               # Esquema BD
│
└── 📁 docs/                       # Documentación
    ├── 📄 INDEX.md                   # Índice documentación
    ├── 📄 README.md                  # Manual principal
    ├── 📄 TECHNICAL.md               # Doc técnica
    ├── 📄 INSTALLATION.md            # Guía instalación
    ├── 📄 DIAGRAMS.md                # Este archivo
    └── 📁 guides/                    # Guías específicas
        ├── 📄 admin-guide.md             # Guía administrador
        ├── 📄 editor-guide.md            # Guía editor
        └── 📄 viewer-guide.md            # Guía viewer
```

### Flujo de Archivos por Request

```
┌─────────────┐
│   Browser   │
│   Request   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ index.php   │ ◄── Punto de entrada
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ Router.php  │ ◄── Análisis de ruta
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ Controller  │ ◄── Controlador específico
│ (Auth/User/ │     (app/controllers/)
│ Dashboard)  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Model     │ ◄── Modelo de datos
│ (User/Role) │     (app/models/)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Database   │ ◄── Base de datos
│   (MySQL)   │     (database/)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    View     │ ◄── Vista/Template
│ (Template)  │     (app/views/)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Browser   │
│  Response   │
└─────────────┘
```

---

## 📊 Diagramas de Estado

### Estados de Usuario

```
┌─────────────┐    register    ┌─────────────┐
│    Guest    │──────────────► │   Inactive  │
│ (No login)  │                │    User     │
└─────────────┘                └──────┬──────┘
       │                              │ activate
       │ login                        ▼
       ▼                     ┌─────────────┐
┌─────────────┐              │   Active    │
│ Logged User │◄─────────────│    User     │
│             │    login     │             │
└──────┬──────┘              └──────┬──────┘
       │ logout                     │ deactivate
       ▼                            ▼
┌─────────────┐              ┌─────────────┐
│    Guest    │              │   Inactive  │
│ (No login)  │              │    User     │
└─────────────┘              └─────────────┘
```

### Estados de Sesión

```
┌─────────────┐    create     ┌─────────────┐
│ No Session  │─────────────► │   Active    │
│             │               │   Session   │
└─────────────┘               └──────┬──────┘
       ▲                             │
       │ destroy                     │ expire/logout
       │                             ▼
┌─────────────┐              ┌─────────────┐
│   Session   │◄─────────────│   Expired   │
│  Destroyed  │   cleanup    │   Session   │
└─────────────┘              └─────────────┘
```

---

## 🎯 Convenciones de Diagramas

### Símbolos Utilizados

```
┌─────────────┐     Proceso/Función
│   Proceso   │
└─────────────┘

┌─────────────┐     Decisión
│ ¿Condición? │
└──────┬──────┘
       │ SÍ/NO

   ▼ ▲ ► ◄           Flujo direccional

┌─────────────────────────────────┐    Contenedor/Agrupación
│           Grupo                 │
├─────────────────────────────────┤
│  Contenido                      │
└─────────────────────────────────┘

• Item                             Lista/Elementos
• Item 2

🚫 Restricción                     Indicadores especiales
✅ Permitido
⚠️ Advertencia
```

### Códigos de Color Conceptual

```
🔴 Crítico/Error      - Elementos que requieren atención inmediata
🟡 Advertencia        - Elementos que requieren cuidado
🟢 Normal/Correcto    - Elementos funcionando correctamente
🔵 Información        - Elementos informativos
⚪ Neutral           - Elementos estándar
```

---

## 📝 Notas Importantes

### Para Desarrolladores
- Todos los diagramas están basados en la implementación actual del sistema
- Las relaciones de base de datos son enforced por claves foráneas
- El flujo MVC es estricto y debe seguirse para nuevos desarrollos

### Para Administradores
- Los mapas de navegación reflejan exactamente los permisos actuales
- Los flujos de autenticación están implementados y probados
- Las restricciones por rol son aplicadas tanto en UI como en backend

### Para Usuarios
- Los diagramas de navegación muestran exactamente lo que verán según su rol
- Los flujos de proceso son los que experimentarán en el sistema real

---

## 🔄 Actualización de Diagramas

### Control de Versiones
- **Versión**: 1.0.0
- **Fecha**: 12 de septiembre de 2025
- **Estado**: Actualizado con implementación actual

### Proceso de Actualización
1. Los diagramas deben actualizarse cuando se modifique la arquitectura
2. Cambios en roles/permisos requieren actualización de mapas de navegación
3. Modificaciones de BD requieren actualización del ERD
4. Nuevos controladores/vistas requieren actualización de diagramas MVC

---

*Diagramas del Sistema - Sistema de Votación v1.0.0*  
*Documentación visual completa de la arquitectura y flujos del sistema*