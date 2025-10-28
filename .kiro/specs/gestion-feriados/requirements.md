# CU-13: Gestionar Días No Laborables/Feriados

## Introducción

El CU-13 permite al Administrador registrar, modificar o eliminar fechas especiales (feriados, recesos, asuetos) para que el sistema las excluya de la planificación académica y del cálculo de la asistencia.

## Glossario

- **Administrador**: Usuario con permisos para gestionar el sistema académico
- **Día No Laborable**: Fecha en la que no se realizan actividades académicas
- **Feriado**: Día festivo oficial o institucional
- **Receso**: Período de descanso académico
- **Asueto**: Día libre por disposición institucional
- **Sistema de Planificación**: Módulo que programa horarios y clases
- **Sistema de Asistencia**: Módulo que registra y calcula asistencia

## Requisitos

### Requisito 1: Acceso Restringido

**User Story:** Como Administrador, quiero que solo usuarios autorizados puedan gestionar días no laborables, para mantener la integridad del calendario académico.

#### Acceptance Criteria

1. WHEN un usuario no autorizado intenta acceder, THE Sistema SHALL denegar el acceso
2. WHEN un Administrador accede al módulo, THE Sistema SHALL mostrar las opciones de gestión
3. THE Sistema SHALL validar permisos antes de cada operación
4. IF el usuario no es Administrador, THEN THE Sistema SHALL redirigir al login
5. THE Sistema SHALL registrar todos los accesos al módulo en logs de auditoría

### Requisito 2: Operaciones CRUD de Feriados

**User Story:** Como Administrador, quiero registrar, modificar y eliminar días no laborables, para mantener actualizado el calendario académico.

#### Acceptance Criteria

1. WHEN el Administrador selecciona "Registrar Nuevo Feriado", THE Sistema SHALL mostrar formulario de registro
2. WHEN el Administrador selecciona "Modificar Feriado", THE Sistema SHALL mostrar lista de feriados existentes
3. WHEN el Administrador selecciona "Eliminar Feriado", THE Sistema SHALL solicitar confirmación
4. THE Sistema SHALL permitir operaciones sobre fechas específicas y rangos de fechas
5. THE Sistema SHALL mantener historial de cambios para auditoría

### Requisito 3: Campos de Entrada y Validación

**User Story:** Como Administrador, quiero especificar fechas y descripciones de feriados, para documentar correctamente los días no laborables.

#### Acceptance Criteria

1. THE Sistema SHALL permitir entrada de fecha específica en formato DD/MM/YYYY
2. THE Sistema SHALL permitir entrada de rango de fechas con fecha inicio y fecha fin
3. THE Sistema SHALL requerir descripción del evento con máximo 255 caracteres
4. THE Sistema SHALL validar que las fechas sean válidas y futuras o actuales
5. WHERE se especifica rango de fechas, THE Sistema SHALL validar que fecha fin sea posterior a fecha inicio

### Requisito 4: Validación de Superposición

**User Story:** Como Administrador, quiero que el sistema evite superposiciones de feriados, para mantener consistencia en el calendario.

#### Acceptance Criteria

1. WHEN se registra nueva fecha, THE Sistema SHALL verificar superposición con feriados existentes
2. IF existe superposición, THEN THE Sistema SHALL mostrar error específico
3. THE Sistema SHALL permitir modificación de feriados existentes sin conflicto
4. THE Sistema SHALL validar rangos de fechas completos contra base de datos
5. THE Sistema SHALL sugerir fechas alternativas en caso de conflicto

### Requisito 5: Impacto en Planificación Académica

**User Story:** Como Sistema de Planificación, quiero excluir días no laborables de la programación, para evitar asignar clases en fechas no válidas.

#### Acceptance Criteria

1. THE Sistema SHALL marcar fechas registradas como no lectivas en base de datos
2. WHEN se programa horario automático, THE Sistema SHALL excluir días no laborables
3. THE Sistema SHALL actualizar calendarios existentes al registrar nuevo feriado
4. THE Sistema SHALL notificar cambios a módulos dependientes
5. THE Sistema SHALL mantener integridad referencial con horarios programados

### Requisito 6: Impacto en Sistema de Asistencia

**User Story:** Como Sistema de Asistencia, quiero excluir días no laborables del cálculo, para mantener precisión en reportes de asistencia.

#### Acceptance Criteria

1. THE Sistema SHALL excluir días no laborables del cálculo de asistencia
2. THE Sistema SHALL no esperar registro de asistencia en días no laborables
3. WHEN se calcula porcentaje de asistencia, THE Sistema SHALL usar solo días lectivos
4. THE Sistema SHALL generar reportes excluyendo días no laborables
5. THE Sistema SHALL mantener histórico de días excluidos para auditoría

### Requisito 7: Mensajes de Retroalimentación

**User Story:** Como Administrador, quiero recibir confirmación clara de las operaciones, para saber el resultado de mis acciones.

#### Acceptance Criteria

1. WHEN operación es exitosa, THE Sistema SHALL mostrar "Gestión de Feriados Exitosa"
2. IF existe superposición, THEN THE Sistema SHALL mostrar "Error: La fecha se superpone con un feriado ya registrado"
3. IF datos son incorrectos, THEN THE Sistema SHALL mostrar "Error: Los datos son incorrectos"
4. THE Sistema SHALL mostrar mensajes específicos para cada tipo de error
5. THE Sistema SHALL incluir detalles del conflicto en mensajes de error

### Requisito 8: Interfaz de Usuario

**User Story:** Como Administrador, quiero una interfaz intuitiva para gestionar feriados, para realizar operaciones de manera eficiente.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar calendario visual con feriados marcados
2. THE Sistema SHALL permitir selección de fechas mediante calendario interactivo
3. THE Sistema SHALL mostrar lista paginada de feriados existentes
4. THE Sistema SHALL permitir búsqueda y filtrado de feriados por fecha o descripción
5. THE Sistema SHALL mostrar estadísticas de días no laborables por período