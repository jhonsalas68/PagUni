# API Universidad - Documentación

## Descripción
Sistema de gestión universitaria con API REST para manejar facultades, carreras, materias, profesores, estudiantes e inscripciones.

## Estructura de la Base de Datos

### Entidades Principales:
- **Facultades**: Organizaciones académicas principales
- **Carreras**: Programas académicos dentro de las facultades
- **Materias**: Asignaturas de cada carrera
- **Profesores**: Docentes de la universidad
- **Estudiantes**: Alumnos matriculados
- **Inscripciones**: Relación entre estudiantes, materias y profesores

## Endpoints de la API

### Facultades
- `GET /api/facultades` - Listar todas las facultades
- `POST /api/facultades` - Crear nueva facultad
- `GET /api/facultades/{id}` - Obtener facultad específica
- `PUT /api/facultades/{id}` - Actualizar facultad
- `DELETE /api/facultades/{id}` - Eliminar facultad

### Carreras
- `GET /api/carreras` - Listar todas las carreras
- `POST /api/carreras` - Crear nueva carrera
- `GET /api/carreras/{id}` - Obtener carrera específica
- `PUT /api/carreras/{id}` - Actualizar carrera
- `DELETE /api/carreras/{id}` - Eliminar carrera

### Materias
- `GET /api/materias` - Listar todas las materias
- `POST /api/materias` - Crear nueva materia
- `GET /api/materias/{id}` - Obtener materia específica
- `PUT /api/materias/{id}` - Actualizar materia
- `DELETE /api/materias/{id}` - Eliminar materia

### Profesores
- `GET /api/profesores` - Listar todos los profesores
- `POST /api/profesores` - Crear nuevo profesor
- `GET /api/profesores/{id}` - Obtener profesor específico
- `PUT /api/profesores/{id}` - Actualizar profesor
- `DELETE /api/profesores/{id}` - Eliminar profesor

### Estudiantes
- `GET /api/estudiantes` - Listar todos los estudiantes
- `POST /api/estudiantes` - Crear nuevo estudiante
- `GET /api/estudiantes/{id}` - Obtener estudiante específico
- `PUT /api/estudiantes/{id}` - Actualizar estudiante
- `DELETE /api/estudiantes/{id}` - Eliminar estudiante

### Inscripciones
- `GET /api/inscripciones` - Listar todas las inscripciones
- `POST /api/inscripciones` - Crear nueva inscripción
- `GET /api/inscripciones/{id}` - Obtener inscripción específica
- `PUT /api/inscripciones/{id}` - Actualizar inscripción
- `DELETE /api/inscripciones/{id}` - Eliminar inscripción
- `PUT /api/inscripciones/{id}/calificar` - Calificar estudiante

## Ejemplos de Uso

### Crear una Facultad
```json
POST /api/facultades
{
    "nombre": "Facultad de Ingeniería",
    "codigo": "ING",
    "descripcion": "Facultad dedicada a las ingenierías"
}
```

### Crear un Estudiante
```json
POST /api/estudiantes
{
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan.perez@estudiante.edu",
    "cedula": "12345678",
    "codigo_estudiante": "ISC2024001",
    "fecha_nacimiento": "2000-01-15",
    "carrera_id": 1,
    "telefono": "555-0001",
    "direccion": "Calle Principal 123"
}
```

### Calificar una Inscripción
```json
PUT /api/inscripciones/1/calificar
{
    "nota_final": 4.5
}
```

## Comandos Útiles

### Ejecutar migraciones
```bash
php artisan migrate
```

### Ejecutar seeders
```bash
php artisan db:seed
```

### Refrescar base de datos con datos de ejemplo
```bash
php artisan migrate:fresh --seed
```

### Iniciar servidor de desarrollo
```bash
php artisan serve
```

## Datos de Ejemplo
El sistema incluye datos de ejemplo con:
- 2 Facultades (Ingeniería y Ciencias)
- 2 Carreras (ISC y Matemáticas)
- 3 Materias
- 2 Profesores
- 2 Estudiantes