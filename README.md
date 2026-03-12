# //TODO Alberto esto es un boceto para modificar mas adelante

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

**Fragmento de código**

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

### 5. Migraciones y Datos (Seeders)

Si prefieres construir la estructura de la base de datos desde Laravel y cargar los datos de prueba iniciales, ejecuta:

**Bash**

```
# Crear la estructura de tablas
php artisan migrate

# Cargar datos de prueba (Seeders)
php artisan db:seed
```

### 6. Ejecutar la API

Una vez que la base de datos está lista y las dependencias instaladas, puedes poner en marcha el servidor de desarrollo:

**Bash**

```
php artisan serve
```

La API estará disponible en: `http://127.0.0.1:8000`

---

## Conexión a la Base de Datos

Para gestionar los datos y ver los  **mapas de Córdoba** , conecta **DBeaver** con los siguientes datos:

* **Host:** `localhost`
* **Puerto:** `5432`
* **Base de datos:** `leadchain`
* **Usuario:** `root`
* **Contraseña:** `root`

---

## Endpoints Principales (API)

La API responde en formato JSON. Algunos de los recursos disponibles son:

CRUD = Create, Read, Update, Delete


## Postman:

|    Header    |                                          |
| :-----------: | :--------------------------------------: |
| Content-Type |             application/json             |
| Authorization |                                          |
| Bearer Token | Token recibido cuando se manda el login. |

POST: `http://127.0.0.1:8000/api/auth/login`

|  Recurso  |  Admin  | Comercial |
| :-------: | :-----: | :-------: |
| Clientes |  CRUD  |  - R - -  |
|   Zonas   |  CRUD  |  - R - -  |
| Usuarios |  CRUD  |  - - - -  |
| Edificios |  CRUD  |  - R - -  |
|  Visitas  | - R - D |  C-R-U -  |

`http://127.0.0.1:8000`

Ruta de login

| **Método** | **Endpoint**  |       **Descripción**       | Body                                                                 | Token |
| :---------------: | ------------------- | :---------------------------------: | -------------------------------------------------------------------- | ----- |
|       POST       | `/api/auth/login` | Iniciar sesión y obtener token JWT | {<br />"email": "root@leadchain.com",<br />"password": "root"<br />} | NO    |

Rutas protegidas con JWT (Se necesita el apartado de Authorization). Cada vez que se use  logout o refresh va a hacer falta modificar los tokens guardados en `Bearer Token`

| **Método** | **Endpoint**    |        **Descripción**        | Body     | Token |
| :---------------: | --------------------- | :-----------------------------------: | -------- | ----- |
|       POST       | `/api/auth/logout`  |   Cerrar sesión e invalidar token   | No tiene | SI    |
|       POST       | `/api/auth/refresh` |          Refrescar token JWT          | No tiene | SI    |
|        GET        | `/api/auth/me`      | Obtener datos del usuario autenticado | No tiene | SI    |

Rutas compartidas por ambos roles

| Método | Endpoint                         |        Descripción        | Body     | Token |
| :-----: | -------------------------------- | :------------------------: | -------- | ----- |
|   GET   | `/api/clientes`                | Listar todos los clientes | No tiene | SI    |
|   GET   | `/api/clientes/{id_cliente}`   | Obtener un cliente por ID | No tiene | SI    |
|   GET   | `/api/zonas`                   |   Listar todas las zonas   | No tiene | SI    |
|   GET   | `/api/zonas/{id_zona}`         |  Obtener una zona por ID  | No tiene | SI    |
|   GET   | `/api/edificios`               | Listar todos los edificios | No tiene | SI    |
|   GET   | `/api/edificios/{id_edificio}` | Obtener un edificio por ID | No tiene | SI    |
|   GET   | `/api/visitas`                 |  Listar todas las visitas  | No tiene | SI    |
|   GET   | `/api/visitas/{id_visita}`     | Obtener una visita por ID | No tiene | SI    |

Rutas del comercial (anunciante)

| Método | Endpoint                     |            Descripción            | Body                                                                                                                                                                                                                                                         | Token |
| :-----: | ---------------------------- | :--------------------------------: | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ----- |
|  POST  | `/api/visitas`             |       Crear una nueva visita       | {<br />      "id_usuario": 2,<br />      "id_cliente": 1,<br />      "fecha_hora": "2026-03-15 10:30:00",<br />      "hora_visita": "10:30:00",<br />      "id_estado": 1,<br />      "observaciones": "Primera visita al cliente"<br />} | Si    |
|   PUT   | `/api/visitas/{id_visita}` |   Actualizar una visita completa   |                                                                                                                                                                                                                                                              |       |
|  PATCH  | `/api/visitas/{id_visita}` | Actualizar parcialmente una visita |                                                                                                                                                                                                                                                              |       |

Rutas del administrador

|   Método   |             Endpoint             |            Descripción            |
| :---------: | :------------------------------: | :---------------------------------: |
|    POST    |          `/api/zonas`          |        Crear una nueva zona        |
|     PUT     |     `/api/zonas/{id_zona}`     |    Actualizar una zona completa    |
|    PATCH    |     `/api/zonas/{id_zona}`     |  Actualizar parcialmente una zona  |
|   DELETE   |     `/api/zonas/{id_zona}`     |          Eliminar una zona          |
| apiResource |          `/api/users`          |      CRUD completo de usuarios      |
|    POST    |        `/api/clientes`        |       Crear un nuevo cliente       |
|     PUT     |  `/api/clientes/{id_cliente}`  |   Actualizar un cliente completo   |
|    PATCH    |  `/api/clientes/{id_cliente}`  | Actualizar parcialmente un cliente |
|   DELETE   |  `/api/clientes/{id_cliente}`  |         Eliminar un cliente         |
|    POST    |        `/api/edificios`        |       Crear un nuevo edificio       |
|     PUT     | `/api/edificios/{id_edificio}` |   Actualizar un edificio completo   |
|    PATCH    | `/api/edificios/{id_edificio}` | Actualizar parcialmente un edificio |
|   DELETE   | `/api/edificios/{id_edificio}` |        Eliminar un edificio        |
|   DELETE   |   `/api/visitas/{id_visita}`   |         Eliminar una visita         |

---

## Estructura del Proyecto

```json

```

Return:

```json
{"success":true,"message":"Login exitoso",
"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NzMzMDc0OTcsImV4cCI6MTc3MzMxMTA5NywibmJmIjoxNzczMzA3NDk3LCJqdGkiOiJRRm9qUGg1TnhJR2Z4Q0lGIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.i2ovStcKS90DQk2iEZNQ7DT6Y-F3XDLqL1EvGRXMlNI",
"token_type":"bearer","expires_in":3600,
"user":
{
	"id":1,
	"nombre":"Admin",
	"apellidos":"Root",
	"email":"root@leadchain.com",
	"rol":"admin",
	"id_zona":1
},
"dashboard":"\/admin\/dashboard"}
```
