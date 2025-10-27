# 🏢 Sistema de Aulas - UAGRM

## ✅ **Tabla de Aulas Creada**

### 📊 **Estructura de la Tabla `aulas`:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | Integer | ID único |
| `codigo_aula` | String(20) | Código único del aula (A101, LAB-B205, AUD-C301) |
| `nombre` | String | Nombre descriptivo del aula |
| `tipo_aula` | Enum | Tipo: aula, laboratorio, auditorio, sala_conferencias, biblioteca |
| `edificio` | String | Nombre del edificio |
| `piso` | Integer | Número de piso |
| `capacidad` | Integer | Capacidad máxima de estudiantes |
| `descripcion` | Text | Descripción adicional (opcional) |
| `equipamiento` | JSON | Array de equipos disponibles |
| `estado` | Enum | Estado: disponible, ocupada, mantenimiento, fuera_servicio |
| `tiene_aire_acondicionado` | Boolean | Si tiene A/C |
| `tiene_proyector` | Boolean | Si tiene proyector |
| `tiene_computadoras` | Boolean | Si tiene computadoras |
| `acceso_discapacitados` | Boolean | Si tiene acceso para discapacitados |

## 🏫 **Aulas de Ejemplo Creadas (6 aulas):**

### 📚 **Aulas Regulares:**
1. **A101** - Aula Magna A101 (Edificio A, Piso 1, 40 personas)
   - ✅ Aire acondicionado, Proyector
   - ✅ Acceso discapacitados

2. **B201** - Aula B201 (Edificio B, Piso 2, 35 personas)
   - ❌ Sin aire acondicionado ni proyector

### 🔬 **Laboratorios:**
3. **LAB-B205** - Laboratorio de Computación (Edificio B, Piso 2, 25 personas)
   - ✅ 25 Computadoras, Proyector, A/C

4. **LAB-A102** - Laboratorio de Física (Edificio A, Piso 1, 20 personas)
   - ✅ Equipos de laboratorio, Acceso discapacitados

### 🎭 **Auditorios y Salas:**
5. **AUD-C301** - Auditorio Principal (Edificio C, Piso 3, 150 personas)
   - ✅ Sistema de audio, Proyector, A/C, Acceso discapacitados

6. **CONF-C201** - Sala de Conferencias (Edificio C, Piso 2, 30 personas)
   - ✅ Mesa de conferencia, Proyector, A/C, Acceso discapacitados

## 🚀 **API Endpoints Disponibles:**

### CRUD Básico:
- `GET /api/aulas` - Listar todas las aulas
- `POST /api/aulas` - Crear nueva aula
- `GET /api/aulas/{id}` - Ver aula específica
- `PUT /api/aulas/{id}` - Actualizar aula
- `DELETE /api/aulas/{id}` - Eliminar aula

### Filtros Especiales:
- `GET /api/aulas/tipo/{tipo}` - Aulas por tipo (aula, laboratorio, auditorio)
- `GET /api/aulas/edificio/{edificio}` - Aulas por edificio
- `GET /api/aulas-disponibles` - Solo aulas disponibles
- `GET /api/laboratorios` - Solo laboratorios
- `GET /api/auditorios` - Solo auditorios

## 🎯 **Funcionalidades del Modelo:**

### Accessors:
- `tipo_aula_legible` - Muestra el tipo en español
- `estado_legible` - Muestra el estado en español
- `ubicacion_completa` - Ubicación completa del aula

### Scopes:
- `tipo($tipo)` - Filtrar por tipo
- `edificio($edificio)` - Filtrar por edificio
- `disponibles()` - Solo aulas disponibles

## 📋 **Próximos Pasos Sugeridos:**

1. **Horarios de Aulas** - Tabla para gestionar horarios
2. **Reservas de Aulas** - Sistema de reservas
3. **Asignación Materia-Aula** - Vincular materias con aulas
4. **Calendario de Ocupación** - Vista de disponibilidad

---

**¡La tabla de aulas está completamente funcional!** 🏢