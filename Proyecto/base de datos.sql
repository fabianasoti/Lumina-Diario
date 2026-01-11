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
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE usuarios 
ADD COLUMN token_reset VARCHAR(64) NULL DEFAULT NULL,
ADD COLUMN token_expira DATETIME NULL DEFAULT NULL;
    

ALTER TABLE usuarios ADD COLUMN username VARCHAR(20) AFTER apellido;

ALTER TABLE usuarios 
MODIFY COLUMN username VARCHAR(20) NOT NULL;

ALTER TABLE usuarios 
ADD CONSTRAINT uk_username UNIQUE (username);

ALTER TABLE usuarios 
ADD CONSTRAINT chk_username_formato 
CHECK (username REGEXP '^[a-zA-Z0-9._]{5,20}$');

/* 1. Añadimos el rol (por defecto todos son usuarios normales) */
ALTER TABLE usuarios ADD COLUMN rol VARCHAR(10) DEFAULT 'user';

/* 2. Añadimos el campo para rastrear la actividad */
ALTER TABLE usuarios ADD COLUMN ultima_conexion DATETIME DEFAULT NULL;

/* 3. ¡IMPORTANTE! Conviértete TÚ en el administrador */
/* Cambia 'tu_email@ejemplo.com' por TU email real con el que te registraste */
UPDATE usuarios SET rol = 'admin' WHERE email = 'tu_email@ejemplo.com';

ALTER TABLE entradas ADD COLUMN intensidad INT DEFAULT 5;
/*Esto añade una columna numérica del 1 al 10, con valor 5 por defecto para la intensidad de las emociones.*/

ALTER TABLE usuarios ADD COLUMN ultimo_cambio_nombre DATETIME DEFAULT NULL;
/*Necesitamos una "marca de tiempo" para saber cuándo fue la última vez que el usuario cambió su nombre.*/


