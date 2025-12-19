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
    password VARCHAR(255) NOT NULL,
    )
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



