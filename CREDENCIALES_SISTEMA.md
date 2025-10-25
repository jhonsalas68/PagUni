# 🔐 Credenciales del Sistema Universitario

## 📊 **Base de Datos Actualizada**

Ahora cada tipo de usuario tiene su propio código y contraseña:

### 🔧 **ADMINISTRADORES**
Tabla: `administradores`

**Super Administrador:**
- **Código:** `ADM001`
- **Contraseña:** `admin123`
- **Nombre:** Super Administrador
- **Nivel:** super_admin

**Administrador Académico:**
- **Código:** `ADM002`
- **Contraseña:** `admin123`
- **Nombre:** Administrador Académico
- **Nivel:** admin

### 👨‍🏫 **PROFESORES**
Tabla: `profesores` (ahora con `codigo_docente` y `password`)

**Profesor 1:**
- **Código:** `PROF001`
- **Contraseña:** `password123`
- **Nombre:** Juan Carlos Pérez García
- **Especialidad:** Ingeniería de Software

**Profesor 2:**
- **Código:** `PROF002`
- **Contraseña:** `password123`
- **Nombre:** María Elena Rodríguez López
- **Especialidad:** Matemáticas Aplicadas

### 🎓 **ESTUDIANTES**
Tabla: `estudiantes` (ahora con `password`)

**Estudiante 1:**
- **Código:** `ISC2024001` (ya existía)
- **Contraseña:** `student123`
- **Nombre:** Ana González Martínez
- **Carrera:** Ingeniería en Sistemas Computacionales

**Estudiante 2:**
- **Código:** `MATE2024001` (ya existía)
- **Contraseña:** `student123`
- **Nombre:** Carlos Hernández Silva
- **Carrera:** Licenciatura en Matemáticas

## 🗄️ **Estructura de Tablas**

### Administradores
```sql
- id
- codigo_admin (único)
- nombre
- apellido
- email (único)
- telefono
- cedula (único)
- password (hasheado)
- nivel_acceso (super_admin/admin)
- timestamps
```

### Profesores (actualizada)
```sql
- id
- codigo_docente (único) ← NUEVO
- nombre
- apellido
- email (único)
- telefono
- cedula (único)
- especialidad
- tipo_contrato
- password (hasheado) ← NUEVO
- timestamps
```

### Estudiantes (actualizada)
```sql
- id
- nombre
- apellido
- email (único)
- telefono
- cedula (único)
- codigo_estudiante (único)
- fecha_nacimiento
- direccion
- password (hasheado) ← NUEVO
- carrera_id
- semestre_actual
- estado
- timestamps
```

## 🚀 **API Endpoints Disponibles**

### Administradores
- `GET /api/administradores` - Listar administradores
- `POST /api/administradores` - Crear administrador
- `GET /api/administradores/{id}` - Ver administrador
- `PUT /api/administradores/{id}` - Actualizar administrador
- `DELETE /api/administradores/{id}` - Eliminar administrador

### Profesores, Estudiantes, etc.
- Todas las rutas API existentes siguen funcionando
- Los modelos ahora incluyen los campos de código y contraseña

## ✅ **Listo para Login**

Ahora tienes:
- ✅ **Administradores** con `codigo_admin` y `password`
- ✅ **Profesores** con `codigo_docente` y `password`  
- ✅ **Estudiantes** con `codigo_estudiante` y `password`

Cada entidad puede autenticarse independientemente con su código y contraseña.

## 🔄 **Próximos Pasos**

1. Crear sistema de autenticación que detecte el tipo de usuario por el formato del código
2. Crear dashboards específicos para cada rol
3. Implementar middleware de autorización

---

**¡La base de datos está lista para el sistema de login!** 🎓