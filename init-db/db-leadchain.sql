-- =============================================
-- Base de datos LeadChain para PostgreSQL
-- Coincide exactamente con las migraciones y seeders de Laravel
-- Requiere extensión PostGIS para tipos geometry (POINT y Polygon)
-- =============================================

-- Habilitar extensión PostGIS
CREATE EXTENSION IF NOT EXISTS postgis;

-- Eliminar tablas si existen (en orden inverso por dependencias)
DROP TABLE IF EXISTS visitas CASCADE;
DROP TABLE IF EXISTS cliente_edificio CASCADE;
DROP TABLE IF EXISTS edificios CASCADE;
DROP TABLE IF EXISTS clientes CASCADE;
DROP TABLE IF EXISTS personal_access_tokens CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS estados_visita CASCADE;
DROP TABLE IF EXISTS zonas CASCADE;

-- =============================================
-- TABLA: ZONAS (migración 000001)
-- Define cuadrículas/zonas donde los usuarios no-admin se mueven
-- Cada zona guarda su área con geometry(Polygon, 4326)
-- =============================================
CREATE TABLE zonas (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    area GEOMETRY(Polygon, 4326),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: ESTADOS_VISITA (migración 000002)
-- =============================================
CREATE TABLE estados_visita (
    id BIGSERIAL PRIMARY KEY,
    etiqueta VARCHAR(50) NOT NULL,
    color_hex VARCHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: USERS (migración 000003)
-- =============================================
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL DEFAULT 'comercial',
    id_responsable BIGINT,
    id_zona BIGINT,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_responsable FOREIGN KEY (id_responsable) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_user_zona FOREIGN KEY (id_zona) REFERENCES zonas(id) ON DELETE SET NULL
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_zona ON users(id_zona);

-- =============================================
-- TABLA: PASSWORD_RESET_TOKENS (migración 000003)
-- =============================================
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP
);

-- =============================================
-- TABLA: SESSIONS (migración 000003)
-- =============================================
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- =============================================
-- TABLA: CACHE (migración 000004)
-- =============================================
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE INDEX idx_cache_expiration ON cache(expiration);

-- =============================================
-- TABLA: CACHE_LOCKS (migración 000004)
-- =============================================
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE INDEX idx_cache_locks_expiration ON cache_locks(expiration);

-- =============================================
-- TABLA: JOBS (migración 000005)
-- =============================================
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

-- =============================================
-- TABLA: JOB_BATCHES (migración 000005)
-- =============================================
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

-- =============================================
-- TABLA: FAILED_JOBS (migración 000005)
-- =============================================
CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: CLIENTES (migración 000006)
-- =============================================
CREATE TABLE clientes (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: EDIFICIOS (migración 000007)
-- =============================================
CREATE TABLE edificios (
    id BIGSERIAL PRIMARY KEY,
    direccion_completa VARCHAR(255) NOT NULL,
    ubicacion GEOMETRY(Point, 4326) NOT NULL,
    id_zona BIGINT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_edificio_zona FOREIGN KEY (id_zona) REFERENCES zonas(id) ON DELETE RESTRICT
);

CREATE INDEX idx_edificios_zona ON edificios(id_zona);
CREATE INDEX idx_edificios_ubicacion ON edificios USING GIST(ubicacion);

-- =============================================
-- TABLA: VISITAS (migración 000008)
-- =============================================
CREATE TABLE visitas (
    id BIGSERIAL PRIMARY KEY,
    id_usuario BIGINT NOT NULL,
    id_cliente BIGINT NOT NULL,
    fecha_hora TIMESTAMP NOT NULL,
    id_estado BIGINT NOT NULL,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visita_usuario FOREIGN KEY (id_usuario) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_visita_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_visita_estado FOREIGN KEY (id_estado) REFERENCES estados_visita(id) ON DELETE RESTRICT
);

CREATE INDEX idx_visitas_usuario ON visitas(id_usuario);
CREATE INDEX idx_visitas_cliente ON visitas(id_cliente);
CREATE INDEX idx_visitas_fecha ON visitas(fecha_hora);

-- =============================================
-- TABLA: PERSONAL_ACCESS_TOKENS (migración 000009)
-- =============================================
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_personal_access_tokens_tokenable ON personal_access_tokens(tokenable_type, tokenable_id);
CREATE INDEX idx_personal_access_tokens_expires_at ON personal_access_tokens(expires_at);

-- =============================================
-- TABLA: CLIENTE_EDIFICIO (migración 000011)
-- Tabla de relación muchos-a-muchos entre clientes y edificios
-- =============================================
CREATE TABLE cliente_edificio (
    id BIGSERIAL PRIMARY KEY,
    cliente_id BIGINT NOT NULL,
    edificio_id BIGINT NOT NULL,
    planta VARCHAR(20),
    puerta VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cliente_edificio_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_cliente_edificio_edificio FOREIGN KEY (edificio_id) REFERENCES edificios(id) ON DELETE CASCADE,
    CONSTRAINT uk_cliente_edificio UNIQUE (cliente_id, edificio_id)
);

-- =============================================
-- TABLA: PERSONAL_ACCESS_TOKENS (migración 000009)
-- =============================================
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_personal_access_tokens_tokenable ON personal_access_tokens(tokenable_type, tokenable_id);
CREATE INDEX idx_personal_access_tokens_expires_at ON personal_access_tokens(expires_at);

-- =============================================
-- DATOS INICIALES (Seeders)
-- =============================================

-- Zonas de Córdoba (ZonaSeeder)
-- Las coordenadas reales con polígonos precisos
INSERT INTO zonas (id, nombre, area, created_at, updated_at) VALUES
    (1, 'Centro',
        ST_GeomFromText('POLYGON((-4.786251783371 37.875157874361, -4.7857475280762 37.880188176038, -4.7851145267487 37.880823290712, -4.7846531867981 37.886276583298, -4.7838699817657 37.886886243086, -4.7792994976044 37.889248627086, -4.7774863243103 37.889028479808, -4.7773146629334 37.889257094276, -4.7779154777527 37.891695604429, -4.7774970531464 37.891873409302, -4.7739458084106 37.891805674163, -4.7696971893311 37.893261965917, -4.769332408905 37.893160365101, -4.7686672210693 37.892635425321, -4.7656202316284 37.891509332197, -4.7647404670715 37.890323952406, -4.7638499736786 37.886894710547, -4.7638821601868 37.886530608823, -4.7657060623169 37.885336681236, -4.7664141654968 37.88469313777, -4.7671115398407 37.880899504105, -4.7681844234467 37.880476095369, -4.7675514221191 37.877817032863, -4.7653841972351 37.874971560294, -4.7654271125793 37.874573524119, -4.7667789459229 37.874226299315, -4.775083065033 37.875090123846, -4.7788166999817 37.870389779749, -4.779235124588 37.870169576096, -4.7855973243713 37.873735100457, -4.7862410545349 37.874776776903, -4.786251783371 37.875157874361))', 4326),
        '2026-04-22 05:54:11', '2026-04-22 05:54:11'),
    (2, 'Sagunto',
        ST_GeomFromText('POLYGON((-4.7695469856262 37.89341397, -4.7687101364136 37.892685829227, -4.7655558586121 37.891466600645, -4.7647404670715 37.890332419472, -4.7636246681213 37.886886243086, -4.7602987289429 37.889900597855, -4.7555243968964 37.891958078138, -4.7498273849487 37.894396498831, -4.7490656375885 37.894794427875, -4.7495377063751 37.895463282487, -4.7658562660217 37.897495208586, -4.7695469856262 37.89341397))', 4326),
        '2026-04-22 05:57:31', '2026-04-22 05:57:31'),
    (3, 'Campus de Rabanales',
        ST_GeomFromText('POLYGON((-4.727897644043 37.906078286803, -4.7269105911255 37.91992613995, -4.7130060195923 37.918605796947, -4.712405204773 37.914238339691, -4.727897644043 37.906078286803))', 4326),
        '2026-04-22 05:58:59', '2026-04-22 05:58:59'),
    (4, 'Poligono Industrial La Torrecilla',
        ST_GeomFromText('POLYGON((-4.7906398773193 37.859335246585, -4.7827434539795 37.853608878827, -4.7832155227661 37.851202993994, -4.7901248931885 37.846695984367, -4.7917985916138 37.843239294092, -4.8021411895752 37.843578192459, -4.8047590255737 37.847373747774, -4.7994804382324 37.854557656892, -4.7914552688599 37.860182304638, -4.7906398773193 37.859335246585))', 4326),
        '2026-04-22 06:00:45', '2026-04-22 06:00:45'),
    (5, 'Guadalquivir',
        ST_GeomFromText('POLYGON((-4.7857046127319 37.869939711363, -4.7785377502441 37.877426289722, -4.7769069671631 37.876579429883, -4.7818422317505 37.869973588997, -4.7857046127319 37.869939711363))', 4326),
        '2026-04-22 07:35:30', '2026-04-22 07:35:30');

-- Estados de visita (EstadoVisitaSeeder)
INSERT INTO estados_visita (id, etiqueta, color_hex, created_at, updated_at) VALUES
    (1, 'Vendido', '#2ECC71', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (2, 'En Camino', '#F1C40F', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (3, 'Pendiente', '#BDC3C7', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (4, 'Volver luego', '#E67E22', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (5, 'Ausente', '#9B59B6', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (6, 'Local Cerrado', '#A67C52', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (7, 'No Interesado', '#7F8C8D', '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (8, 'Cancelada', '#212121', '2026-04-22 05:52:14', '2026-04-22 05:52:14');

-- Usuarios (UserSeeder)
-- Password hash para: root (bcrypt con salt)
INSERT INTO users (id, nombre, apellidos, email, password, rol, id_responsable, id_zona, email_verified_at, remember_token, created_at, updated_at) VALUES
    (1, 'Admin', 'Testing 1', 'root@leadchain.com', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'admin', NULL, NULL, NULL, NULL, '2026-04-22 05:52:14', '2026-04-22 05:52:14'),
    (2, 'Admin', 'Testing 2', 'root2@leadchain.com', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'admin', NULL, NULL, NULL, NULL, '2026-04-22 05:52:15', '2026-04-22 05:52:15'),
    (3, 'El Pato', 'Mareado De La Albolafia Que No Encuentra El Edificio', 'pato.rio@guadalquivir.es', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'comercial', 1, 5, NULL, NULL, '2026-04-22 07:40:28', '2026-04-22 07:40:28'),
    (4, 'Rafael', 'Cruz Montilla', 'rafa.centro@comercial.es', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'comercial', 1, 1, NULL, NULL, '2026-04-22 07:41:22', '2026-04-22 07:41:22'),
    (5, 'Carmen', 'Flores Jurado', 'carmen.torrecilla@comercial.es', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'comercial', 1, 4, NULL, NULL, '2026-04-22 07:41:54', '2026-04-22 07:41:54'),
    (6, 'Manuel', 'Ortiz Serrano', 'manuel.sagunto@comercial.es', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'comercial', 1, 2, NULL, NULL, '2026-04-22 07:42:30', '2026-04-22 07:42:30'),
    (7, 'Lucía', 'García Roldán', 'lucia.rabanales@comercial.es', '$2y$12$EZ10FKcpaHq9/nzWp39dZ.1yYooA230m3MBZuSRnILjUfoyJ49IdC', 'comercial', 1, 3, NULL, NULL, '2026-04-22 07:42:59', '2026-04-22 07:42:59');

-- Clientes (ClienteSeeder) - 29 clientes
INSERT INTO clientes (nombre, apellidos, telefono, email, created_at, updated_at) VALUES
    ('Juan', 'Pérez Garcia', '600111222', 'Juan.perez@email.com', '2026-04-22 06:22:23', '2026-04-22 06:22:23'),
    ('María', '', NULL, 'maria85@email.com', '2026-04-22 06:22:23', '2026-04-22 06:22:23'),
    ('Carlos', 'Rodríguez', '611222333', NULL, '2026-04-22 06:22:23', '2026-04-22 06:22:23'),
    ('Ana', 'López', '622333444', 'ana.lopez@prov.es', '2026-04-22 06:24:28', '2026-04-22 06:24:28'),
    ('Luis', '', NULL, NULL, '2026-04-22 06:24:28', '2026-04-22 06:24:28'),
    ('Elena', 'Sánchez Ruíz', NULL, 'elena.sanchez@mail.com', '2026-04-22 06:24:28', '2026-04-22 06:24:28'),
    ('Pedro', '', '633444555', NULL, '2026-04-22 06:25:49', '2026-04-22 06:25:49'),
    ('Sofía', 'Martínez', '644555666', 'sofia.mtz@web.com', '2026-04-22 06:25:49', '2026-04-22 06:25:49'),
    ('Diego', '', NULL, NULL, '2026-04-22 06:25:49', '2026-04-22 06:25:49'),
    ('Lucia', 'Gómez', NULL, 'lucia.g@servidor.com', '2026-04-22 07:09:55', '2026-04-22 07:09:55'),
    ('Manuel', 'Ferrero', NULL, NULL, '2026-04-22 07:09:55', '2026-04-22 07:09:55'),
    ('Carmen', 'Ruíz Lara', '655666777', NULL, '2026-04-22 07:09:55', '2026-04-22 07:09:55'),
    ('Siluro', 'El del Pantano', NULL, 'gluglu@rio.es', '2026-04-22 07:13:03', '2026-04-22 07:13:03'),
    ('Piragüista', 'Desorientado', NULL, NULL, '2026-04-22 07:19:46', '2026-04-22 07:19:46'),
    ('El Caimán', 'De la Fuensanta', NULL, 'no_soy_un_bolso@rio.es', '2026-04-22 07:19:46', '2026-04-22 07:19:46'),
    ('Jose', '', NULL, 'jose.test@mail.com', '2026-04-22 07:21:50', '2026-04-22 07:21:50'),
    ('Isabel', 'Castro', '666777888', 'isabel.castro@email.es', '2026-04-22 07:21:50', '2026-04-22 07:21:50'),
    ('Antonio', '', NULL, NULL, '2026-04-22 07:21:50', '2026-04-22 07:21:50'),
    ('Rosa', 'Navarro', NULL, 'rosa.nav@mail.com', '2026-04-22 07:24:04', '2026-04-22 07:24:04'),
    ('Javier', '', '677888999', NULL, '2026-04-22 07:24:04', '2026-04-22 07:24:04'),
    ('Teresa', 'Díaz Sobrino', '688999000', 'teresa.diaz@web.es', '2026-04-22 07:24:04', '2026-04-22 07:24:04'),
    ('Francisco', '', NULL, 'fran.90@email.com', '2026-04-22 07:24:04', '2026-04-22 07:24:04'),
    ('Pilar', 'Jiménez', NULL, NULL, '2026-04-22 07:24:36', '2026-04-22 07:24:36'),
    ('Miguel', '', '699000111', NULL, '2026-04-22 07:25:07', '2026-04-22 07:25:07'),
    ('Angela', 'Moreno', '612345678', 'angela.m@prov.com', '2026-04-22 07:26:46', '2026-04-22 07:26:46'),
    ('Rafael', '', NULL, 'rafa.g@mail.com', '2026-04-22 07:26:46', '2026-04-22 07:26:46'),
    ('Concepción', '', NULL, NULL, '2026-04-22 07:26:46', '2026-04-22 07:26:46'),
    ('Alberto', 'Vega', '623456789', 'alberto.v@email.com', '2026-04-22 07:26:46', '2026-04-22 07:26:46'),
    ('San Rafael Custodio Que Vigila El Puente Y El Rio!', 'Protector Eterno De Todos Los Cordobeses Que Se Queja Por Un Bloque De Pisos En Mitad De La Abolafia', NULL, 'el_arcangel@mezquita.es', '2026-04-22 07:33:39', '2026-04-22 07:33:39');

-- Edificios (EdificioSeeder) - 9 edificios
INSERT INTO edificios (id, direccion_completa, ubicacion, id_zona, tipo, created_at, updated_at) VALUES
    (1, 'Calle Moricos, 34', ST_GeomFromText('POINT (-4.7732001543045 37.890618777723)', 4326), 1, 'Complejo residencial', '2026-04-22 06:07:11', '2026-04-22 06:07:11'),
    (2, 'Calle Claudio Marcelo, 1', ST_GeomFromText('POINT (-4.7790205478668 37.884530729479)', 4326), 1, 'Residencial', '2026-04-22 06:08:06', '2026-04-22 06:08:06'),
    (3, 'Calle Escañuela, 14', ST_GeomFromText('POINT (-4.7680932283401 37.887452039369)', 4326), 1, 'Edificio', '2026-04-22 06:09:22', '2026-04-22 06:09:22'),
    (4, 'Calle 28 de Febrero, 10', ST_GeomFromText('POINT (-4.7607171535492 37.894030912855)', 4326), 2, 'Edificio', '2026-04-22 06:10:53', '2026-04-22 06:10:53'),
    (5, 'Calle platero  Pedro de Bares, 7', ST_GeomFromText('POINT (-4.7611570358276 37.895453294691)', 4326), 2, 'Edificio', '2026-04-22 06:12:05', '2026-04-22 06:12:05'),
    (6, 'Residencias de universidad Rabanales', ST_GeomFromText('POINT (-4.7237992286682 37.914229544738)', 4326), 3, 'Residencial', '2026-04-22 06:13:39', '2026-04-22 06:13:39'),
    (7, 'Agrocor la torrecilla', ST_GeomFromText('POINT (-4.7933650016785 37.854081019215)', 4326), 4, 'Tienda', '2026-04-22 06:14:42', '2026-04-22 06:14:42'),
    (8, 'Obramat', ST_GeomFromText('POINT (-4.7923028469086 37.847583297192)', 4326), 4, 'Supermercado', '2026-04-22 06:16:17', '2026-04-22 06:16:17'),
    (9, 'Rio Guadalquivir', ST_GeomFromText('POINT (-4.7820138931274 37.871898501141)', 4326), 5, 'Rio', '2026-04-22 06:09:54', '2026-04-22 07:35:48');

-- Cliente-Edificio relaciones (ClienteEdificioSeeder) - 29 relaciones
INSERT INTO cliente_edificio (cliente_id, edificio_id, planta, puerta, created_at, updated_at) VALUES
    (1, 1, '1', 'A', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (2, 1, '2', 'A', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (3, 1, 'Bajo', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (4, 2, '3', 'C', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (5, 2, '4', 'Izquierda', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (6, 2, '5', 'Derecha', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (7, 3, 'Entreplanta', '2', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (8, 3, '1', 'B', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (9, 3, '2', 'A', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (10, 4, '3', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (11, 4, '4', '2', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (12, 4, '5', 'C', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (13, 9, 'Lecho del río', 'Branq. Izq', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (14, 9, 'Cubierta', 'Kayak 4', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (15, 9, 'Sótano -2', 'Fauces', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (16, 5, 'Bajo', 'B', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (17, 5, '1', 'Izquierda', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (18, 5, '2', 'Derecha', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (19, 6, '3', 'A', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (20, 6, '4', 'B', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (21, 6, '5', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (22, 6, '1', '3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (23, 6, '2', 'C', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (24, 6, '3', 'Izquierda', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (25, 6, '4', 'Derecha', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (26, 6, '5', 'A', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (27, 7, 'Bajo', 'C', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (28, 8, '1', 'D', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (29, 9, 'Encima del Triunfo', 'Derecha', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Visitas (VisitaSeeder) - 19 visitas
INSERT INTO visitas (id_usuario, id_cliente, fecha_hora, id_estado, observaciones, created_at, updated_at) VALUES
    (3, 13, '2026-04-22 09:44:00', 1, 'Un poco profundo y dificil de llegar pero el cliente muy amable', '2026-04-22 07:45:06', '2026-04-22 07:45:06'),
    (3, 14, '2026-04-22 09:45:00', 7, 'Me pidio ayuda de como llegar a la orilla. Quizas en otro momento', '2026-04-22 07:46:22', '2026-04-22 07:46:22'),
    (4, 1, '2026-04-22 02:47:00', 1, 'Cliente encantado con la oferta, firma y paga la señal ahora mismo.', '2026-04-22 07:54:26', '2026-04-22 07:54:26'),
    (4, 2, '2026-04-22 07:54:00', 2, 'El camión está cruzando el Puente Romano, llega en unos 5 minutos.', '2026-04-22 07:54:50', '2026-04-22 07:54:50'),
    (4, 3, '2026-04-22 07:55:00', 3, 'Dice que tiene que consultarlo con su mujer y me llama el lunes.', '2026-04-22 07:55:05', '2026-04-22 07:55:05'),
    (4, 4, '2026-04-22 07:55:00', 4, 'Hay muchísima gente en la tienda ahora, me han pedido volver luego.', '2026-04-22 07:55:23', '2026-04-22 07:55:23'),
    (4, 5, '2026-04-22 07:55:00', 5, 'He llamado tres veces al timbre y no sale nadie, dejo nota debajo.', '2026-04-22 07:55:33', '2026-04-22 07:55:33'),
    (4, 6, '2026-04-22 07:55:00', 6, 'El local está cerrado por reforma hasta la semana que viene seguro.', '2026-04-22 07:55:46', '2026-04-22 07:55:46'),
    (5, 24, '2026-04-22 09:56:00', 7, 'No le interesa el producto porque dice que ya tiene uno parecido.', '2026-04-22 07:56:33', '2026-04-22 07:57:10'),
    (5, 23, '2026-04-22 07:56:00', 8, 'Visita cancelada porque el cliente se ha puesto enfermo de repente.', '2026-04-22 07:56:48', '2026-04-22 07:56:48'),
    (7, 19, '2026-04-22 07:58:00', 1, 'Venta completada, quiere que le enviemos la factura por el correo.', '2026-04-22 07:58:13', '2026-04-22 07:58:13'),
    (7, 20, '2026-04-22 07:58:00', 2, 'El repartidor está en la zona de La Torrecilla buscando el número.', '2026-04-22 07:58:21', '2026-04-22 07:58:21'),
    (7, 21, '2026-04-22 07:58:00', 3, 'Tenemos que revisar si quedan unidades en el almacén de Rabanales.', '2026-04-22 07:58:38', '2026-04-22 07:58:38'),
    (7, 22, '2026-04-22 07:58:00', 4, 'Está reunido con un proveedor, dice que pase después de comer hoy.', '2026-04-22 07:58:55', '2026-04-22 07:58:55'),
    (7, 25, '2026-04-22 07:59:00', 6, 'Negocio cerrado permanentemente, hay un cartel de se alquila aquí.', '2026-04-22 07:59:18', '2026-04-22 07:59:18'),
    (6, 12, '2026-04-22 07:59:00', 7, 'Dice que el precio es excesivo para su presupuesto de este año.', '2026-04-22 07:59:48', '2026-04-22 07:59:48'),
    (6, 16, '2026-04-22 08:00:00', 1, 'Todo perfecto, cliente VIP que quiere ampliar el contrato pronto.', '2026-04-22 08:00:11', '2026-04-22 08:00:11'),
    (6, 17, '2026-04-22 08:00:00', 3, 'Esperando a que el jefe de zona dé el visto bueno al descuento.', '2026-04-22 08:00:38', '2026-04-22 08:00:38'),
    (6, 11, '2026-04-22 08:00:00', 7, 'Se ha mudado a otra zona y ya no le interesa nuestro servicio.', '2026-04-22 08:01:03', '2026-04-22 08:01:03');
