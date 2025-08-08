-- Agregar columna user_email a la tabla contenidos para almacenar el correo del usuario que subió el contenido
ALTER TABLE contenidos
ADD COLUMN user_email VARCHAR(255) AFTER user_id;
