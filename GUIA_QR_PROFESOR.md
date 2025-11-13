# 📱 Guía Rápida: Sistema de QR para Profesores

## ⚠️ IMPORTANTE: QR de Un Solo Uso

**Cada código QR solo puede escanearse UNA VEZ**. Esto es por diseño de seguridad.

## 🎯 Flujo Normal de Uso

### 1. Inicio de Clase
```
Profesor llega → Abre dashboard → Genera QR → Muestra a estudiantes
```

### 2. Durante la Clase
- Los estudiantes escanean el QR
- El sistema registra la asistencia del profesor
- El QR queda "usado" y no puede volver a escanearse

### 3. Si Necesitas Otro QR
- Haz clic en **"Nueva Sesión"** o **"Generar QR"**
- Se creará un nuevo QR (Sesión #2, #3, etc.)
- Comparte el nuevo QR

## 🔴 Errores Comunes y Soluciones

### Error: "Código QR inválido o ya utilizado"

**Causa:** El QR ya fue escaneado anteriormente

**Solución:**
1. Ve a tu dashboard
2. Busca la clase actual
3. Haz clic en **"Nueva Sesión"** o **"Generar QR"**
4. Comparte el NUEVO QR (no el anterior)

### Error: "Código QR expirado"

**Causa:** Han pasado más de 30 minutos desde que se generó

**Solución:**
1. Genera un nuevo QR
2. Los QR antiguos se desactivan automáticamente

### No Aparece el Botón "Generar QR"

**Causa:** No tienes clases programadas para hoy

**Solución:**
1. Verifica tu horario en "Mi Horario"
2. Contacta al administrador si falta alguna clase

## 📋 Casos de Uso Comunes

### Caso 1: Clase Normal
```
1. Llegas a clase a las 8:00
2. Generas QR (Sesión #1)
3. Estudiantes escanean
4. Clase termina
```

### Caso 2: Clase con Receso
```
1. Primera parte de clase (8:00)
   → Generas QR (Sesión #1)
   
2. Receso (9:30)

3. Segunda parte de clase (10:00)
   → Generas NUEVO QR (Sesión #2)
```

### Caso 3: Estudiante Llega Tarde
```
1. Ya generaste QR al inicio
2. Estudiante llega tarde
3. El QR ya fue usado
4. Opciones:
   a) Generar nuevo QR (Sesión #2) solo para él
   b) Registrar manualmente su asistencia
```

### Caso 4: QR No Funciona
```
1. Verifica que sea el QR más reciente
2. Genera un nuevo QR
3. Asegúrate de compartir el enlace completo
4. Verifica tu conexión a internet
```

## 🔧 Cómo Generar un Nuevo QR

### Opción 1: Desde el Dashboard
1. Ve a `/profesor/dashboard`
2. Busca tu clase actual
3. Haz clic en **"Generar QR"** o **"Nueva Sesión"**
4. Selecciona modalidad (Presencial/Virtual)
5. Confirma

### Opción 2: Si Ya Hay un QR Activo
1. Busca el botón **"Nueva Sesión"**
2. Haz clic
3. Se generará un nuevo QR (Sesión #2, #3, etc.)

## 📱 Formas de Compartir el QR

### 1. Mostrar en Pantalla
- Proyecta el QR en el aula
- Estudiantes escanean con su celular

### 2. Copiar Enlace
- Haz clic en "Copiar Enlace"
- Envía por WhatsApp/Telegram al grupo

### 3. Compartir Directamente
- Usa el botón "Compartir"
- Selecciona la app (WhatsApp, etc.)

## ⏰ Tiempos Importantes

- **Validez del QR:** 30 minutos
- **Uso:** Una sola vez
- **Sesiones:** Ilimitadas por clase

## 🎓 Buenas Prácticas

### ✅ Hacer
- Generar QR al inicio de cada clase
- Generar nuevo QR si hay receso largo
- Verificar que el QR se generó correctamente
- Compartir el enlace completo

### ❌ Evitar
- Reutilizar QR de clases anteriores
- Compartir QR de hace más de 30 minutos
- Usar el mismo QR para múltiples sesiones
- Compartir enlaces incompletos

## 🔍 Verificar Estado de Asistencia

### En el Dashboard
- **Verde (Confirmado):** Asistencia registrada
- **Amarillo (Pendiente QR):** QR generado pero no escaneado
- **Gris (Sin registro):** No hay QR generado

### En el Historial
- Ve a "Historial de Asistencias"
- Verifica todas tus asistencias registradas
- Filtra por fecha, materia, etc.

## 📞 Soporte

### Si el QR no funciona:

1. **Limpia caché del navegador**
   - Ctrl + Shift + Delete
   - Borra caché y cookies

2. **Verifica conexión**
   - Asegúrate de tener internet
   - Prueba en otro navegador

3. **Genera nuevo QR**
   - Siempre puedes generar uno nuevo
   - No hay límite de sesiones

4. **Contacta soporte técnico**
   - Si el problema persiste
   - Proporciona: fecha, hora, materia

## 💡 Tips Útiles

1. **Genera el QR justo antes de clase**
   - No lo generes con mucha anticipación
   - Así evitas que expire

2. **Guarda el enlace**
   - Copia el enlace del QR
   - Guárdalo por si necesitas reenviarlo

3. **Verifica en el dashboard**
   - Después de generar el QR
   - Confirma que aparece como "Pendiente QR"

4. **Comunica a los estudiantes**
   - Explica que cada QR es de un solo uso
   - Diles que escaneen el más reciente

## 📊 Estadísticas

En tu dashboard verás:
- **Clases Hoy:** Total de clases programadas
- **Asistidas Hoy:** Clases con asistencia confirmada
- **Pendientes:** Clases sin QR o sin escanear
- **Total Materias:** Materias que impartes

## 🎯 Resumen Rápido

```
✅ Cada QR = Un solo uso
✅ Expira en 30 minutos
✅ Puedes generar múltiples QR por clase (sesiones)
✅ Comparte siempre el QR más reciente
✅ Si no funciona, genera uno nuevo
```

---

**Última actualización:** 2025-11-12
**Versión:** 1.0
