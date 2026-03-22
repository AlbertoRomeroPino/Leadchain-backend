-- =============================================
-- Base de datos LeadChain para PostgreSQL
-- Coincide con las migraciones y seeders de Laravel
-- Requiere extensión PostGIS para tipos geometry
-- =============================================

-- Habilitar extensión PostGIS
CREATE EXTENSION IF NOT EXISTS postgis;

-- Eliminar tablas si existen (en orden inverso por dependencias)
DROP TABLE IF EXISTS visitas CASCADE;
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
    nombre_zona VARCHAR(100) NOT NULL,
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
    planta VARCHAR(20),
    puerta VARCHAR(10),
    ubicacion GEOMETRY(Point, 4326) NOT NULL,
    id_zona BIGINT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    id_cliente BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_edificio_zona FOREIGN KEY (id_zona) REFERENCES zonas(id) ON DELETE RESTRICT,
    CONSTRAINT fk_edificio_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE SET NULL
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
    hora_visita TIME,
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
-- DATOS INICIALES (Seeders)
-- =============================================

-- Zonas de Córdoba (ZonaSeeder)
-- Cada zona es una cuadrícula definida por un polígono
INSERT INTO zonas (nombre_zona, area) VALUES
    ('Centro',
        ST_GeomFromText('POLYGON((-4.7850 37.8920, -4.7740 37.8920, -4.7740 37.8850, -4.7850 37.8850, -4.7850 37.8920))', 4326)),
    ('La Judería',
        ST_GeomFromText('POLYGON((-4.7870 37.8830, -4.7780 37.8830, -4.7780 37.8760, -4.7870 37.8760, -4.7870 37.8830))', 4326)),
    ('San Basilio',
        ST_GeomFromText('POLYGON((-4.7900 37.8790, -4.7810 37.8790, -4.7810 37.8720, -4.7900 37.8720, -4.7900 37.8790))', 4326)),
    ('La Ribera',
        ST_GeomFromText('POLYGON((-4.7780 37.8900, -4.7680 37.8900, -4.7680 37.8820, -4.7780 37.8820, -4.7780 37.8900))', 4326));

-- Estados de visita (EstadoVisitaSeeder)
INSERT INTO estados_visita (etiqueta, color_hex) VALUES
    ('Pendiente', '#FFA500'),
    ('Confirmada', '#28A745'),
    ('Cancelada', '#DC3545'),
    ('Completada', '#007BFF'),
    ('Reprogramada', '#6C757D');

-- Usuario admin (UserSeeder) - contraseña: root
-- Hash bcrypt de "root"
INSERT INTO users (nombre, apellidos, email, password, rol, id_zona) VALUES
    ('Admin', 'Root', 'root@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin', 1);

-- Usuarios comerciales (UserSeeder) - contraseña: root
INSERT INTO users (nombre, apellidos, email, password, rol, id_responsable, id_zona) VALUES
    ('Juan', 'García López', 'juan.garcia@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 1),
    ('María', 'Fernández Ruiz', 'maria.fernandez@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 2),
    ('Pedro', 'Martínez Sánchez', 'pedro.martinez@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 3);

-- Clientes (ClienteSeeder)
INSERT INTO clientes (nombre, apellidos, telefono, email) VALUES
    ('Antonio', 'López Moreno', '657123456', 'antonio.lopez@email.com'),
    ('Carmen', 'Rodríguez Pérez', '658234567', 'carmen.rodriguez@email.com'),
    ('Francisco', 'Jiménez Torres', '659345678', 'francisco.jimenez@email.com'),
    ('Isabel', 'Navarro Delgado', '660456789', 'isabel.navarro@email.com');

-- Edificios (EdificioSeeder)
INSERT INTO edificios (direccion_completa, planta, puerta, ubicacion, id_zona, tipo, id_cliente) VALUES
    ('Calle Cruz Conde 15, Córdoba', '2', 'A', ST_GeomFromText('POINT(-4.7794 37.8882)', 4326), 1, 'residencial', 1),
    ('Avenida Gran Capitán 8, Córdoba', 'Bajo', NULL, ST_GeomFromText('POINT(-4.7731 37.8900)', 4326), 1, 'comercial', NULL),
    ('Calle Judería 3, Córdoba', '1', 'B', ST_GeomFromText('POINT(-4.7822 37.8794)', 4326), 2, 'residencial', 2),
    ('Calle San Basilio 22, Córdoba', '3', 'C', ST_GeomFromText('POINT(-4.7856 37.8756)', 4326), 3, 'residencial', 3),
    ('Plaza de las Tendillas 1, Córdoba', '1', NULL, ST_GeomFromText('POINT(-4.7789 37.8847)', 4326), 1, 'comercial', NULL),
    ('Calle Cruz Conde 15, Córdoba', '5', 'D', ST_GeomFromText('POINT(-4.7794 37.8882)', 4326), 1, 'residencial', 4);

-- Visitas (VisitaSeeder)
INSERT INTO visitas (id_usuario, id_cliente, fecha_hora, hora_visita, id_estado, observaciones) VALUES
    (2, 1, '2026-03-10 10:00:00', '10:00:00', 1, 'Primera visita comercial'),
    (2, 2, '2026-03-10 12:00:00', '12:00:00', 2, 'Interesado en servicios premium'),
    (3, 3, '2026-03-11 09:30:00', '09:30:00', 1, 'Seguimiento de propuesta'),
    (4, 4, '2026-03-12 16:00:00', '16:00:00', 4, 'Contrato firmado');
