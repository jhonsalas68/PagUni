# 🔐 Credenciales de Login - Sistema Universitario

## 🚀 **Cómo Acceder**

1. **Iniciar servidor:** `php artisan serve`
2. **URL:** http://127.0.0.1:8000
3. **Usar código y contraseña según el rol**

---

## 👤 **CÓDIGOS POR ROL**

### 🔧 **ADMINISTRADOR**
- **Código:** `ADM001`
- **Contraseña:** `admin123`

### 👨‍🏫 **PROFESOR** 
- **Código:** `PROF001`
- **Contraseña:** `password123`

### 🎓 **ESTUDIANTE**
- **Código:** `ISC2024001`
- **Contraseña:** `student123`

---

## 🎯 **Detección Automática de Roles**

El sistema detecta automáticamente el tipo de usuario por el formato del código:

- **ADM###** → Redirige a Dashboard Administrador
- **PROF###** → Redirige a Dashboard Profesor  
- **[CARRERA][AÑO]###** → Redirige a Dashboard Estudiante

---

## ✅ **Sistema Funcionando**

- ✅ Login con código y contraseña únicamente
- ✅ Detección automática de roles
- ✅ Redirección automática según el tipo de usuario
- ✅ Dashboards básicos para cada rol
- ✅ Sesiones seguras
- ✅ Logout funcional
- ✅ Middleware de protección de rutas

---

## 🔄 **Flujo Completo**

1. **Acceder a:** http://127.0.0.1:8000
2. **Ingresar código y contraseña**
3. **Sistema detecta el rol automáticamente**
4. **Redirige al dashboard correspondiente**
5. **Navegación protegida por sesiones**

---

**¡El sistema de login está completamente funcional!** 🎓