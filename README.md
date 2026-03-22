# LeadChain API - Backend Córdoba

**LeadChain** es una solución backend RESTful de alto rendimiento diseñada específicamente para la gestión inteligente de carteras de clientes, edificios y visitas comerciales en la ciudad de Córdoba.

Lo que hace única a esta API es su integración profunda con **PostgreSQL y la extensión PostGIS**. Esta arquitectura permite que LeadChain no solo almacene datos, sino que comprenda la ubicación espacial: puede validar si un comercial está dentro de su zona asignada, ubicar edificios en un mapa con precisión milimétrica y optimizar rutas de venta basadas en coordenadas geográficas reales.

## Guía de Despliegue

Para poner en marcha el proyecto, elige una de las dos opciones disponibles según tu entorno.

## Opción A: Docker (Recomendado)

### Requisitos para Opción A

- [Docker Desktop](https://www.docker.com/products/docker-desktop/): Esencial para la contenerización. Docker permite que la API y la base de datos corran en un entorno idéntico al de producción.
- [Git](https://git-scm.com/): Para la clonación y gestión del código fuente.

### Pasos de Instalación con Docker

Este método es el más limpio, ya que Docker se encarga de configurar PostGIS y PHP por ti de forma totalmente aislada.

1. **Clonar el repositorio:**

   ```bash
   git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git
   cd leadchain-backend
   ```
2. **Levantar los contenedores con un comando:**

   ```bash
   php artisan start:complete
   ```

   Este comando se encarga de todo: construye imágenes, levanta contenedores, ejecuta migraciones y carga datos iniciales.
3. **Acceso a la API:**

   - Swagger: `http://localhost:8000/api/documentation`
   - API raíz: `http://localhost:8000`

## Opción B: Instalación Local / Nativa

### Requisitos para Opción B

- **PHP 8.2 o superior**: Motor del lenguaje. En `php.ini` deben estar activas las extensiones `extension=sodium` (para cifrado de tokens) y `extension=pdo_pgsql` (comunicación con PostgreSQL).
- [Composer](https://getcomposer.org/): Gestor de dependencias de PHP.
- **PostgreSQL 15+ con PostGIS**: La extensión PostGIS es obligatoria para que las columnas de tipo `GEOMETRY` (ubicaciones de edificios) funcionen.

### Pasos de Instalación Local

Sigue estos pasos si prefieres ejecutar Laravel de forma nativa mientras usas Docker únicamente para la base de datos (Modo Híbrido).

1. **Preparar el código:**

   ```bash
   git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git
   cd leadchain-backend
   ```
2. **Instalar las dependencias y configurar entorno:**

   ```bash
   # Instalar librerías de Laravel
   composer install

   # Configurar archivo de entorno
   cp .env.example .env

   # Generar claves de seguridad
   php artisan key:generate
   php artisan jwt:secret
   ```
3. **Configurar Base de Datos en `.env`:**

   Asegúrate de tener:

   ```env
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=leadchain
   DB_USERNAME=root
   DB_PASSWORD=root
   SESSION_DRIVER=file
   ```
4. **Iniciar desarrollo con un comando:**

   ```bash
   php artisan start:hybrid
   ```

   Este comando levanta la BD en Docker, ejecuta migraciones, seeders y arranca el servidor local automáticamente.

   Acceso: `http://127.0.0.1:8000/api/documentation` (Swagger)

## Modelo de Accesos y Permisos

La seguridad se gestiona mediante **JWT (JSON Web Tokens)**. El sistema implementa una lógica de roles estricta:

| Recurso       | Administrador | Comercial   | Notas de Privacidad                                        |
| ------------- | ------------- | ----------- | ---------------------------------------------------------- |
| Clientes      | CRUD          | R (Lectura) | Los comerciales ven sus clientes pero no pueden borrarlos. |
| Zonas         | CRUD          | R (Lectura) | Gestión geográfica reservada a gerencia.                 |
| Usuarios      | CRUD          | -           | Datos de empleados privados.                               |
| Edificios     | CRUD          | R (Lectura) | Catálogo de puntos de interés comercial.                 |
| Visitas       | R / D         | C / R / U   | Gestión diaria de actividad comercial.                    |
| Estado Visita | R / U         | R / U       | Flujo de estados (Pendiente, Éxito, etc).                 |

## Comandos Personalizados

Este proyecto incluye cuatro comandos Artisan customizados para facilitar el desarrollo y despliegue:

### 1. `php artisan start:complete`

**Propósito:** Levanta el stack completo (app + BD) usando Docker Compose.

**Qué hace:**

- Ejecuta `docker compose up -d --build` para construir y levantar todos los contenedores.
- Espera a que el contenedor app esté completamente inicializado (artisan listo, vendor instalado).
- Ejecuta automáticamente `php artisan migrate --force` dentro del contenedor.
- Ejecuta automáticamente `php artisan db:seed --force` para cargar datos iniciales.

**Cuándo usarlo:** Primera vez que necesites levantar el proyecto completo en Docker o tras un `docker compose down -v`.

**Ejemplo:**

```bash
php artisan start:complete
```

---

### 2. `php artisan start:hybrid`

**Propósito:** Arranca la BD en Docker y la API en tu máquina local (Modo Híbrido).

**Qué hace:**

- Levanta solo el contenedor PostgreSQL con `docker compose up -d db`.
- Espera a que PostgreSQL esté completamente listo y funcional.
- Configura automáticamente `DB_HOST=127.0.0.1` para que Laravel local pueda conectar.
- Ejecuta `php artisan migrate --force` con reintentos automáticos (tolera fallos transitorios).
- Ejecuta `php artisan db:seed --force` para cargar datos iniciales.
- Limpia cachés con `php artisan optimize:clear`.
- Arranca el servidor local en `http://127.0.0.1:8000`.

**Ventajas:** Mejor para desarrollo local, más rápido que Docker completo, acceso directo a logs sin contenedor.

**Ejemplo:**

```bash
php artisan start:hybrid
```

---

### 3. `php artisan retest`

**Propósito:** Ejecuta automáticamente el flujo completo de pruebas (reset + seed + tests).

**Qué hace:**

- Limpia la BD con `php artisan migrate:refresh` (borra todos los datos).
- Carga datos de prueba con `php artisan db:seed`.
- Ejecuta todos los tests unitarios y de feature con `php artisan test`.

**Cuándo usarlo:** Antes de mergear cambios, para asegurar que nada está roto.

**Ejemplo:**

```bash
php artisan retest
```

---

### 4. `php artisan api:tree`

**Propósito:** Visualiza la estructura y endpoints de la API de forma simplificada.

**Qué hace:**

- Muestra un árbol de rutas organizadas jerárquicamente.
- Indica método HTTP (GET, POST, PUT, DELETE).
- Agrupa por controlador.

**Cuándo usarlo:** Para documentar rápidamente o explorar los endpoints sin abrir el Swagger.

**Ejemplo:**

```bash
php artisan app:tree
```

## Estructura del Sistema

```bash
Estructura del proyecto (filtrada):
├── .dockerignore
├── .env
├── .env.example
├── README.md
├── app
│   ├── Console
│   │   └── Commands
│   │       ├── EstructuraProyecto.php
│   │       ├── ResetAndTest.php
│   │       ├── StartComplete.php
│   │       └── StartHybrid.php
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
    └── Fixture
        ├── ClienteData.php
        ├── EdificioData.php
        ├── LoginData.php
        ├── UserData.php
        ├── VisitasData.php
        └── ZonaData.php
```

**Desarrollado por:** Alberto Romero Pino - [GitHub](https://github.com/AlbertoRomeroPino "null")
