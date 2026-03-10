# //TODO Alberto esto es un boceto para modificar mas adelante

# LeadChain API - Backend Córdoba

LeadChain es una **API-Rest** diseñada para la gestión de clientes, edificios y visitas comerciales en la ciudad de Córdoba. Utiliza **PostgreSQL con PostGIS** para manejar ubicaciones geográficas exactas, permitiendo visualizar mapas y zonas de venta en tiempo real.

---

## 🛠️ Tecnologías utilizadas

* **Lenguaje:** PHP 8.x (Laravel Framework)
* **Base de Datos:** PostgreSQL 15 + PostGIS (Extensión espacial)
* **Contenedores:** Docker & Docker Compose
* **Gestión de BD:** DBeaver (Recomendado)

---

## 🚀 Instalación y Despliegue

Sigue estos pasos para tener el entorno funcionando en menos de 5 minutos:

### 1. Requisitos Previos

Asegúrate de tener instalados:

* [Docker Desktop](https://www.docker.com/products/docker-desktop/)
* [Git](https://git-scm.com/)

### 2. Clonar el proyecto

**Bash**

```
git clone https://github.com/tu-usuario/leadchain-backend.git
cd leadchain-backend
```

### 3. Configurar variables de entorno

Copia el archivo de ejemplo y configura tus credenciales (por defecto ya vienen preparadas para el Docker):

**Bash**

```
cp .env.example .env
```

### 4. Levantar la infraestructura (Docker)

Este comando descargará la imagen de **PostGIS** y ejecutará automáticamente el script de creación de tablas de Córdoba (`init-db/db-leadchain.sql`).

**Bash**

```
docker-compose up -d
```

> **💡 Nota importante:** Si realizas cambios manuales en el archivo `.sql` de la carpeta `init-db`, debes reiniciar el contenedor borrando los volúmenes para que los cambios surtan efecto:
>
> `docker-compose down -v && docker-compose up -d`

---

## 🗄️ Conexión a la Base de Datos

Para gestionar los datos y ver los  **mapas de Córdoba** , conecta **DBeaver** con los siguientes datos:

* **Host:** `localhost`
* **Puerto:** `5432`
* **Base de datos:** `leadchain`
* **Usuario:** `root`
* **Contraseña:** `root`

### Visualización Espacial

Para ver los edificios en el mapa:

1. Abre la tabla `edificios` en DBeaver.
2. Ve a la pestaña  **Datos** .
3. Selecciona una celda de la columna `ubicacion`.
4. Abre el panel lateral derecho ( **F7** ) y selecciona la pestaña  **Spatial** .

---

## 🛣️ Endpoints Principales (API)

La API responde en formato JSON. Algunos de los recursos disponibles son:

| **Método** | **Endpoint** | **Descripción**                                |
| ----------------- | ------------------ | ----------------------------------------------------- |
| `GET`           | `/api/usuarios`  | Listado de comerciales y responsables.                |
| `GET`           | `/api/edificios` | Lista de edificios con coordenadas geográficas.      |
| `POST`          | `/api/visitas`   | Registrar una nueva visita comercial.                 |
| `GET`           | `/api/zonas`     | Ver los sectores de Córdoba (Centro, Judería, etc). |

---

## 📂 Estructura del Proyecto

* `/app`: Lógica interna de la API (Modelos, Controladores).
* `/init-db`: Contiene el script `db-leadchain.sql` con la estructura geográfica inicial.
* `/routes`: Definición de los endpoints de la API.
* `docker-compose.yml`: Configuración del contenedor de PostgreSQL + PostGIS.
  "# Leadchain-backend"


| Recurso   | Admin      | Comercial |
| --------- | ---------- | --------- |
| Clientes  | CRUD       | R         |
| Zonas     | CRUD       | R         |
| Usuarios  | CRUD       | -         |
| Edificios | CRUD       | R         |
| Visitas   | R + Delete | CRU       |
