# 🧮 MathMaster

> [!IMPORTANT]
> **Proyecto Educativo:** Esta aplicación ha sido desarrollada como parte del programa académico del **Grupo 10**. Su objetivo es facilitar el aprendizaje de sumas de alta complejidad (6 dígitos) para niños de primaria.

Aplicación web interactiva construida en PHP que transforma la práctica matemática en un torneo de fútbol, utilizando una interfaz *Drag & Drop* para resolver desafíos.

---

## 🚀 Características Principales

* **Sistema de Torneos:** Tablero de retos organizado por series de dificultad.
* **Interfaz Dinámica:** Mecánica de "arrastrar y soltar" fichas numéricas.
* **Gestión de Usuarios:** Registro seguro con migración automática de seguridad.
* **Seguimiento en Vivo:** Persistencia del estado de los ejercicios (`pendiente`, `incorrecto`, `resuelto`).
* **Gamificación:** Sistema de puntos acumulados por cada ejercicio resuelto.

---

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 8.0+ (Vanilla)
* **Base de Datos:** MySQL / MariaDB mediante **PDO**.
* **Frontend:** HTML5, CSS3 (Animaciones personalizadas) y JavaScript Vanilla.

---

## 📁 Estructura del Proyecto

A continuación se detallan los archivos clave del sistema:

| Archivo | Función |
| :--- | :--- |
| `index.php` | **Dashboard:** Panel principal de retos (Vista de Estadio). |
| `ejercicio.php` | **Ejercicios:** Interfaz para resolver la suma. |
| `db.php` | Conexión segura a la base de datos. |
| `logica_guardado.php` | API interna que gestiona el progreso mediante JSON. |
| `login.php` / `registro.php` | Gestión de acceso y seguridad de cuentas. |

---

## ⚙️ Instalación y Configuración

> [!TIP]
> Para una mejor experiencia de desarrollo, se recomienda utilizar **VS Code** junto con un servidor local como **XAMPP** o **Laragon**.

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/tavoidkk/MathMaster.git](https://github.com/tu-usuario/MathMaster.git)
    ```
2.  **Configurar la Base de Datos:**
    * Crea una base de datos llamada `grup_grupo10proyecto`.
    * Importa el archivo `grup_grupo10proyecto.sql` proporcionado en la raíz.
3.  **Ajustar Credenciales:**
    * Edita `db.php` con tus datos locales (usuario `root` por defecto en XAMPP).

> [!WARNING]
> **Seguridad:** Asegúrate de que el archivo `db.php` esté incluido en tu `.gitignore` antes de realizar despliegues en servidores públicos para proteger tus credenciales.

---

## 🔄 Flujo de Usuario

1.  **Entrada:** El usuario se registra e inicia sesión.
2.  **Selección:** En el panel principal, elige un "Reto" disponible.
3.  **Juego:** Arrastra los números a las casillas de resultado.
4.  **Validación:**
    * **¡Correcto!:** Si es correcto, el estado cambia a `resuelto` y se suma un punto.
    * **IIncorrecto :(:** Si es incorrecto, se marca como tal para permitir el reintento.

---

## 🛡️ Notas Técnicas y Seguridad

> [!NOTE]
> **Migración de Hash:** El sistema cuenta con una lógica en `login.php` que detecta contraseñas en texto plano y las actualiza automáticamente a `password_hash()` de PHP en el primer inicio de sesión exitoso.

* **Consultas Preparadas:** Protección total contra Inyecciones SQL mediante el uso exclusivo de `prepare()` y `execute()` de PDO.
* **API Local:** La comunicación entre el juego y la base de datos se realiza de forma asíncrona mediante `fetch()` para evitar recargas de página innecesarias.

---

## ✒️ Autores

* **Gustavo Vidal** 
