CREATE DATABASE diarioemocional;
USE diarioemocional;

CREATE USER 
'diarioemocional'@'localhost' 
IDENTIFIED  BY 'Diarioemocional123$';

GRANT USAGE ON *.* TO 'diarioemocional'@'localhost';

ALTER USER 'diarioemocional'@'localhost' 
REQUIRE NONE 
WITH MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;

GRANT ALL PRIVILEGES ON diarioemocional.* 
TO 'diarioemocional'@'localhost';

FLUSH PRIVILEGES;


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    edad INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    )
);

ALTER TABLE usuarios 
ADD COLUMN token_reset VARCHAR(64) NULL DEFAULT NULL,
ADD COLUMN token_expira DATETIME NULL DEFAULT NULL;
    

INSERT INTO usuarios (nombre, apellido, email, edad, password) VALUES
('Ana', 'Martínez', 'ana.martinez@email.com', 25, 'Ana1234!'),
('Carlos', 'Gómez', 'carlos.gomez@email.com', 32, 'Carlo$89'),
('Lucía', 'Fernández', 'lucia.fernandez@email.com', 28, 'Lucia#45'),
('Miguel', 'Rodríguez', 'miguel.rodriguez@email.com', 40, 'Miguel@12'),
('Sofía', 'López', 'sofia.lopez@email.com', 22, 'Sofia*78'),
('Javier', 'Pérez', 'javier.perez@email.com', 35, 'Javi123!'),
('María', 'Sánchez', 'maria.sanchez@email.com', 30, 'Maria%90'),
('Daniel', 'Torres', 'daniel.torres@email.com', 27, 'Dani&456'),
('Elena', 'Ruiz', 'elena.ruiz@email.com', 34, 'Elena_33'),
('Pablo', 'Navarro', 'pablo.navarro@email.com', 29, 'Pablo+88');


INSERT INTO usuarios (nombre, apellido, email, edad, password) VALUES('Susana', 'Santana', 'info@susana.com', 23, 'Susana123$');

ALTER TABLE usuarios ADD COLUMN username VARCHAR(20) AFTER apellido;

UPDATE usuarios SET username = 'ana_mar' WHERE email = 'ana.martinez@email.com';
UPDATE usuarios SET username = 'carlos_g' WHERE email = 'carlos.gomez@email.com';
-- Haz esto con todos tus usuarios actuales...
UPDATE usuarios SET username = 'susana_s' WHERE email = 'info@susana.com';

ALTER TABLE usuarios 
MODIFY COLUMN username VARCHAR(20) NOT NULL;

ALTER TABLE usuarios 
ADD CONSTRAINT uk_username UNIQUE (username);

ALTER TABLE usuarios 
ADD CONSTRAINT chk_username_formato 
CHECK (username REGEXP '^[a-zA-Z0-9._]{5,20}$');



