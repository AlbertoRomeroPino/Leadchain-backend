# LeadChain Backend API

Backend REST de LeadChain para la gestion comercial de clientes, edificios y visitas en Cordoba.

## Descripcion

Este proyecto esta construido con Laravel y usa PostgreSQL + PostGIS para trabajar con datos geograficos.
El backend expone endpoints para autenticacion, gestion de recursos de negocio y control de estados de visita.

## Stack Tecnologico

- PHP 8.2
- Laravel 12
- PostgreSQL 15
- PostGIS 3.3
- JWT (php-open-source-saver/jwt-auth)
- Docker / Docker Compose

## Arquitectura del Entorno

El entorno Docker levanta dos servicios:

- app: contenedor PHP que ejecuta Laravel.
- db: contenedor PostgreSQL con PostGIS.

Flujo al iniciar `docker compose up --build`:

1. Si no existe el codigo en el volumen Docker, se clona el repositorio backend.
2. Se instala Composer dentro del contenedor app.
3. Se espera disponibilidad de base de datos con `pg_isready`.
4. Se ejecutan migraciones.
5. Se levanta la API en `0.0.0.0:8000`.

## Requisitos

- Docker Desktop

No necesitas instalar PHP, Composer ni PostgreSQL en tu maquina para usar este flujo.

## Puesta en Marcha Rapida

```bash
docker compose up --build
```

---

Una vez descargado el --build ejecutamos para tener los seeders:

```bash
docker compose exec app php artisan db:seed --force
```

API disponible en:

- http://localhost:8000

Base de datos disponible en:

- host: localhost
- puerto: 5432
- base de datos: leadchain
- usuario: root
- password: root

## Comandos Operativos

```bash
# Ver logs del backend
docker compose logs -f app

# Ver logs de la base de datos
docker compose logs -f db

# Parar servicios
docker compose down

# Reset completo (borra volumen de codigo y de base de datos)
docker compose down -v
```

## Configuracion de Entorno

Variables relevantes usadas por el backend (inyectadas por Docker Compose):

- APP_ENV
- APP_DEBUG
- APP_KEY
- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- JWT_ALGO
- JWT_SECRET

## Modelo Funcional (Resumen)

Recursos principales del dominio:

- Clientes
- Zonas
- Usuarios
- Edificios
- Visitas
- EstadoVisita

Matriz de permisos definida en el proyecto:

| Recurso      | Admin | Comercial |
| :----------- | :---: | :-------: |
| Clientes     | CRUD |     R     |
| Zonas        | CRUD |     R     |
| Usuarios     | CRUD |     -     |
| Edificios    | CRUD |     R     |
| Visitas      | R, D |  C, R, U  |
| EstadoVisita |   R   |     R     |

## Estructura Base del Proyecto

Directorios clave:

- app/Http/Controllers
- app/Http/Requests
- app/Http/Resources
- app/Models
- database/migrations
- database/seeders
- routes/api.php
- config/jwt.php
- docker-compose.yml
- dockerfile

## Testing

Si ejecutas tests fuera de Docker, revisa `phpunit.xml` para usar `pgsql` en lugar de `sqlite`.

Comando del proyecto para reset de BD + seeders + tests:

```bash
php artisan retest
```

## Notas Importantes

- El codigo que ejecuta el contenedor app proviene del repositorio remoto clonado en un volumen Docker.
- Cambios locales no subidos al repositorio no se reflejan automaticamente en ese volumen.
- Si quieres regenerar todo desde cero, usa `docker compose down -v` y vuelve a levantar.

## Estado del Proyecto

Este backend esta preparado para desarrollo local con arranque automatizado de API y base de datos mediante Docker Compose.
