# LeadChain API - Backend Córdoba

LeadChain es una **API-Rest** diseñada para la gestión de clientes, edificios y visitas comerciales en la ciudad de Córdoba. Utiliza **PostgreSQL con PostGIS** para manejar ubicaciones geográficas exactas, permitiendo visualizar mapas y zonas de venta en tiempo real.

---

## Tecnologías utilizadas

* **Lenguaje:** PHP 8.x (Laravel Framework)
* **Base de Datos:** PostgreSQL 15 + PostGIS (Extensión espacial)
* **Contenedores:** Docker & Docker Compose
* **Gestión de BD:** DBeaver (Recomendado)

---

## Instalación y Despliegue

Sigue estos pasos para tener el entorno funcionando en menos de 5 minutos:

### 1. Requisitos Previos

Asegúrate de tener instalados:

* [Docker Desktop](https://www.docker.com/products/docker-desktop/)
* [Git](https://git-scm.com/)
* [Composer](https://getcomposer.org/)

Comprueba que php.ini tenga descomentado las lineas:

- `extension=sodium`
- `extension=pdo_pgsql`

> ⚠️ Descomentado significa sin el `;` delante

### 2. Clonar y Preparar el Proyecto

```bash
# Clonar el repositorio
git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git
cd leadchain-backend

# Instalar dependencias de PHP
composer install
```

### 3. Configurar Entorno y Seguridad

Copia el archivo de `.env.example`, modificalo a `.env` y genera las claves necesarias:

```bash
cp .env.example .env

# Generar clave de la aplicación y el secreto para JWT
php artisan key:generate
php artisan jwt:secret
```

**Verificación en el `.env`:**
Asegúrate de que las variables de la base de datos coincidan con el entorno Docker y que el algoritmo JWT sea el correcto:

**Fragmento de código de ejemplo**

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=leadchain
DB_USERNAME=root
DB_PASSWORD=root

JWT_ALGO=HS256
```

### 4. Levantar la infraestructura (Docker)

Este comando levanta el contenedor de **PostgreSQL/PostGIS** necesario para el almacenamiento de datos geográficos.

**Bash**

```
docker-compose up -d
```

> **Nota sobre persistencia:** Si decides descomentar la sección de `volumes` en tu `docker-compose.yml`, los datos se mantendrán aunque borres el contenedor. Si usas el script de inicialización automático (`init-db`), es posible que no necesites el siguiente paso.

### 5 Lanzar migraciones, seeder y test

Esto no es un comando nativo pero lo e creado para lanzarlo. Esto realiza las 3 acciones al mismo tiempo y te muestra si los test a finalizado correctamente.

```bash
php artisan retest
```

### 6. Ejecutar la API

Una vez que la base de datos está lista y las dependencias instaladas, puedes poner en marcha el servidor de desarrollo:

**Bash**

```
php artisan serve
```

La API estará disponible en: `http://127.0.0.1:8000`

> (👉ﾟヮﾟ)👉Si pulsas CTRL + CLIC  en http://127.0.0.1:8000 te mostrara los endpoints 👈(ﾟヮﾟ👈)

---

## Comando creado

Creado para poder hacer una estrutura de proyecto personalizada para mostrar los apartados que yo queria y quitar los inecesario.

```bash
php artisan api:tree
```

Comando que resetea la base de datos, ejecuta Seeders y lanza los tests

```bash
php artisan retest
```

---

## Permisos de usuario

CRUD = Create, Read, Update, Delete

|   Recurso   |  Admin  | Comercial |
| :----------: | :-----: | :-------: |
|   Clientes   |  CRUD  |  - R - -  |
|    Zonas    |  CRUD  |  - R - -  |
|   Usuarios   |  CRUD  |  - - - -  |
|  Edificios  |  CRUD  |  - R - -  |
|   Visitas   | - R - D |  C-R-U -  |
| EstadoVisita | - - U - |  - - U -  |

---

## Estructura del Proyecto

```bash
Estructura del proyecto (filtrada):
├── .env
├── .env.example
├── README.md
├── app
│   ├── Console
│   │   └── Commands
│   │       └── EstructuraProyecto.php
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
│   │   │   ├── EstadoVisitaRequest.php
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
│   └── Models
│       ├── Cliente.php
│       ├── Edificio.php
│       ├── EstadoVisita.php
│       ├── User.php
│       ├── Visita.php
│       └── Zona.php
├── config
│   ├── auth.php
│   └── jwt.php
├── database
│   ├── migrations
│   │   ├── 2026_03_09_000001_create_zonas_table.php
│   │   ├── 2026_03_09_000002_create_estados_visita_table.php
│   │   ├── 2026_03_09_000003_create_users_table.php
│   │   ├── 2026_03_09_000006_create_clientes_table.php
│   │   ├── 2026_03_09_000007_create_edificios_table.php
│   │   └── 2026_03_09_000008_create_visitas_table.php
│   └── seeders
│       ├── ClienteSeeder.php
│       ├── DatabaseSeeder.php
│       ├── EdificioSeeder.php
│       ├── EstadoVisitaSeeder.php
│       ├── UserSeeder.php
│       ├── VisitaSeeder.php
│       └── ZonaSeeder.php
├── docker-compose.yml
├── init-db
│   └── db-leadchain.sql
├── package.json
└── routes
    ├── api.php
    ├── console.php
    └── web.php
```

## Test Realizado

Antes de hacerlo hay que comprobar que phpunit.xml tenga los datos referentes a tu base de datos. sino considerara que estas usando sqlite y daria fallo al estar usando posgre.
Estos campos hay que modificar:

```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="5432"/>
<env name="DB_DATABASE" value="leadchain"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value="root"/>
```
