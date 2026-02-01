#
#
¡Perfecto! Aquí tienes la **versión definitiva** y lista para producción. He unificado todos los puntos: la estructura de archivos con la carpeta `/api`, la automatización de la base de datos en Docker y las credenciales por defecto.



```markdown
# 🏫 API REST Reserva de Aulas
**Proyecto Final DAW** | Desarrollado con **PHP (PDO)** y **MySQL** | Testing con **Bruno**.

## 📂 Estructura del Proyecto
```text
.
├── api/                <-- Directorio de la Aplicación (PHP puro)
│   ├── .htaccess       <-- Reescritura de URLs para rutas REST
│   ├── Dockerfile      <-- Configuración del entorno PHP-Apache
│   ├── .env.example    <-- Plantilla de variables de entorno
│   └── ...             <-- Lógica (Controladores, Modelos, index.php)
├── sql/                <-- Persistencia de datos
│   ├── schema.sql      <-- Estructura de tablas
│   └── seed.sql        <-- Datos de ejemplo (Autodespliegue en Docker)
├── bruno_collection/   <-- Colección de pruebas para Bruno
├── docker-compose.yml  <-- Orquestador de servicios
└── README.md           <-- Manual de instrucciones

```

---

## 🚀 Guía de Despliegue con Docker 🐳

Esta opción es la recomendada, ya que **automatiza por completo** el entorno de servidor y la base de datos.

### 1. Preparar el directorio `api/`

Asegúrate de que el código fuente esté dentro de la carpeta `/api`. Puedes copiarlo manualmente o clonar el repositorio:

```bash
git clone [https://github.com/fj-herrera/API-REST-FULL-reserva-aulas.git](https://github.com/fj-herrera/API-REST-FULL-reserva-aulas.git) api

```

### 2. Configuración de Env

Es necesario configurar las credenciales dentro del directorio de la aplicación:

1. **Entra en la carpeta:** `cd api`
2. **Crea el archivo:** `cp .env.example .env`
3. **Edita el .env con los siguientes datos por defecto:**
* **DB_USER:** `admin`
* **DB_PASS:** `secreto`


4. **Sincronización:** Copia el archivo a la raíz para que el orquestador lo detecte: `cp .env ../.env`

### 3. Lanzamiento

Desde la raíz del proyecto, ejecuta:

```bash
docker-compose up -d --build

```

> [!IMPORTANT]
> **Automatización de Base de Datos:** Al levantar los contenedores, Docker detectará los archivos en la carpeta `/sql` e importará automáticamente tanto el esquema (`schema.sql`) como los datos de prueba (`seed.sql`). No es necesario realizar ninguna importación manual.

---

## 🛠️ Instalación Manual (Alternativa)

Si utilizas un entorno local como **XAMPP** o **Laragon**:

1. **Directorio de trabajo:** Copia el **directorio completo `/api**` directamente a la raíz de tu servidor web (ej. `C:/xampp/htdocs/api`).
2. **Configuración local:**
* Entra en la carpeta `api/` y crea el archivo `.env`: `cp .env.example .env`.
* Configura el usuario (`admin`) y contraseña (`secreto`) definidos para tu MySQL local.


3. **Base de Datos:** Importa manualmente `sql/schema.sql` y luego `sql/seed.sql` desde tu gestor (PHPMyAdmin).
4. **Servidor:** Verifica que el módulo `mod_rewrite` esté activo para que el `.htaccess` funcione.

---

## 🧪 Testing con Bruno

1. Importa la carpeta `bruno_collection/` en el cliente **Bruno**.
2. Configura la variable `{{base_url}}` según tu entorno:
* **Docker:** `http://localhost:8080`
* **Manual:** `http://localhost`



---

> **Repositorio oficial:** [API-REST-FULL-reserva-aulas](https://github.com/fj-herrera/API-REST-FULL-reserva-aulas.git)

```

**Siguiente paso recomendado:** ¿Te gustaría que verifiquemos el archivo `docker-compose.yml` para asegurar que el volumen de la base de datos apunta correctamente a esa carpeta `/sql`?

```