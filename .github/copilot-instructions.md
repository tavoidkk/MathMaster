# Copilot instructions

## Build, test, lint
- No build, test, or lint tooling is defined in this repository.

## High-level architecture
- `db.php` centralizes the MySQL PDO connection (`$pdo`) used by all PHP pages.
- Auth flow: `registro.php` inserts a new user, `login.php` validates credentials and sets `$_SESSION['usuario_id']` and `$_SESSION['nombre']`, `logout.php` clears the session.
- Game flow: `index.php` is the dashboard that lists 8 fixed challenges and marks them complete based on `progreso_ejercicios`, while `ejercicio.php` renders the drag-and-drop sum and reads the `ejercicio` query param.
- Progress persistence: `ejercicio.php` posts JSON to `logica_guardado.php`, which upserts the exercise status in `progreso_ejercicios` and returns JSON.
- Database schema and sample data live in `grup_grupo10proyecto.sql` (`usuarios`, `progreso_ejercicios`, `progreso`).

## Key conventions
- All authenticated pages call `session_start()` at the top and gate access with `$_SESSION['usuario_id']`.
- Use `include 'db.php'` and the shared `$pdo` variable for all database operations.
- `logica_guardado.php` expects a JSON body (`Content-Type: application/json`) with `ejercicio_n` and responds with JSON.
- The exercise number is always passed as `?ejercicio=N` to `ejercicio.php` and used for progress tracking.
