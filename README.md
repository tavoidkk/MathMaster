# MathMaster 10

Aplicacion web educativa en PHP para practicar sumas mediante retos interactivos con fichas arrastrables.

## Caracteristicas

- Registro e inicio de sesion de usuarios.
- Tablero de retos por serie de ejercicios.
- Ejercicios de suma con interfaz drag-and-drop.
- Guardado de progreso por ejercicio (`pendiente`, `incorrecto`, `resuelto`).
- Sistema de puntaje acumulado por usuario.

## Tecnologias

- PHP (sin framework)
- MySQL/MariaDB (via PDO)
- HTML, CSS y JavaScript vanilla

## Estructura del proyecto

- `login.php`: autenticacion y migracion automatica de contrasenas en texto plano a hash.
- `registro.php`: creacion de usuarios.
- `logout.php`: cierre de sesion.
- `index.php`: panel principal de retos (serie base).
- `sumas_2dig.php`: segundo panel de retos (serie adicional).
- `ejercicio.php`: vista y logica de un reto individual.
- `logica_guardado.php`: endpoint JSON para guardar estado del reto.
- `ejercicios_lib.php`: funciones compartidas para generacion y reinicio de ejercicios.
- `db.php`: conexion a base de datos mediante PDO.
- `grup_grupo10proyecto.sql`: esquema SQL y datos de ejemplo.
- `guardar.php`: endpoint legacy para sumar puntos (no es el flujo principal actual).

## Requisitos

- PHP 8.0 o superior.
- MySQL o MariaDB.
- Servidor web local (por ejemplo XAMPP, Laragon, WAMP o Apache/Nginx + PHP).

## Instalacion y puesta en marcha

1. Clona o descarga el proyecto.
2. Crea una base de datos llamada `grup_grupo10proyecto`.
3. Importa el archivo `grup_grupo10proyecto.sql`.
4. Revisa credenciales en `db.php` y ajustalas a tu entorno si hace falta.
5. Coloca la carpeta del proyecto en el directorio publico de tu servidor local.
6. Abre en el navegador:
   - `http://localhost/miuni/login.php` (ruta tipica en entorno local)

## Flujo de uso

1. Registrarse en `registro.php`.
2. Iniciar sesion en `login.php`.
3. Entrar al panel de retos (`index.php` o `sumas_2dig.php`).
4. Abrir un ejercicio, ordenar fichas y verificar resultado.
5. Al acertar:
   - Se marca el reto como `resuelto`.
   - Se suma 1 punto al usuario.
6. Al fallar:
   - Se marca el reto como `incorrecto` para reintento.

## Modelo de datos (resumen)

### `usuarios`

- `id`
- `nombre_usuario` (unico)
- `password`
- `puntos_totales`

### `progreso_ejercicios`

- `id`
- `usuario_id` (FK -> `usuarios.id`)
- `ejercicio_n`
- `tipo` (por ejemplo `6d`, `6d2`, `2d`)
- `num1`, `num2`
- `estado` (`pendiente`, `incorrecto`, `resuelto`)

### `progreso`

Tabla historica incluida en el SQL, no es el eje principal del flujo actual.

## API interna

### POST `logica_guardado.php`

- Content-Type: `application/json`
- Body esperado:

```json
{
  "ejercicio_n": 1,
  "tipo": "6d",
  "accion": "resolver"
}
```

- `accion` opcional:
  - `resolver` (por defecto): marca resuelto y suma punto.
  - `incorrecto`: marca para reintento.

Respuesta JSON con `status` (`success` o `error`) y `message`.

## Seguridad y notas tecnicas

- Se usan consultas preparadas PDO para evitar inyecciones SQL.
- `login.php` migra automaticamente contrasenas antiguas en texto plano a hash en el primer login exitoso.
- Actualmente `db.php` contiene credenciales embebidas; se recomienda moverlas a variables de entorno para produccion.

## Mejoras recomendadas

- Mover configuracion sensible a `.env`.
- Agregar validaciones de entrada mas estrictas (rangos y tipos).
- Incluir CSRF token en formularios y endpoints.
- Separar estilos/JS en archivos estaticos.
- Agregar pruebas basicas del flujo de autenticacion y progreso.

## Licencia

No definida en el repositorio.
