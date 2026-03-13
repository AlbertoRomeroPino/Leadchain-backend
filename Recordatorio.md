# Recordatorio de cambios sobre Laravel base (LeadChain)

Este documento resume lo que se ha creado o modificado respecto a un Laravel base para que puedas defender el backend en la presentación.

## 1. Arquitectura y objetivo funcional

- API REST para gestión de clientes, zonas, edificios, usuarios y visitas.
- Backend orientado a roles: admin y comercial.
- Dominio geoespacial con PostGIS para zonas y ubicaciones.

## 2. Autenticación y seguridad

- JWT implementado con paquete externo: `php-open-source-saver/jwt-auth`.
- Guard API con driver JWT en `config/auth.php`.
- Endpoints de autenticación propios:
  - `POST /api/auth/login`
  - `POST /api/auth/logout`
  - `POST /api/auth/refresh`
  - `GET /api/auth/me`
- Respuesta de login enriquecida con:
  - token JWT
  - datos de usuario
  - ruta de dashboard por rol

Archivos clave:

- `composer.json`
- `config/auth.php`
- `app/Http/Controllers/Api/AuthController.php`

## 3. Middleware y autorización por rol

- Middleware personalizado para control de permisos por rol: `CheckRole`.
- Middleware adicional para admin: `LoginAdmin`.
- Alias de middleware registrados en bootstrap (`role`, `admin`).
- Uso en rutas API para dividir acceso compartido, comercial y admin.

Archivos clave:

- `app/Http/Middleware/CheckRole.php`
- `app/Http/Middleware/LoginAdmin.php`
- `bootstrap/app.php`
- `routes/api.php`

## 4. Modelo de datos y migraciones personalizadas

- Entidades de negocio añadidas:
  - `zonas`
  - `estados_visita`
  - `clientes`
  - `edificios`
  - `visitas`
  - `users` adaptado al dominio
- Uso de PostGIS en migraciones:
  - extensión `postgis`
  - columnas `geometry(Point, 4326)` para esquinas de zona
- Relaciones de dominio entre usuarios, clientes, edificios, zonas y visitas.

Archivos clave:

- `database/migrations/2026_03_09_000001_create_zonas_table.php`
- `database/migrations/2026_03_09_000006_create_clientes_table.php`
- `database/migrations/2026_03_09_000007_create_edificios_table.php`
- `database/migrations/2026_03_09_000008_create_visitas_table.php`
- `app/Models/Zona.php`
- `app/Models/Edificio.php`
- `app/Models/Cliente.php`
- `app/Models/Visita.php`
- `app/Models/User.php`

## 5. Controladores CRUD de negocio

- CRUD completo para:
  - Usuarios
  - Clientes
  - Edificios
  - Zonas
  - Visitas
- Reglas de permisos por endpoint (lectura compartida, edición restringida por rol).

Archivos clave:

- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/ClienteController.php`
- `app/Http/Controllers/EdificioController.php`
- `app/Http/Controllers/ZonaController.php`
- `app/Http/Controllers/VisitaController.php`

## 6. Validación de entrada con FormRequest

- Validaciones por recurso con reglas específicas de negocio.
- Diferenciación `store/update` cuando aplica (por ejemplo edificio y usuario).

Archivos clave:

- `app/Http/Requests/AuthRequest.php`
- `app/Http/Requests/UserRequest.php`
- `app/Http/Requests/UserUpdateRequest.php`
- `app/Http/Requests/ClienteRequest.php`
- `app/Http/Requests/EdificioRequest.php`
- `app/Http/Requests/ZonaRequest.php`
- `app/Http/Requests/VisitaRequest.php`

## 7. Recursos API (transformación JSON)

- Estandarización de payload de salida con `JsonResource`.
- Recursos por entidad para controlar lo que se expone en API.
- En zonas se exponen también las coordenadas de esquinas (`lat/lng`) además de metadatos.

Archivos clave:

- `app/Http/Resources/UserResource.php`
- `app/Http/Resources/ClienteResource.php`
- `app/Http/Resources/EdificioResource.php`
- `app/Http/Resources/ZonaResource.php`
- `app/Http/Resources/VisitaResource.php`

## 8. Seeders y datos base para demo

- Seeders completos con datos realistas de Córdoba.
- Usuarios iniciales con roles (admin/comercial).
- Datos de clientes, edificios y visitas ya relacionados.

Archivos clave:

- `database/seeders/DatabaseSeeder.php`
- `database/seeders/UserSeeder.php`
- `database/seeders/ZonaSeeder.php`
- `database/seeders/ClienteSeeder.php`
- `database/seeders/EdificioSeeder.php`
- `database/seeders/VisitaSeeder.php`

## 9. Testing funcional completo de endpoints

- Suite de tests feature por módulo:
  - Auth (login/logout/refresh/me)
  - Users
  - Clientes
  - Edificios
  - Visitas
  - Zonas
- Cobertura CRUD por endpoint según permisos.
- Tests ajustados a respuestas reales HTTP (`201`, `200`, `204`).
- Tests de zonas validando estructura geográfica (`lat/lng`).

Archivos clave:

- `tests/Feature/LoginTest.php`
- `tests/Feature/UserTest.php`
- `tests/Feature/ClienteTest.php`
- `tests/Feature/EdificioTest.php`
- `tests/Feature/VisitasTest.php`
- `tests/Feature/ZonaTest.php`

## 10. Comandos Artisan personalizados

- `php artisan retest`
  - Ejecuta `migrate:fresh --seed`
  - Lanza tests
  - Da feedback final del ciclo completo
- `php artisan app:tree`
  - Imprime estructura del proyecto filtrada para revisión rápida

Archivos clave:

- `app/Console/Commands/ResetAndTest.php`
- `app/Console/Commands/EstructuraProyecto.php`

## 11. Infraestructura local con Docker

- Contenedor de base de datos `postgis/postgis:15-3.3`.
- Configuración de conexión para entorno local.
- Preparado para inicialización por SQL si se activa volumen/script.

Archivo clave:

- `docker-compose.yml`

## 12. Cambios recientes relevantes para defensa técnica

- Se corrigieron tests de usuarios para evitar colisiones por email en update.
- Se alineó delete de usuarios al comportamiento real `204 No Content`.
- Se evitó que un test mutara al admin y rompiera logins posteriores.
- Se corrigió comando `retest` para finalizar sin error de método inexistente.
- Se añadió serialización de esquinas geográficas en zona resource.
- Se corrigió typo en accessor de `esquina_noroeste` en modelo de zona.

## 13. Frase corta para presentación

"Partimos de Laravel base y construimos una API JWT con control de roles, modelo geoespacial con PostGIS, CRUD completo de negocio, seeders demo y testing automatizado end-to-end con comando de ciclo completo (`php artisan retest`)."
