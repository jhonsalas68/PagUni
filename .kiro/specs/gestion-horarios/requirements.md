# Requirements Document - Gestión de Horarios y Asignación Académica

## Introduction

Sistema integral de gestión de horarios académicos que permite registrar materias con grupos, gestionar aulas/laboratorios, asignar carga académica a docentes y generar horarios automáticos o manuales con validación de conflictos.

## Glossary

- **Materia_Grupo**: Combinación de materia académica con sus grupos específicos (A, B, C)
- **Carga_Academica**: Asignación de materias y grupos a un docente específico
- **Horario_Matriz**: Sistema de horarios que evita conflictos de docentes y aulas
- **Validacion_Disponibilidad**: Verificación de que docente y aula estén libres en horario específico
- **Asignacion_Automatica**: Algoritmo que genera horarios óptimos respetando restricciones
- **Codigo_Aula**: Identificador único del espacio físico (ej: L-301, A-205)

## Requirements

### Requirement 6 - Registro de Materia y Grupos

**User Story:** Como Administrador/Coordinador, quiero registrar una materia con sus grupos correspondientes, para organizar la oferta académica por secciones.

#### Acceptance Criteria

1. WHEN el Administrador accede al registro de materias, THE Sistema_Horarios SHALL mostrar campos para nombre de materia, código de materia e identificadores de grupos
2. THE Sistema_Horarios SHALL permitir agregar múltiples grupos (A, B, C, etc.) para una misma materia
3. WHEN se ingresa un código de materia, THE Sistema_Horarios SHALL verificar que no esté duplicado en la base de datos
4. WHEN el registro es exitoso, THE Sistema_Horarios SHALL vincular automáticamente todos los grupos a la materia
5. IF el código ya existe, THEN THE Sistema_Horarios SHALL mostrar "Error: El Código de Materia ya existe o faltan identificadores de grupo"
6. WHEN el registro es exitoso, THE Sistema_Horarios SHALL mostrar "Materia y Grupos Registrados Exitosamente"

### Requirement 7 - Registro de Aula/Laboratorio

**User Story:** Como Administrador/Coordinador, quiero registrar aulas y laboratorios con sus características, para tener un inventario completo de espacios disponibles.

#### Acceptance Criteria

1. WHEN el Administrador registra un aula, THE Sistema_Horarios SHALL solicitar código único de aula, capacidad y tipo de recurso
2. THE Sistema_Horarios SHALL ofrecer opciones de tipo: "Aula Común" y "Laboratorio"
3. WHEN se ingresa un código de aula, THE Sistema_Horarios SHALL verificar unicidad para evitar espacios duplicados
4. THE Sistema_Horarios SHALL validar que todos los campos obligatorios estén completos
5. IF el código ya existe, THEN THE Sistema_Horarios SHALL mostrar "Error: El Código de Aula ya existe o faltan datos obligatorios"
6. WHEN el registro es exitoso, THE Sistema_Horarios SHALL mostrar "Aula Registrada Exitosamente"

### Requirement 8 - Asignación de Carga Académica

**User Story:** Como Administrador/Coordinador, quiero asignar materias y grupos específicos a cada docente, para definir su carga académica del período.

#### Acceptance Criteria

1. WHEN el Administrador asigna carga académica, THE Sistema_Horarios SHALL permitir seleccionar un docente de la lista
2. THE Sistema_Horarios SHALL mostrar lista de materias/grupos disponibles para asignación
3. THE Sistema_Horarios SHALL permitir selección múltiple de grupos/materias para el docente
4. WHEN se confirma la asignación, THE Sistema_Horarios SHALL registrar vinculación formal en tabla de carga académica
5. THE Sistema_Horarios SHALL verificar existencia de materia/grupo antes de asignar
6. IF la materia/grupo no existe, THEN THE Sistema_Horarios SHALL mostrar "Error al asignar la carga. Verifique la existencia de la Materia/Grupo"
7. WHEN la asignación es exitosa, THE Sistema_Horarios SHALL mostrar "Carga Académica Asignada Exitosamente al Docente"

### Requirement 9 - Generación Automática de Horarios

**User Story:** Como Administrador/Coordinador, quiero generar horarios automáticamente, para optimizar el uso de recursos y evitar conflictos de programación.

#### Acceptance Criteria

1. WHEN el Administrador activa generación automática, THE Sistema_Horarios SHALL ejecutar algoritmo de optimización de horarios
2. THE Sistema_Horarios SHALL respetar todas las asignaciones de carga académica existentes
3. THE Sistema_Horarios SHALL garantizar ausencia total de cruces de docentes y aulas
4. THE Sistema_Horarios SHALL generar matriz de horarios óptima considerando restricciones
5. WHEN se genera propuesta, THE Sistema_Horarios SHALL requerir aprobación del administrador antes de publicar
6. IF existen conflictos irresolubles, THEN THE Sistema_Horarios SHALL mostrar "Error: Imposible Generar Horarios, hay conflictos de recursos irresolubles"
7. WHEN el administrador aprueba, THE Sistema_Horarios SHALL mostrar "Horarios Generados y Publicados Exitosamente"

### Requirement 10 - Asignación Manual de Horarios

**User Story:** Como Administrador/Coordinador, quiero asignar horarios específicos manualmente, para tener control detallado sobre la programación académica.

#### Acceptance Criteria

1. WHEN el Administrador asigna horario manual, THE Sistema_Horarios SHALL solicitar selección de docente, materia/grupo, día, hora inicio, hora fin y aula
2. THE Sistema_Horarios SHALL mostrar listas desplegables con opciones disponibles para cada campo
3. WHEN se intenta guardar asignación, THE Sistema_Horarios SHALL ejecutar validación de disponibilidad
4. THE Sistema_Horarios SHALL verificar que la tripleta Docente-Aula-Horario esté libre
5. THE Sistema_Horarios SHALL validar que no existan cruces de recursos en el horario especificado
6. IF existe conflicto, THEN THE Sistema_Horarios SHALL mostrar "Error: Los recursos generan un cruce. El Docente o el Aula ya están ocupados en ese horario"
7. WHEN la asignación es válida, THE Sistema_Horarios SHALL mostrar "Clase Asignada Exitosamente"

### Requirement 11 - Validación de Disponibilidad (Implícito)

**User Story:** Como sistema, quiero validar automáticamente la disponibilidad de recursos, para prevenir conflictos de programación.

#### Acceptance Criteria

1. WHEN se solicita validación de disponibilidad, THE Sistema_Horarios SHALL verificar disponibilidad de docente en horario específico
2. THE Sistema_Horarios SHALL verificar disponibilidad de aula en horario específico
3. THE Sistema_Horarios SHALL verificar que no existan solapamientos de tiempo
4. THE Sistema_Horarios SHALL considerar horarios ya asignados en el período académico
5. THE Sistema_Horarios SHALL retornar resultado booleano de disponibilidad
6. THE Sistema_Horarios SHALL proporcionar detalles del conflicto si existe
7. THE Sistema_Horarios SHALL ser invocado automáticamente antes de cualquier asignación de horario