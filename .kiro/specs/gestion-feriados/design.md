# Diseño del Sistema de Gestión de Feriados

## Resumen

El sistema de gestión de feriados permite a los administradores registrar, modificar y eliminar días no laborables que afectan la planificación académica y el cálculo de asistencia. El diseño se basa en una arquitectura MVC de Laravel con validaciones robustas y integración con otros módulos del sistema.

## Arquitectura

### Patrón MVC
- **Modelo**: `Feriado` - Representa los días no laborables en la base de datos
- **Vista**: Interfaces web para CRUD de feriados con calendario interactivo
- **Controlador**: `FeriadoController` - Maneja la lógica de negocio y validaciones

### Integración con Módulos Existentes
- **Sistema de Horarios**: Consulta feriados para excluir fechas en programación
- **Sistema de Asistencia**: Excluye días no laborables de cálculos
- **Sistema de Autenticación**: Valida permisos de administrador

## Componentes y Interfaces

### Modelo de Datos

#### Tabla `feriados`
```sql
- id (bigint, primary key, auto_increment)
- fecha_inicio (date, not null)
- fecha_fin (date, nullable) 
- descripcion (varchar(255), not null)
- tipo (enum: 'feriado', 'receso', 'asueto')
- activo (boolean, default true)
- created_at (timestamp)
- updated_at (timestamp)
```

#### Índices
- Índice compuesto en (fecha_inicio, fecha_fin) para optimizar consultas de rango
- Índice en campo `activo` para filtrar feriados vigentes

### Controlador Principal

#### FeriadoController
```php
- index(): Listar feriados con paginación y filtros
- create(): Mostrar formulario de creación
- store(): Validar y guardar nuevo feriado
- show(): Mostrar detalles de feriado específico
- edit(): Mostrar formulario de edición
- update(): Validar y actualizar feriado existente
- destroy(): Eliminar feriado con confirmación
- calendar(): API endpoint para vista de calendario
- checkOverlap(): Validar superposiciones de fechas
```

### Servicios de Negocio

#### FeriadoService
```php
- validateDateRange(): Validar rango de fechas
- checkOverlap(): Verificar superposiciones con feriados existentes
- getActiveFeriados(): Obtener feriados activos para período
- markAsNonLectivo(): Marcar fechas como no lectivas
- notifyDependentModules(): Notificar cambios a otros módulos
```

### Interfaces de Usuario

#### Vista Principal (index.blade.php)
- Tabla paginada de feriados con filtros por fecha y tipo
- Botones de acción (Crear, Editar, Eliminar)
- Calendario visual con feriados marcados
- Búsqueda por descripción o rango de fechas

#### Formulario de Creación/Edición
- Selector de fecha específica o rango de fechas
- Campo de descripción con validación de longitud
- Selector de tipo de feriado (feriado/receso/asueto)
- Validación en tiempo real de superposiciones

#### Vista de Calendario
- Calendario mensual/anual con feriados resaltados
- Tooltip con descripción al pasar mouse sobre fechas
- Navegación entre meses/años
- Leyenda de tipos de feriados

## Modelos de Datos

### Modelo Feriado

#### Atributos
- `fecha_inicio`: Fecha de inicio del feriado
- `fecha_fin`: Fecha de fin (null para días específicos)
- `descripcion`: Descripción del evento
- `tipo`: Tipo de día no laborable
- `activo`: Estado del feriado

#### Relaciones
- No tiene relaciones directas, pero es consultado por otros módulos

#### Métodos Principales
```php
- isActive(): Verificar si el feriado está activo
- isInRange($date): Verificar si una fecha está en el rango del feriado
- overlaps($startDate, $endDate): Verificar superposición con otro rango
- scopeActive(): Scope para feriados activos
- scopeInPeriod($start, $end): Scope para feriados en período específico
```

### Validaciones del Modelo

#### Reglas de Validación
```php
- fecha_inicio: required|date|after_or_equal:today
- fecha_fin: nullable|date|after:fecha_inicio
- descripcion: required|string|max:255
- tipo: required|in:feriado,receso,asueto
```

#### Validaciones Personalizadas
- Validación de superposición con feriados existentes
- Validación de coherencia en rangos de fechas
- Validación de formato de fechas según configuración regional

## Manejo de Errores

### Tipos de Errores

#### Errores de Validación
- Fechas inválidas o en formato incorrecto
- Rangos de fechas inconsistentes (fin antes que inicio)
- Descripción vacía o demasiado larga
- Tipo de feriado no válido

#### Errores de Negocio
- Superposición con feriados existentes
- Intento de eliminar feriado con dependencias
- Acceso no autorizado (usuario no administrador)

#### Errores del Sistema
- Fallos de conexión a base de datos
- Errores de integridad referencial
- Timeouts en operaciones de validación

### Estrategias de Manejo

#### Validación en Capas
1. **Frontend**: Validación JavaScript para feedback inmediato
2. **Controlador**: Validación de Laravel con FormRequest
3. **Modelo**: Validaciones de integridad y reglas de negocio
4. **Base de Datos**: Constraints y triggers para integridad final

#### Mensajes de Error Específicos
- "Error: La fecha se superpone con un feriado ya registrado del [fecha] al [fecha]"
- "Error: La fecha de fin debe ser posterior a la fecha de inicio"
- "Error: No tiene permisos para realizar esta operación"
- "Error: Los datos proporcionados son incorrectos. Verifique el formato de las fechas"

## Estrategia de Pruebas

### Pruebas Unitarias

#### Modelo Feriado
- Validación de atributos y reglas de negocio
- Métodos de verificación de rangos y superposiciones
- Scopes y consultas personalizadas

#### Servicio FeriadoService
- Lógica de validación de superposiciones
- Algoritmos de verificación de rangos de fechas
- Integración con otros módulos del sistema

### Pruebas de Integración

#### Controlador FeriadoController
- Flujos completos de CRUD con validaciones
- Manejo de errores y respuestas HTTP correctas
- Integración con sistema de autenticación

#### Base de Datos
- Integridad referencial y constraints
- Performance de consultas con índices
- Transacciones y rollbacks en operaciones críticas

### Pruebas de Interfaz

#### Formularios Web
- Validación de campos en tiempo real
- Envío de formularios y manejo de respuestas
- Navegación y usabilidad del calendario

#### API Endpoints
- Respuestas JSON correctas para calendario
- Manejo de parámetros de filtrado y paginación
- Códigos de estado HTTP apropiados

### Casos de Prueba Críticos

#### Superposición de Fechas
- Feriado específico que coincide con día de rango existente
- Rangos que se superponen parcialmente
- Rangos completamente contenidos en otros rangos
- Múltiples feriados en la misma fecha

#### Integración con Otros Módulos
- Exclusión correcta de feriados en programación de horarios
- Cálculo preciso de asistencia excluyendo días no laborables
- Notificaciones a módulos dependientes tras cambios

#### Casos Límite
- Feriados en años bisiestos
- Rangos que cruzan cambios de año
- Feriados muy largos (varios meses)
- Eliminación de feriados con impacto en horarios existentes

## Consideraciones de Performance

### Optimizaciones de Base de Datos
- Índices compuestos para consultas de rango de fechas
- Particionamiento por año para tablas históricas grandes
- Cache de feriados frecuentemente consultados

### Optimizaciones de Aplicación
- Cache de feriados activos en memoria
- Lazy loading de relaciones no críticas
- Paginación eficiente en listados grandes

### Monitoreo y Métricas
- Tiempo de respuesta de validaciones de superposición
- Frecuencia de consultas a feriados por otros módulos
- Uso de cache y hit ratio de consultas frecuentes