-- =============================================
-- Base de datos LeadChain para PostgreSQL
-- Incluye timestamps de Laravel (created_at, updated_at)
-- Requiere extensión PostGIS para tipos geometry
-- =============================================

-- Habilitar extensión PostGIS
CREATE EXTENSION IF NOT EXISTS postgis;

-- Eliminar tablas si existen (en orden inverso por dependencias)
DROP TABLE IF EXISTS visitas CASCADE;
DROP TABLE IF EXISTS edificios CASCADE;
DROP TABLE IF EXISTS clientes CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS estados_visita CASCADE;
DROP TABLE IF EXISTS zonas CASCADE;

-- =============================================
-- TABLA: ZONAS
-- =============================================
CREATE TABLE zonas (
    id SERIAL PRIMARY KEY,
    nombre_zona VARCHAR(100) NOT NULL,
    poligono_coordenadas GEOMETRY(Point, 4326) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: ESTADOS_VISITA
-- =============================================
CREATE TABLE estados_visita (
    id SERIAL PRIMARY KEY,
    etiqueta VARCHAR(50) NOT NULL,
    color_hex VARCHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLA: USERS
-- =============================================
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    id_responsable INTEGER,
    id_zona INTEGER,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_responsable FOREIGN KEY (id_responsable) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_user_zona FOREIGN KEY (id_zona) REFERENCES zonas(id) ON DELETE SET NULL
);

-- =============================================
-- TABLA: CLIENTES
-- =============================================
CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(255),
    id_usuario_asignado INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cliente_usuario FOREIGN KEY (id_usuario_asignado) REFERENCES users(id) ON DELETE RESTRICT
);

-- =============================================
-- TABLA: EDIFICIOS
-- =============================================
CREATE TABLE edificios (
    id SERIAL PRIMARY KEY,
    direccion_completa VARCHAR(255) NOT NULL,
    planta VARCHAR(20),
    puerta VARCHAR(10),
    ubicacion GEOMETRY(Point, 4326) NOT NULL,
    id_zona INTEGER NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    id_cliente INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_edificio_zona FOREIGN KEY (id_zona) REFERENCES zonas(id) ON DELETE RESTRICT,
    CONSTRAINT fk_edificio_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE SET NULL
);

-- =============================================
-- TABLA: VISITAS
-- =============================================
CREATE TABLE visitas (
    id SERIAL PRIMARY KEY,
    id_usuario INTEGER NOT NULL,
    id_cliente INTEGER NOT NULL,
    fecha_hora TIMESTAMP NOT NULL,
    hora_visita TIME,
    id_estado INTEGER NOT NULL,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visita_usuario FOREIGN KEY (id_usuario) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_visita_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_visita_estado FOREIGN KEY (id_estado) REFERENCES estados_visita(id) ON DELETE RESTRICT
);

-- =============================================
-- ÍNDICES para mejorar rendimiento
-- =============================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_zona ON users(id_zona);
CREATE INDEX idx_clientes_usuario ON clientes(id_usuario_asignado);
CREATE INDEX idx_edificios_zona ON edificios(id_zona);
CREATE INDEX idx_visitas_usuario ON visitas(id_usuario);
CREATE INDEX idx_visitas_cliente ON visitas(id_cliente);
CREATE INDEX idx_visitas_fecha ON visitas(fecha_hora);

-- Índices espaciales para columnas geometry
CREATE INDEX idx_zonas_poligono ON zonas USING GIST(poligono_coordenadas);
CREATE INDEX idx_edificios_ubicacion ON edificios USING GIST(ubicacion);

-- =============================================
-- DATOS INICIALES
-- =============================================

-- Estados de visita por defecto
INSERT INTO estados_visita (etiqueta, color_hex) VALUES
    ('Pendiente', '#FFA500'),
    ('Confirmada', '#28A745'),
    ('Cancelada', '#DC3545'),
    ('Completada', '#007BFF'),
    ('Reprogramada', '#6C757D');

-- Zonas de Córdoba (España)
-- ST_SetSRID(ST_MakePoint(longitud, latitud), 4326) para crear puntos geográficos
INSERT INTO zonas (nombre_zona, poligono_coordenadas) VALUES
    ('Centro', ST_GeomFromText('POINT(-4.7794 37.8882)', 4326)),
    ('La Judería', ST_GeomFromText('POINT(-4.7822 37.8794)', 4326)),
    ('San Basilio', ST_GeomFromText('POINT(-4.7856 37.8756)', 4326)),
    ('La Ribera', ST_GeomFromText('POINT(-4.7731 37.8856)', 4326));

-- Usuario de pruebas (root / root)
-- Hash bcrypt de "root"
INSERT INTO users (nombre, apellidos, email, password, rol, id_zona) VALUES
    ('Admin', 'Root', 'root@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin', 1);

-- Usuarios comerciales de ejemplo
INSERT INTO users (nombre, apellidos, email, password, rol, id_responsable, id_zona) VALUES
    ('Juan', 'García López', 'juan.garcia@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 1),
    ('María', 'Fernández Ruiz', 'maria.fernandez@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 2),
    ('Pedro', 'Martínez Sánchez', 'pedro.martinez@leadchain.com', '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'comercial', 1, 3);

-- Clientes de ejemplo
INSERT INTO clientes (nombre, apellidos, telefono, email, id_usuario_asignado) VALUES
    ('Antonio', 'López Moreno', '657123456', 'antonio.lopez@email.com', 2),
    ('Carmen', 'Rodríguez Pérez', '658234567', 'carmen.rodriguez@email.com', 2),
    ('Francisco', 'Jiménez Torres', '659345678', 'francisco.jimenez@email.com', 3),
    ('Isabel', 'Navarro Delgado', '660456789', 'isabel.navarro@email.com', 4);

-- Edificios de ejemplo en Córdoba
INSERT INTO edificios (direccion_completa, planta, puerta, ubicacion, id_zona, tipo, id_cliente) VALUES
    ('Calle Cruz Conde 15, Córdoba', '2', 'A', ST_GeomFromText('POINT(-4.7794 37.8882)', 4326), 1, 'residencial', 1),
    ('Avenida Gran Capitán 8, Córdoba', 'Bajo', NULL, ST_GeomFromText('POINT(-4.7731 37.8900)', 4326), 1, 'comercial', NULL),
    ('Calle Judería 3, Córdoba', '1', 'B', ST_GeomFromText('POINT(-4.7822 37.8794)', 4326), 2, 'residencial', 2),
    ('Calle San Basilio 22, Córdoba', '3', 'C', ST_GeomFromText('POINT(-4.7856 37.8756)', 4326), 3, 'residencial', 3),
    ('Plaza de las Tendillas 1, Córdoba', '1', NULL, ST_GeomFromText('POINT(-4.7789 37.8847)', 4326), 1, 'comercial', NULL),
    ('Calle Cruz Conde 15, Córdoba', '5', 'D', ST_GeomFromText('POINT(-4.7794 37.8882)', 4326), 1, 'residencial', 4);

-- Visitas de ejemplo
INSERT INTO visitas (id_usuario, id_cliente, fecha_hora, hora_visita, id_estado, observaciones) VALUES
    (2, 1, '2026-03-10 10:00:00', '10:00:00', 1, 'Primera visita comercial'),
    (2, 2, '2026-03-10 12:00:00', '12:00:00', 2, 'Interesado en servicios premium'),
    (3, 3, '2026-03-11 09:30:00', '09:30:00', 1, 'Seguimiento de propuesta'),
    (4, 4, '2026-03-12 16:00:00', '16:00:00', 4, 'Contrato firmado');
