
# LeadChain API - Backend Córdoba 🏢📍

**LeadChain** es una solución backend RESTful de alto rendimiento diseñada específicamente para la gestión inteligente de carteras de clientes, edificios y visitas comerciales en la ciudad de Córdoba.

Lo que hace única a esta API es su integración profunda con  **PostgreSQL y la extensión PostGIS** . Esta arquitectura permite que LeadChain no solo almacene datos, sino que comprenda la ubicación espacial: puede validar si un comercial está dentro de su zona asignada, ubicar edificios en un mapa con precisión milimétrica y optimizar rutas de venta basadas en coordenadas geográficas reales.

## 🚀 Guía de Despliegue (Deployment)

Para poner en marcha el proyecto, primero revisa los componentes necesarios según el método que elijas.

### 📋 1. Requisitos e Información Técnica

Antes de comenzar, asegúrate de contar con las herramientas necesarias según tu preferencia de ejecución:

#### **Para la Opción A (Docker - Recomendado)**

* [**Docker Desktop**](https://www.docker.com/products/docker-desktop/ "null")**:** Esencial para la "contenerización". Docker permite que la API y la base de datos corran en un entorno idéntico al de producción, evitando conflictos de versiones de PHP o librerías de sistema.
* [**Git**](https://git-scm.com/ "null")**:** Para la clonación y gestión del código fuente.

#### **Para la Opción B (Instalación Local / Nativa)**

* **PHP 8.2 o superior:** El motor del lenguaje. Es vital que en tu archivo `php.ini` estén activas las extensiones `extension=sodium` (para el cifrado de tokens) y `extension=pdo_pgsql` (para la comunicación con PostgreSQL).
* [**Composer**](https://getcomposer.org/ "null")**:** El gestor de dependencias de PHP que descargará Laravel y todas las librerías necesarias.
* **PostgreSQL 15+ con PostGIS:** La base de datos debe tener instalada la extensión PostGIS. Sin ella, las columnas de tipo `GEOMETRY` (donde se guardan las ubicaciones de los edificios) no funcionarán.

### 🐳 Opción A: Pasos para Instalar con Docker (Recomendado)

Este método es el más limpio, ya que Docker se encarga de configurar PostGIS y PHP por ti de forma totalmente aislada.

1. **Clonar el repositorio:**

   ```
   git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git
   cd leadchain-backend
   ```
2. **Levantar los contenedores:**
   Este comando descarga las imágenes, instala dependencias y configura la red interna.

   ```
   docker compose up --build -d

   ```
3. **Carga de datos de prueba:**
   Para que la API no esté vacía, inyectamos datos realistas de Córdoba (usuarios, zonas y clientes):

   ```
   docker compose exec app php artisan db:seed --force

   ```

   *📍 Acceso: La API estará lista en `http://localhost:8000`.*

### 💻 Opción B: Pasos para Instalación Local (Nativa)

Sigue estos pasos si prefieres ejecutar la aplicación Laravel de forma nativa mientras usas Docker únicamente para la base de datos (Modo Híbrido).

1. **Preparar el código:**

   ```
   git clone [https://github.com/AlbertoRomeroPino/Leadchain-backend.git](https://github.com/AlbertoRomeroPino/Leadchain-backend.git)
   cd leadchain-backend
   ```
2. **Configurar Docker para Base de Datos:**
   Para este modo, debes modificar tu archivo `docker-compose.yml` para que  **solo dockerice PostgreSQL y PostGIS** . Sustituye el contenido de tu archivo por el siguiente bloque:

   ```
   services:
     db:
       image: postgis/postgis:15-3.3 # Postgres 15 con PostGIS 3.3
       container_name: postgres_leadchain
       restart: always
       environment:
         POSTGRES_USER: root
         POSTGRES_PASSWORD: root
         POSTGRES_DB: leadchain
       ports:
         - "5432:5432"
       # volumes:
         # PostgreSQL ejecuta automáticamente los .sql en /docker-entrypoint-initdb.d/
         # - ./init-db/db-leadchain.sql:/docker-entrypoint-initdb.d/db-leadchain.sql
         # - postgres_data:/var/lib/postgresql/data

   volumes:
     postgres_data:
   ```

   *Levanta la base de datos con:* `docker compose up -d db`
3. **Instalar dependencias y Configurar Entorno:**

   ```
   # Instalar librerías de Laravel
   composer install

   # Configurar archivo de entorno
   cp .env.example .env

   # Generar claves de seguridad únicas para tu instancia
   php artisan key:generate
   php artisan jwt:secret
   ```
4. **Configurar Base de Datos:**
   Edita el archivo `.env` asegurándote de que el `DB_HOST` sea `127.0.0.1`. Luego, crea las tablas y carga los datos:

   ```
   php artisan migrate --seed

   ```
5. **Lanzar el Servidor:**

   ```
   php artisan serve

   ```

## 🔐 Modelo de Accesos y Permisos

La seguridad se gestiona mediante  **JWT (JSON Web Tokens)** . El sistema implementa una lógica de roles estricta:

| Recurso                 | Administrador | Comercial   | Notas de Privacidad                                        |
| ----------------------- | ------------- | ----------- | ---------------------------------------------------------- |
| **Clientes**      | CRUD          | R (Lectura) | Los comerciales ven sus clientes pero no pueden borrarlos. |
| **Zonas**         | CRUD          | R (Lectura) | Gestión geográfica reservada a gerencia.                 |
| **Usuarios**      | CRUD          | -           | Datos de empleados privados.                               |
| **Edificios**     | CRUD          | R (Lectura) | Catálogo de puntos de interés comercial.                 |
| **Visitas**       | R / D         | C / R / U   | Gestión diaria de actividad comercial.                    |
| **Estado Visita** | R / U         | R / U       | Flujo de estados (Pendiente, Éxito, etc).                 |

## 🛠️ Comandos de Utilidad Personalizados

* **Mapa de Arquitectura (`php artisan api:tree`):** Visualización simplificada de la estructura.
* **Garantía de Calidad (`php artisan retest`):** Limpia la base de datos, ejecuta seeders y lanza los tests unitarios.

## 📂 Estructura del Sistema

```
Estructura del proyecto (filtrada):
├── .dockerignore
├── .env
├── .env.example
├── NUL
├── README-PROFESIONAL.md
├── README.md
├── Recordatorio.md
├── app
│   ├── Console
│   │   └── Commands
│   │       ├── EstructuraProyecto.php
│   │       └── ResetAndTest.php
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Api
│   │   │   │   └── AuthController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── Controller.php
│   │   │   ├── EdificioController.php
│   │   │   ├── EstadoVisitaController.php
│   │   │   ├── UserController.php
│   │   │   ├── VisitaController.php
│   │   │   └── ZonaController.php
│   │   ├── Middleware
│   │   │   ├── CheckRole.php
│   │   │   └── LoginAdmin.php
│   │   ├── Requests
│   │   │   ├── AuthRequest.php
│   │   │   ├── ClienteRequest.php
│   │   │   ├── EdificioRequest.php
│   │   │   ├── UserRequest.php
│   │   │   ├── UserUpdateRequest.php
│   │   │   ├── VisitaRequest.php
│   │   │   └── ZonaRequest.php
│   │   └── Resources
│   │       ├── ClienteResource.php
│   │       ├── EdificioResource.php
│   │       ├── EstadoVisitaResource.php
│   │       ├── UserResource.php
│   │       ├── VisitaResource.php
│   │       └── ZonaResource.php
│   ├── Models
│   │   ├── Cliente.php
│   │   ├── Edificio.php
│   │   ├── EstadoVisita.php
│   │   ├── User.php
│   │   ├── Visita.php
│   │   └── Zona.php
│   └── OpenApi
│       └── OpenApiSpec.php
├── config
│   ├── auth.php
│   ├── cors.php
│   ├── jwt.php
│   └── l5-swagger.php
├── database
│   ├── migrations
│   │   ├── 2026_03_09_000001_create_zonas_table.php
│   │   ├── 2026_03_09_000002_create_estados_visita_table.php
│   │   ├── 2026_03_09_000003_create_users_table.php
│   │   ├── 2026_03_09_000006_create_clientes_table.php
│   │   ├── 2026_03_09_000007_create_edificios_table.php
│   │   ├── 2026_03_09_000008_create_visitas_table.php
│   │   └── 2026_03_19_000010_convert_zonas_points_to_polygon.php
│   └── seeders
│       ├── ClienteSeeder.php
│       ├── DatabaseSeeder.php
│       ├── EdificioSeeder.php
│       ├── EstadoVisitaSeeder.php
│       ├── UserSeeder.php
│       ├── VisitaSeeder.php
│       └── ZonaSeeder.php
├── docker-compose.yml
├── dockerfile
├── init-db
│   └── db-leadchain.sql
├── package.json
├── routes
│   ├── api.php
│   ├── console.php
│   └── web.php
└── tests
    ├── Feature
    │   ├── ClienteTest.php
    │   ├── EdificioTest.php
    │   ├── LoginTest.php
    │   ├── UserTest.php
    │   ├── VisitasTest.php
    │   └── ZonaTest.php
    ├── Fixture
    │   ├── ClienteData.php
    │   ├── EdificioData.php
    │   ├── LoginData.php
    │   ├── UserData.php
    │   ├── VisitasData.php
    │   └── ZonaData.php
    ├── TestCase.php
    └── Unit
        └── ExampleTest.php
```

**Desarrollado por:** Alberto Romero Pino - [GitHub](https://github.com/AlbertoRomeroPino "null")
