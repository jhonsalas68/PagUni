# 🔐 CREDENCIALES DE ADMINISTRADORES

## 📋 LISTA DE ADMINISTRADORES

### 1️⃣ ADMINISTRADOR PRINCIPAL (Super Admin)
```
Email:    admin@uagrm.edu.bo
Password: Admin2024!
Código:   ADMIN001
Nivel:    super_admin
Nombre:   Administrador Principal
```
**Uso:** Acceso completo al sistema, todas las funcionalidades

---

### 2️⃣ ADMINISTRADOR ACADÉMICO
```
Email:    academico@uagrm.edu.bo
Password: Academico2024!
Código:   ADMIN002
Nivel:    admin
Nombre:   Carlos Rodríguez Pérez
```
**Uso:** Gestión académica, horarios, materias, grupos

---

### 3️⃣ ADMINISTRADOR DE SISTEMAS
```
Email:    sistemas@uagrm.edu.bo
Password: Sistemas2024!
Código:   ADMIN003
Nivel:    admin
Nombre:   María González Silva
```
**Uso:** Gestión técnica, configuraciones, reportes

---

### 4️⃣ ADMINISTRADOR DE RECURSOS HUMANOS
```
Email:    rrhh@uagrm.edu.bo
Password: RRHH2024!
Código:   ADMIN004
Nivel:    admin
Nombre:   Juan Martínez López
```
**Uso:** Gestión de docentes, asistencias, justificaciones

---

### 5️⃣ ADMINISTRADOR DE PRUEBAS (Desarrollo)
```
Email:    test@uagrm.edu.bo
Password: test123
Código:   ADMIN999
Nivel:    super_admin
Nombre:   Test Administrator
```
**Uso:** Testing y desarrollo (NO usar en producción)

---

## 🚀 CÓMO EJECUTAR EL SEEDER

### Opción 1: Solo Administradores
```bash
php artisan db:seed --class=AdminSeeder
```

### Opción 2: Todos los Seeders
```bash
php artisan db:seed
```

### Opción 3: Refrescar BD y Seeders
```bash
php artisan migrate:fresh --seed
```

---

## 🌐 ACCESO AL SISTEMA

**URL de Login:**
```
http://localhost/login
```

**Pasos:**
1. Abrir navegador
2. Ir a `http://localhost/login`
3. Ingresar email y password
4. Click en "Iniciar Sesión"
5. Serás redirigido al dashboard de administrador

---

## 📊 TABLA RESUMEN

| Rol | Email | Password | Nivel | Código |
|-----|-------|----------|-------|--------|
| Super Admin | admin@uagrm.edu.bo | Admin2024! | super_admin | ADMIN001 |
| Admin Académico | academico@uagrm.edu.bo | Academico2024! | admin | ADMIN002 |
| Admin Sistemas | sistemas@uagrm.edu.bo | Sistemas2024! | admin | ADMIN003 |
| Admin RRHH | rrhh@uagrm.edu.bo | RRHH2024! | admin | ADMIN004 |
| Admin Test | test@uagrm.edu.bo | test123 | super_admin | ADMIN999 |

---

## 🔒 NIVELES DE ACCESO

### **super_admin**
- Acceso total al sistema
- Puede crear/editar/eliminar todo
- Gestión de otros administradores
- Acceso a configuraciones críticas

### **admin**
- Acceso a funcionalidades administrativas
- Gestión de datos académicos
- Reportes y bitácora
- Sin acceso a configuraciones críticas

---

## 💡 RECOMENDACIONES

### Para Desarrollo:
✅ Usar `test@uagrm.edu.bo` con password `test123`
✅ Fácil de recordar y escribir
✅ Nivel super_admin para probar todo

### Para Demostración:
✅ Usar `admin@uagrm.edu.bo` con password `Admin2024!`
✅ Credenciales profesionales
✅ Nombre descriptivo

### Para Producción:
⚠️ **CAMBIAR TODAS LAS CONTRASEÑAS**
⚠️ Usar contraseñas seguras (mínimo 12 caracteres)
⚠️ Eliminar cuenta de pruebas (ADMIN999)
⚠️ Activar autenticación de dos factores

---

## 🛠️ SOLUCIÓN DE PROBLEMAS

### Error: "Credenciales incorrectas"
1. Verificar que el seeder se ejecutó correctamente
2. Verificar mayúsculas/minúsculas en el password
3. Verificar que no haya espacios extra
4. Intentar con cuenta de pruebas: `test@uagrm.edu.bo` / `test123`

### Error: "Usuario no encontrado"
1. Ejecutar el seeder: `php artisan db:seed --class=AdminSeeder`
2. Verificar que la tabla `administradores` existe
3. Verificar conexión a la base de datos

### No puedo acceder al sistema
1. Verificar que el servidor esté corriendo (XAMPP)
2. Verificar URL: `http://localhost/login`
3. Limpiar caché del navegador (Ctrl+Shift+R)
4. Verificar que PostgreSQL esté corriendo

---

## 📝 NOTAS IMPORTANTES

1. **Passwords con caracteres especiales:**
   - `Admin2024!` tiene mayúscula, minúscula, número y símbolo
   - Cumple con estándares de seguridad básicos

2. **Emails institucionales:**
   - Todos usan dominio `@uagrm.edu.bo`
   - Fáciles de identificar como administradores

3. **Códigos únicos:**
   - Formato: `ADMIN###`
   - Secuenciales para fácil identificación

4. **Estado activo:**
   - Todos los administradores están activos por defecto
   - Pueden ser desactivados desde el sistema

---

## 🎯 CREDENCIAL RECOMENDADA PARA MAÑANA

**Para demostración/presentación:**
```
Email:    admin@uagrm.edu.bo
Password: Admin2024!
```

**Ventajas:**
- ✅ Profesional
- ✅ Fácil de recordar
- ✅ Acceso completo (super_admin)
- ✅ Nombre descriptivo

**Alternativa rápida (si olvidas la principal):**
```
Email:    test@uagrm.edu.bo
Password: test123
```

---

## 📞 CONTACTO DE EMERGENCIA

Si tienes problemas para acceder:
1. Ejecutar: `php artisan db:seed --class=AdminSeeder`
2. Usar credenciales de prueba: `test@uagrm.edu.bo` / `test123`
3. Verificar que XAMPP y PostgreSQL estén corriendo

---

**Fecha de creación:** 13 de Noviembre, 2025
**Versión:** 1.0
**Estado:** ✅ Listo para usar
