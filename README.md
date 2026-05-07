<h1 align="center">
  <a href="https://github.com/AlbertoRomeroPino/Leadchain-backend" style="text-decoration: none; color: inherit;">LeadChain API - Backend</a>
</h1>

<h3 align="center">API REST de alto rendimiento para la gestión de rutas comerciales, edificios, clientes y visitas técnicas geolocalizadas.</h3>

<p align="center">
  <a href="https://github.com/AlbertoRomeroPino/Leadchain-backend">
    <img alt="Backend Repo" src="https://img.shields.io/badge/Repositorio-Backend-000000?style=for-the-badge&logo=github&logoColor=white">
  </a>
</p>

<p align="center">
  <a href="https://github.com/AlbertoRomeroPino/Leadchain-frontend">
    <img alt="Frontend Repo" src="https://img.shields.io/badge/Repositorio-Frontend-E34F26?style=for-the-badge&logo=github&logoColor=white">
  </a>
  <a href="https://github.com/AlbertoRomeroPino/Leadchain.git">
    <img alt="Docker Repo" src="https://img.shields.io/badge/Repositorio-Docker-blue?style=for-the-badge&logo=github&logoColor=white">
  </a>
</p>

<h4 align="center">Frameworks, lenguajes y herramientas usadas</h4>

<div align="center">
  <img src="https://skillicons.dev/icons?i=laravel,php,postgres,docker,postman,github,markdown&theme=dark" height="50" />
</div>

<h4 align="center">Base de Datos Geoespacial y Documentación</h4>

<p align="center"> 
  <a href="https://postgis.net/">
    <img alt="PostGIS" src="https://img.shields.io/badge/PostGIS-152240?style=for-the-badge&logo=postgresql&logoColor=white">
  </a>
  <a href="https://swagger.io/">
    <img alt="Swagger" src="https://img.shields.io/badge/Swagger-85EA2D?style=for-the-badge&logo=swagger&logoColor=black">
  </a>
</p>

---

<h2 align="center">Acerca del Proyecto</h2>

**LeadChain** es una solución backend RESTful de alto rendimiento diseñada específicamente para la gestión inteligente de carteras de clientes, edificios y visitas comerciales en la ciudad de Córdoba.

Lo que hace única a esta API es su integración profunda con **PostgreSQL y la extensión PostGIS**. Esta arquitectura permite que LeadChain no solo almacene datos, sino que comprenda la ubicación espacial: puede validar si un comercial está dentro de su zona asignada, ubicar edificios en un mapa con precisión milimétrica y optimizar rutas de venta basadas en coordenadas geográficas reales.

`<H3 align="center">`Características Principales `</H3>`

* **Procesamiento Geoespacial Avanzado:** Almacenamiento nativo de coordenadas y polígonos, permitiendo la ejecución de consultas espaciales complejas desde el servidor de forma optimizada.
* **Arquitectura RESTful:** Diseño de *endpoints* semánticos y estandarizados para la orquestación del CRUD de entidades clave (Comerciales, Zonas, Edificios, Clientes y Visitas).
* **Seguridad y Control de Acceso (RBAC):** Sistema robusto de autenticación mediante JWT (*JSON Web Tokens*) acoplado a un sistema de *middlewares* para la protección y restricción de rutas según el rol de usuario (`Administrador` vs `Comercial`).
* **Documentación Viva:** Especificación completa de los contratos de la API mediante **Swagger / OpenAPI**, permitiendo la auditoría y prueba interactiva de los *endpoints* en entornos de desarrollo.

<h2 align="center" id="despliegue">Guía de Despliegue </h2>

Para poner en marcha el proyecto, dispones de dos alternativas. Se recomienda encarecidamente el uso del entorno contenerizado para evitar conflictos de versiones y configuraciones manuales complejas del motor espacial.

---

<h3 align="center"> Opción A: Entorno Dockerizado (Recomendado) </h3>

Este método es el más limpio y seguro. Docker se encarga de aprovisionar y enlazar los contenedores de PHP, Nginx y la base de datos PostgreSQL con PostGIS de forma totalmente aislada.

<h4 align="center">Requisitos Previos</h4>

* 🐳 [**Docker Desktop**](https://www.docker.com/products/docker-desktop/): Esencial para la orquestación.
* 🐙 [**Git**](https://git-scm.com/): Para la clonación del código fuente.

<h4 align="center">Pasos de Instalación</h4>

**1. Clonar el repositorio y preparar el entorno:**
Descarga el código y crea tu archivo de variables de entorno a partir del ejemplo.

```bash
git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git

cd leadchain-backend

cp .env.example .env
```

2. Instalar dependencias y generar claves:
   Ejecuta estos comandos en tu terminal local para instalar los paquetes y generar las firmas de seguridad criptográficas. Esto insertará las claves directamente en tu archivo .env local para que Docker las detecte.

```bash
composer install

php artisan key:generate
php artisan jwt:secret
```

3. Despliegue e Inicialización:
   Una vez configurado el entorno, dispones de dos alternativas para levantar la infraestructura y la base de datos espacial:

- Alternativa 1: Automatizada (Recomendada)
  Utiliza el comando de consola personalizado que orquesta todo el proceso de inicialización de una sola vez:

```bash
php artisan start:complete
```

- Alternativa 2: Manual paso a paso
  Si prefieres tener control absoluto sobre cada fase del despliegue:

```bash
# 1. Construye las imágenes y levanta los contenedores en segundo plano
docker compose up -d --build

# ⚠️ IMPORTANTE: Espera ~10 segundos a que PostGIS esté listo para aceptar conexiones

# 2. Ejecuta las migraciones y la carga de datos iniciales dentro del contenedor
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

4. Acceso a los Servicios:
   Una vez finalizado el proceso, la API estará operativa en los siguientes endpoints:

> **Documentación (Swagger):** `http://127.0.0.1:8000/api/documentation`
> **API Raíz:** `http://127.0.0.1:8000/api`

---

<h3 align="center"> Opción B: Entorno Híbrido (Laravel Local + DB Dockerizada) </h3>

Sigue estos pasos si prefieres ejecutar el servidor embebido de Laravel de forma nativa en tu máquina, delegando únicamente el motor de base de datos geoespacial a Docker.

<h4 align="center">Requisitos Previos</h4>

* 🐘 **PHP 8.2 o superior:** Asegúrate de habilitar en tu archivo `php.ini` las extensiones `extension=sodium` (necesaria para el cifrado de tokens) y `extension=pdo_pgsql` (para la comunicación con PostgreSQL).
* 📦 [**Composer**](https://getcomposer.org/): Gestor de dependencias de PHP.
* 🐳 [**Docker Desktop**](https://www.docker.com/products/docker-desktop/): Necesario para contenerizar PostgreSQL y PostGIS.
* 🐙 [**Git**](https://git-scm.com/): Para la clonación del código fuente.

<h4 align="center">Pasos de Instalación</h4>

**1. Clonar el repositorio:**

```bash
git clone https://github.com/AlbertoRomeroPino/Leadchain-backend.git

cd leadchain-backend
```

**2. Preparar el entorno y generar claves:**
Instala las dependencias y prepara tu archivo de configuración con las claves criptográficas necesarias para la seguridad del proyecto:

```bash
# Configurar archivo de variables de entorno
cp .env.example .env

# Instalar librerías del framework
composer install

# Generar claves criptográficas (App y JWT)
php artisan key:generate
php artisan jwt:secret
```

**3. Inicialización Híbrida Automatizada:**
Para simplificar el despliegue de este entorno mixto, puedes utilizar el siguiente comando personalizado. Este *script* levanta el contenedor de la base de datos en segundo plano, ejecuta las migraciones/seeders y arranca el servidor de desarrollo local automáticamente:

```bash
php artisan start:hybrid
```

**4. Acceso a los Servicios:**
El servidor local quedará en escucha activa. Puedes acceder a la plataforma a través de:

> **Documentación (Swagger):** `http://127.0.0.1:8000/api/documentation`
> **API Raíz:** `http://127.0.0.1:8000/api`

---

<h2 align="center" id="modelo-accesos">Modelo de Accesos y Permisos (RBAC) </h2>

La seguridad de la API y el control de sesiones están protegidos mediante **JWT (JSON Web Tokens)**. El sistema implementa una estricta arquitectura de control de acceso basado en roles (*Role-Based Access Control*) a través de *middlewares* de Laravel, garantizando que cada perfil interactúe únicamente con los recursos que le corresponden:

| Recurso                 | Administrador |  Comercial  | Notas de Privacidad y Lógica de Negocio                                                              |
| :---------------------- | :-----------: | :---------: | :---------------------------------------------------------------------------------------------------- |
| **Clientes**      |     CRUD     | R (Lectura) | Los comerciales consumen su cartera asignada, pero la eliminación de registros está restringida.    |
| **Zonas**         |     CRUD     | R (Lectura) | La configuración y delimitación geográfica está reservada exclusivamente a gerencia.              |
| **Usuarios**      |     CRUD     |      -      | Contiene datos sensibles de la plantilla. Acceso totalmente denegado para comerciales.                |
| **Edificios**     |     CRUD     | R (Lectura) | Catálogo unificado de puntos de interés; los comerciales lo usan para geolocalizar sus objetivos.   |
| **Visitas**       |     R / D     |  C / R / U  | El comercial crea y gestiona su actividad diaria; el administrador audita o elimina en caso de error. |
| **Estado Visita** |  R (Lectura)  | R (Lectura) | Catálogo inmutable del flujo de ventas (Pendiente, Realizada, Fallida, etc.).                        |

---

<h2 align="center" id="comandos-artisan"> Comandos Personalizados (Artisan) </h2>

Este proyecto extiende la consola nativa de Laravel con comandos desarrollados a medida. Su objetivo es automatizar el ciclo de vida del despliegue, gestionar los entornos (Docker/Híbrido) y facilitar la evaluación del proyecto.

---

### 🐳 1. `php artisan start:complete`

**Propósito:** Automatizar el despliegue del stack completo (Aplicación + Base de Datos espacial) utilizando Docker Compose con un solo comando.

**Flujo de ejecución:**

1. Ejecuta `docker compose up -d --build` para compilar imágenes y levantar la infraestructura en segundo plano.
2. Implementa un sistema de espera para garantizar que el contenedor principal esté inicializado (dependencias instaladas y CLI disponible).
3. Inyecta y ejecuta `php artisan migrate --force` dentro del contenedor para construir el esquema relacional.
4. Lanza `php artisan db:seed --force` para poblar las tablas con catálogos, geometrías y usuarios de prueba.

**Casos de uso:**
Ideal para el primer despliegue del proyecto en un entorno limpio (Opción A de instalación) o para realizar un reseteo total tras haber destruido los volúmenes con `docker compose down -v`.

**Ejemplo de uso:**

```bash
php artisan start:complete
```

---

### ⚡ 2. `php artisan start:hybrid`

**Propósito:** Orquestar un entorno de desarrollo mixto, delegando la capa de base de datos geoespacial a Docker mientras la API REST se ejecuta nativamente en la máquina local del desarrollador.

**Flujo de ejecución:**

1. Aprovisiona exclusivamente el servicio de persistencia (`docker compose up -d db`), omitiendo el contenedor de la aplicación.
2. Implementa un sondeo activo (*healthcheck*) hasta confirmar que PostgreSQL está listo para aceptar conexiones.
3. Asegura el enrutamiento local forzando temporalmente la variable `DB_HOST=127.0.0.1` para que el framework pueda conectar.
4. Aplica las migraciones del esquema (`php artisan migrate --force`) utilizando un sistema de reintentos automáticos para tolerar latencias durante el arranque del motor de la BD.
5. Ejecuta la carga de datos maestros, geometrías y usuarios (`php artisan db:seed --force`).
6. Invalida y purga las cachés de configuración del framework (`php artisan optimize:clear`).
7. Levanta el servidor embebido de PHP, escuchando peticiones en `http://127.0.0.1:8000`.

**Ventajas y Casos de Uso:**
Es la opción recomendada para el desarrollo diario (*DX*). Ofrece un rendimiento superior al no tener que virtualizar el entorno de PHP, facilita el uso de depuradores (*debuggers*) como Xdebug, y permite un acceso directo a los *logs* sin necesidad de inspeccionar contenedores.

**Ejemplo de uso:**

```bash
php artisan start:hybrid
```

---

### 🧪 3. `php artisan retest`

**Propósito:** Automatizar el ciclo completo de validación del código, garantizando un entorno de base de datos limpio y determinista antes de la ejecución de la suite de pruebas.

**Flujo de ejecución:**

1. Destruye y reconstruye todo el esquema relacional de la base de datos desde cero mediante `php artisan migrate:refresh`, eliminando posibles inconsistencias por datos residuales.
2. Puebla las tablas con los catálogos necesarios para los tests utilizando `php artisan db:seed`.
3. Lanza la suite completa de pruebas (tanto Unitarias como de Integración/Feature) a través de `php artisan test`, evaluando la lógica de negocio, los *endpoints* de la API y las consultas espaciales.

**Casos de Uso y Ventajas:**
Funciona como una herramienta de Integración Continua (CI) a nivel local. Es de uso obligatorio antes de realizar *commits* críticos o integrar nuevas ramas (*Merge/Pull Requests*) para asegurar la estabilidad del sistema y prevenir regresiones en el código.

**Ejemplo de uso:**

```bash
php artisan retest
```

---

### 📂 4. `php artisan app:tree`

**Propósito:** Muestra la estructura de directorios y archivos del proyecto en forma de árbol visual, filtrando elementos del framework para centrarse únicamente en tu código.

**Qué hace exactamente:**

* Ejecuta una función recursiva que escanea desde la raíz del proyecto (`base_path()`).
* Dibuja conectores visuales (`├──` y `└──`) para representar la jerarquía de las carpetas.
* **Aplica una lista negra de exclusión de carpetas:** Oculta automáticamente directorios que generan "ruido" como `bootstrap`, `public`, `storage`, `vendor`, `.git`, `test`, `Providers` y `factories`.
* **Aplica una lista negra de exclusión de archivos:** Ignora archivos de configuración internos (`app.php`, `database.php`, `session.php`), archivos de entorno (`composer.json`, `phpunit.xml`) y migraciones base por defecto de Laravel (como `jobs` o `personal_access_tokens`).

**Cuándo usarlo:** Cuando necesitas visualizar o copiar la arquitectura limpia de los archivos que tú has programado, sin que la terminal se inunde con las dependencias y la estructura estándar de Laravel.

**Ejemplo de uso:**

```bash
php artisan api:tree
```

---

<h2 align="center">Estructura del Sistema </h2>

```bash
Estructura del proyecto (filtrada):
├── app
│   ├── Console/Commands        # Comandos Artisan personalizados (start, retest, etc.)
│   ├── Http
│   │   ├── Controllers         # Lógica de los Endpoints (Agrupados por entidad)
│   │   ├── Middleware          # Control de acceso (JWT) y seguridad por Roles
│   │   ├── Requests            # Capa de validación de datos (Form Requests)
│   │   ├── Resources           # Transformación de Modelos a respuestas JSON uniformes
│   ├── Models                  # Entidades de negocio y lógica de persistencia
│   └── OpenApi                 # Definiciones y anotaciones para Swagger (L5-Swagger)
├── config                      # Ajustes de seguridad (JWT, Auth, CORS)
├── database
│   ├── migrations              # Definición de tablas y tipos espaciales (PostGIS)
│   └── seeders                 # Generación de datos maestros y geometrías de prueba
├── routes                      # Definición de rutas de la API y Consola
└── tests
    ├── Feature                 # Pruebas de integración de los Endpoints
    └── Fixture                 # Datos centralizados para pruebas repetibles
```

---

<h2 align="center">Autor</h2>

- **Alberto Romero Pino**
- **Email**: albertoromeropino2004@gmail.com
- **LinkedIn**: [linkedin.com/in/alberto-romero-pino-8aa0a32ba](linkedin.com/in/alberto-romero-pino-8aa0a32ba)

<hr>
<p align="center">
  <b>Trabajo de Fin de Grado</b> | <i>Grado en Desarrollo de Aplicaciones Web</i><br>
  I.E.S.Francisco de los Rios - Curso 2025/2026<br>
  <i>El código fuente expuesto forma parte de los entregables técnicos para la defensa del proyecto.</i>
</p>
<br>
