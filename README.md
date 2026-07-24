# Registro de Datos Generales - ESCOM IPN

Plataforma web desarrollada para el registro y gestión de datos generales de alumnos de Nuevo Ingreso en la Escuela Superior de Cómputo (ESCOM) del Instituto Politécnico Nacional (IPN).

## Características Principales

*   **Registro de Alumnos:** Formulario validado para la captura de datos generales e información de contacto de estudiantes de nuevo ingreso.
*   **Autenticación y Control de Acceso:** Sistema de inicio de sesión seguro y protección de rutas para los perfiles registrados.
*   **Panel de Usuario:** Interfaz para la visualización de datos y generación de comprobantes de registro en formato PDF.
*   **Panel Administrativo:** Módulo para la consulta, actualización y eliminación de registros de estudiantes y gestión de grupos.
*   **Seguridad:** Implementación de sesiones, validación de datos en servidor/cliente y Google reCAPTCHA.
*   **Diseño Responsivo:** Interfaz adaptable a dispositivos móviles desarrollada con el framework Bootstrap.

## Tecnologías Utilizadas

*   **Frontend:** HTML5, CSS3, JavaScript, jQuery 4.0, Bootstrap.
*   **Backend:** PHP.
*   **Base de Datos:** MySQL (con la extensión `mysqli`).
*   **Dependencias Adicionales:** [FPDF](http://www.fpdf.org/) para la generación dinámica de documentos PDF.

## Estructura del Proyecto

*   `HTML/`: Estructura y vistas de las páginas (inicio, registro, login, cuenta, panel de administrador).
*   `CSS/`: Hojas de estilo personalizadas.
*   `JS/`: Lógica del cliente, validaciones y peticiones asíncronas.
*   `PHP/`: Controladores, conexión a base de datos, autenticación, operaciones CRUD y generación de PDF.
*   `DataBase/`: Archivos y scripts SQL correspondientes a la estructura de la base de datos.
*   `bootstrap/`: Archivos estáticos del framework CSS.
*   `fpdf186/`: Código fuente de la biblioteca FPDF.
*   `imagenes/`: Recursos gráficos y multimedia del sitio.

## Instrucciones de Instalación

1.  **Ubicación del proyecto:**
    Copie el directorio del proyecto (`Registro-de-datos-generales`) dentro del directorio público de su servidor web local (por ejemplo, `C:\xampp\htdocs\`).

2.  **Configuración de la Base de Datos:**
    *   Cree una nueva base de datos en MySQL con el nombre `registroexamen`.
    *   Importe el archivo SQL ubicado en el directorio `DataBase/` para establecer la estructura de las tablas.
    *   Las credenciales por defecto en `PHP/conexion.php` son:
        *   Host: `localhost`
        *   Usuario: `root`
        *   Contraseña: `(vacío)`
        *   Base de Datos: `registroexamen`

3.  **Configuración de Variables de Entorno (reCAPTCHA):**
    *   Diríjase al directorio `PHP/`.
    *   Renombre el archivo `config.example.php` a `config.php`.
    *   Edite `config.php` y reemplace el valor de prueba con su clave secreta de Google reCAPTCHA:
        ```php
        define('RECAPTCHA_SECRET', 'ingrese_aqui_su_clave_secreta');
        ```

4.  **Ejecución:**
    Inicie los servicios de Apache y MySQL. Posteriormente, acceda desde su navegador web a la siguiente ruta:
    `http://localhost/Registro-de-datos-generales/HTML/inicio.html`

## Consideraciones de Seguridad

*   El sistema requiere la verificación de sesión activa para el acceso a las vistas principales y operaciones del sistema.
*   Se recomienda encarecidamente migrar las consultas de base de datos existentes al uso de sentencias preparadas (Prepared Statements) para mitigar el riesgo de inyecciones SQL en entornos de producción.
*   El acceso al panel de administración debe restringirse mediante credenciales fuertes.

## Contribución

1.  Realice un *Fork* del repositorio.
2.  Cree una nueva rama para sus modificaciones (`git checkout -b feature/nombre-de-la-caracteristica`).
3.  Confirme sus cambios (`git commit -m 'Descripción de los cambios'`).
4.  Suba sus cambios a la rama (`git push origin feature/nombre-de-la-caracteristica`).
5.  Abra un *Pull Request* para su revisión.

## Notas Adicionales

Este sistema fue desarrollado para apoyar las actividades administrativas de alumnos de nuevo ingreso en ESCOM. En caso de despliegue en un entorno de producción, es necesario actualizar las rutas absolutas y los enlaces externos ubicados en los elementos globales de navegación.
