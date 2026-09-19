-- ============================================================
-- DIVERPOOOL MASCOTAS
-- Base de datos principal
-- MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS diverpoool
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE diverpoool;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. ROLES
-- ============================================================

DROP TABLE IF EXISTS rol_permisos;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS calificaciones;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS metodos_pago;
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS estados_reserva;
DROP TABLE IF EXISTS disponibilidad;
DROP TABLE IF EXISTS horarios;
DROP TABLE IF EXISTS sedes;
DROP TABLE IF EXISTS servicio_profesionales;
DROP TABLE IF EXISTS profesional_especialidades;
DROP TABLE IF EXISTS especialidades;
DROP TABLE IF EXISTS profesionales;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS categorias_servicios;
DROP TABLE IF EXISTS historial_medico;
DROP TABLE IF EXISTS mascota_vacunas;
DROP TABLE IF EXISTS vacunas;
DROP TABLE IF EXISTS razas;
DROP TABLE IF EXISTS especies;
DROP TABLE IF EXISTS mascotas;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;


CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 2. PERMISOS
-- ============================================================

CREATE TABLE permisos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    modulo VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE rol_permisos (
    rol_id INT UNSIGNED NOT NULL,
    permiso_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (rol_id, permiso_id),

    CONSTRAINT fk_rol_permisos_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_rol_permisos_permiso
        FOREIGN KEY (permiso_id)
        REFERENCES permisos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 3. USUARIOS
-- ============================================================

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    rol_id INT UNSIGNED NOT NULL,

    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,

    correo VARCHAR(150) NOT NULL UNIQUE,

    telefono VARCHAR(30),

    password_hash VARCHAR(255) NOT NULL,

    documento VARCHAR(30) UNIQUE,

    estado ENUM(
        'activo',
        'inactivo',
        'bloqueado'
    ) NOT NULL DEFAULT 'activo',

    ultimo_acceso DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 4. ESPECIES
-- ============================================================

CREATE TABLE especies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(80) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    estado TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 5. RAZAS
-- ============================================================

CREATE TABLE razas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    especie_id INT UNSIGNED NOT NULL,

    nombre VARCHAR(100) NOT NULL,

    descripcion VARCHAR(255),

    estado TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_raza_especie (especie_id, nombre),

    CONSTRAINT fk_razas_especie
        FOREIGN KEY (especie_id)
        REFERENCES especies(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 6. MASCOTAS
-- ============================================================

CREATE TABLE mascotas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    especie_id INT UNSIGNED NOT NULL,

    raza_id INT UNSIGNED NULL,

    nombre VARCHAR(100) NOT NULL,

    sexo ENUM(
        'macho',
        'hembra'
    ) NOT NULL,

    fecha_nacimiento DATE NULL,

    peso DECIMAL(6,2) NULL,

    color VARCHAR(80),

    microchip VARCHAR(100) UNIQUE,

    foto VARCHAR(255),

    esterilizado TINYINT(1) DEFAULT 0,

    observaciones TEXT,

    estado TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_mascotas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mascotas_especie
        FOREIGN KEY (especie_id)
        REFERENCES especies(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_mascotas_raza
        FOREIGN KEY (raza_id)
        REFERENCES razas(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- ============================================================
-- 7. VACUNAS
-- ============================================================

CREATE TABLE vacunas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(120) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    periodicidad_meses INT UNSIGNED NULL,

    estado TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 8. MASCOTA VACUNAS
-- ============================================================

CREATE TABLE mascota_vacunas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    mascota_id INT UNSIGNED NOT NULL,

    vacuna_id INT UNSIGNED NOT NULL,

    fecha_aplicacion DATE NOT NULL,

    fecha_proxima DATE NULL,

    veterinario VARCHAR(150),

    observaciones TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_mascota_vacunas_mascota
        FOREIGN KEY (mascota_id)
        REFERENCES mascotas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mascota_vacunas_vacuna
        FOREIGN KEY (vacuna_id)
        REFERENCES vacunas(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 9. HISTORIAL MÉDICO
-- ============================================================

CREATE TABLE historial_medico (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    mascota_id INT UNSIGNED NOT NULL,

    profesional_id INT UNSIGNED NULL,

    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    tipo VARCHAR(100) NOT NULL,

    diagnostico TEXT,

    tratamiento TEXT,

    observaciones TEXT,

    archivo VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historial_mascota
        FOREIGN KEY (mascota_id)
        REFERENCES mascotas(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 10. CATEGORÍAS DE SERVICIOS
-- ============================================================

CREATE TABLE categorias_servicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    imagen VARCHAR(255),

    estado TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 11. SERVICIOS
-- ============================================================

CREATE TABLE servicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    categoria_id INT UNSIGNED NOT NULL,

    nombre VARCHAR(150) NOT NULL,

    descripcion TEXT,

    duracion_minutos INT UNSIGNED NOT NULL,

    precio DECIMAL(12,2) NOT NULL DEFAULT 0,

    imagen VARCHAR(255),

    requiere_profesional TINYINT(1) DEFAULT 1,

    estado ENUM(
        'activo',
        'inactivo'
    ) DEFAULT 'activo',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_servicios_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias_servicios(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 12. ESPECIALIDADES
-- ============================================================

CREATE TABLE especialidades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(120) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    estado TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 13. PROFESIONALES
-- ============================================================

CREATE TABLE profesionales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL UNIQUE,

    numero_tarjeta_profesional VARCHAR(100),

    descripcion TEXT,

    experiencia_anios INT UNSIGNED DEFAULT 0,

    foto VARCHAR(255),

    estado ENUM(
        'activo',
        'inactivo',
        'vacaciones'
    ) DEFAULT 'activo',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_profesionales_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 14. PROFESIONAL ESPECIALIDADES
-- ============================================================

CREATE TABLE profesional_especialidades (
    profesional_id INT UNSIGNED NOT NULL,

    especialidad_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (
        profesional_id,
        especialidad_id
    ),

    CONSTRAINT fk_prof_especialidad_profesional
        FOREIGN KEY (profesional_id)
        REFERENCES profesionales(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_prof_especialidad_especialidad
        FOREIGN KEY (especialidad_id)
        REFERENCES especialidades(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 15. SERVICIO PROFESIONALES
-- ============================================================

CREATE TABLE servicio_profesionales (
    servicio_id INT UNSIGNED NOT NULL,

    profesional_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (
        servicio_id,
        profesional_id
    ),

    CONSTRAINT fk_servicio_prof_servicio
        FOREIGN KEY (servicio_id)
        REFERENCES servicios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_servicio_prof_profesional
        FOREIGN KEY (profesional_id)
        REFERENCES profesionales(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 16. SEDES
-- ============================================================

CREATE TABLE sedes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    direccion VARCHAR(255) NOT NULL,

    ciudad VARCHAR(100) NOT NULL DEFAULT 'Bogotá',

    telefono VARCHAR(30),

    correo VARCHAR(150),

    latitud DECIMAL(10,7),

    longitud DECIMAL(10,7),

    estado TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 17. HORARIOS
-- ============================================================

CREATE TABLE horarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    sede_id INT UNSIGNED NOT NULL,

    dia_semana TINYINT UNSIGNED NOT NULL,

    hora_inicio TIME NOT NULL,

    hora_fin TIME NOT NULL,

    activo TINYINT(1) DEFAULT 1,

    CONSTRAINT chk_dia_semana
        CHECK (dia_semana BETWEEN 1 AND 7),

    CONSTRAINT fk_horarios_sede
        FOREIGN KEY (sede_id)
        REFERENCES sedes(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 18. DISPONIBILIDAD
-- ============================================================

CREATE TABLE disponibilidad (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    profesional_id INT UNSIGNED NOT NULL,

    horario_id INT UNSIGNED NOT NULL,

    fecha DATE NULL,

    hora_inicio TIME NOT NULL,

    hora_fin TIME NOT NULL,

    estado ENUM(
        'disponible',
        'ocupado',
        'bloqueado'
    ) DEFAULT 'disponible',

    CONSTRAINT fk_disponibilidad_profesional
        FOREIGN KEY (profesional_id)
        REFERENCES profesionales(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_disponibilidad_horario
        FOREIGN KEY (horario_id)
        REFERENCES horarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 19. ESTADOS DE RESERVA
-- ============================================================

CREATE TABLE estados_reserva (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(50) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;


-- ============================================================
-- 20. RESERVAS
-- ============================================================

CREATE TABLE reservas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    mascota_id INT UNSIGNED NOT NULL,

    servicio_id INT UNSIGNED NOT NULL,

    profesional_id INT UNSIGNED NULL,

    sede_id INT UNSIGNED NOT NULL,

    estado_id INT UNSIGNED NOT NULL,

    fecha DATE NOT NULL,

    hora_inicio TIME NOT NULL,

    hora_fin TIME NOT NULL,

    precio DECIMAL(12,2) NOT NULL DEFAULT 0,

    observaciones TEXT,

    motivo_cancelacion TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_mascota
        FOREIGN KEY (mascota_id)
        REFERENCES mascotas(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_servicio
        FOREIGN KEY (servicio_id)
        REFERENCES servicios(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_profesional
        FOREIGN KEY (profesional_id)
        REFERENCES profesionales(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_reservas_sede
        FOREIGN KEY (sede_id)
        REFERENCES sedes(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_estado
        FOREIGN KEY (estado_id)
        REFERENCES estados_reserva(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 21. MÉTODOS DE PAGO
-- ============================================================

CREATE TABLE metodos_pago (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(80) NOT NULL UNIQUE,

    descripcion VARCHAR(255),

    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;


-- ============================================================
-- 22. PAGOS
-- ============================================================

CREATE TABLE pagos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    reserva_id INT UNSIGNED NOT NULL,

    metodo_pago_id INT UNSIGNED NOT NULL,

    referencia VARCHAR(150),

    monto DECIMAL(12,2) NOT NULL,

    estado ENUM(
        'pendiente',
        'aprobado',
        'rechazado',
        'reembolsado'
    ) DEFAULT 'pendiente',

    fecha_pago DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pagos_reserva
        FOREIGN KEY (reserva_id)
        REFERENCES reservas(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_pagos_metodo
        FOREIGN KEY (metodo_pago_id)
        REFERENCES metodos_pago(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 23. FACTURAS
-- ============================================================

CREATE TABLE facturas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    reserva_id INT UNSIGNED NOT NULL UNIQUE,

    numero VARCHAR(50) NOT NULL UNIQUE,

    subtotal DECIMAL(12,2) NOT NULL,

    impuesto DECIMAL(12,2) NOT NULL DEFAULT 0,

    total DECIMAL(12,2) NOT NULL,

    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,

    estado ENUM(
        'emitida',
        'anulada'
    ) DEFAULT 'emitida',

    CONSTRAINT fk_facturas_reserva
        FOREIGN KEY (reserva_id)
        REFERENCES reservas(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- ============================================================
-- 24. CALIFICACIONES
-- ============================================================

CREATE TABLE calificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    reserva_id INT UNSIGNED NOT NULL UNIQUE,

    usuario_id INT UNSIGNED NOT NULL,

    profesional_id INT UNSIGNED NULL,

    puntuacion TINYINT UNSIGNED NOT NULL,

    comentario TEXT,

    respuesta TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_puntuacion
        CHECK (puntuacion BETWEEN 1 AND 5),

    CONSTRAINT fk_calificaciones_reserva
        FOREIGN KEY (reserva_id)
        REFERENCES reservas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_calificaciones_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_calificaciones_profesional
        FOREIGN KEY (profesional_id)
        REFERENCES profesionales(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- ============================================================
-- 25. NOTIFICACIONES
-- ============================================================

CREATE TABLE notificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT UNSIGNED NOT NULL,

    titulo VARCHAR(150) NOT NULL,

    mensaje TEXT NOT NULL,

    tipo VARCHAR(50),

    referencia_id INT UNSIGNED NULL,

    leida TINYINT(1) DEFAULT 0,

    fecha_lectura DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notificaciones_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- DATOS INICIALES
-- ============================================================

INSERT INTO roles (nombre, descripcion)
VALUES
('Administrador', 'Administración completa del sistema'),
('Cliente', 'Usuario propietario de mascotas'),
('Profesional', 'Profesional encargado de prestar servicios');


INSERT INTO estados_reserva (nombre, descripcion)
VALUES
('Pendiente', 'Reserva creada esperando confirmación'),
('Confirmada', 'Reserva confirmada'),
('En proceso', 'Servicio actualmente en ejecución'),
('Completada', 'Servicio terminado'),
('Cancelada', 'Reserva cancelada');


INSERT INTO metodos_pago (nombre, descripcion)
VALUES
('Efectivo', 'Pago realizado en el establecimiento'),
('Tarjeta', 'Pago mediante tarjeta'),
('Transferencia', 'Transferencia bancaria'),
('PSE', 'Pago electrónico mediante PSE');


INSERT INTO especies (nombre, descripcion)
VALUES
('Perro', 'Canino doméstico'),
('Gato', 'Felino doméstico'),
('Ave', 'Ave doméstica'),
('Conejo', 'Conejo doméstico'),
('Otro', 'Otra especie');


INSERT INTO categorias_servicios
(nombre, descripcion)
VALUES
('Peluquería', 'Cuidado estético e higiene'),
('Veterinaria', 'Atención veterinaria'),
('Terapias', 'Terapias y bienestar'),
('Guardería', 'Cuidado durante el día'),
('Guardería campestre', 'Cuidado en espacios abiertos'),
('Consultas', 'Consultas especializadas');


INSERT INTO sedes
(
    nombre,
    direccion,
    ciudad,
    telefono,
    correo
)
VALUES
(
    'Diverpoool Mascotas',
    'Dirección pendiente',
    'Bogotá',
    '3000000000',
    'contacto@diverpoool.com'
);


SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
