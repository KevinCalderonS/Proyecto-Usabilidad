-- Agregar columna user_id a la tabla contenidos para controlar propiedad de contenido
ALTER TABLE contenidos
ADD COLUMN user_id INT NOT NULL AFTER id;

-- Agregar clave foránea si existe tabla usuarios
ALTER TABLE contenidos
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE;
