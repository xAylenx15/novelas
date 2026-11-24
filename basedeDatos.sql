---------------------------------------------------------
-- 1. CREACIÓN DE TABLAS (Ordenadas por dependencias)
---------------------------------------------------------

CREATE TABLE estados (
    id_estado SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE generos (
    id_genero SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE novelas (
    id_novela SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    sinopsis TEXT,
    portada VARCHAR(255)NOT NULL,
    autor VARCHAR(255) NOT NULL,
    id_genero INT NOT NULL REFERENCES generos(id_genero),
    id_estado INT NOT NULL REFERENCES estados(id_estado),
    CONSTRAINT uq_novela_titulo_autor UNIQUE (titulo, autor)
);

CREATE TABLE capitulos (
    capitulo_id SERIAL PRIMARY KEY,
    id_novela INT NOT NULL REFERENCES novelas(id_novela) ON DELETE CASCADE,
    nombre VARCHAR(255),
    numero INT NOT NULL,
    texto TEXT NOT NULL,
    CONSTRAINT uq_capitulo_novela_numero UNIQUE (id_novela, numero)
);


---------------------------------------------------------
-- 2. INSERCIONES DE DATOS BASE
---------------------------------------------------------

INSERT INTO estados (nombre)
VALUES 
    ('En emision'),
    ('Finalizado'),
    ('Abandonado');

INSERT INTO generos (nombre)
VALUES
    ('Romance'),
    ('Fantasia'),
    ('Ciencia Ficcion'),
    ('Misterio'),
    ('Suspenso'),
    ('Terror'),
    ('Drama'),
    ('Aventura'),
    ('Accion'),
    ('Comedia'),
    ('Historico'),
    ('Policial'),
    ('Psicologico'),
    ('Juvenil'),
    ('Slice of Life'),
    ('Apocaliptico'),
    ('Distopia'),
    ('Mitologia'),
    ('Erotico');


---------------------------------------------------------
-- 3. VISTAS
---------------------------------------------------------



CREATE VIEW vw_novelas_completas AS
SELECT 
    n.id_novela,
    n.titulo,
    n.sinopsis,
    n.portada,
    n.autor,
    n.id_genero,
    g.nombre AS genero,
    n.id_estado,
    e.nombre AS estado,
    COUNT(c.capitulo_id) AS cantidad_capitulos
FROM novelas n
JOIN generos g ON n.id_genero = g.id_genero
JOIN estados e ON n.id_estado = e.id_estado
LEFT JOIN capitulos c ON n.id_novela = c.id_novela
GROUP BY n.id_novela, g.nombre, e.nombre;

CREATE OR REPLACE VIEW vw_capitulos_completos AS
SELECT 
    c.capitulo_id,
    c.id_novela,
    n.titulo AS novela_titulo,
    c.numero,
    c.nombre,
    c.texto
FROM capitulos c
INNER JOIN novelas n ON c.id_novela = n.id_novela;


---------------------------------------------------------
-- 4. FUNCIONES DE NOVELAS
---------------------------------------------------------

CREATE OR REPLACE FUNCTION fn_altaDeNovelas(
    ptitulo VARCHAR, 
    psinopsis TEXT, 
    pportada VARCHAR,
    p_id_estado INT, 
    p_id_genero INT, 
    pautor VARCHAR
)
RETURNS INTEGER AS $$
DECLARE
    v_id INTEGER;
    v_autor VARCHAR;
BEGIN
    IF ptitulo IS NULL OR btrim(ptitulo) = '' THEN
        RAISE EXCEPTION 'El título no puede estar vacío';
    END IF;

	IF length(btrim(ptitulo)) > 255 THEN
    	RAISE EXCEPTION 'El título no puede superar los 255 caracteres';
	END IF;

    IF pportada IS NULL OR btrim(pportada) = '' THEN
        RAISE EXCEPTION 'La portada no puede estar vacía';
    END IF;

    IF lower(btrim(pportada)) NOT LIKE 'http://%' AND lower(btrim(pportada)) NOT LIKE 'https://%' THEN
    	RAISE EXCEPTION 'La portada debe ser una dirección válida (http:// o https://)';
	END IF;
    IF p_id_genero IS NULL THEN
        RAISE EXCEPTION 'Debe seleccionar un género';
    END IF;

    IF p_id_estado IS NULL THEN
        RAISE EXCEPTION 'Debe seleccionar un estado';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM generos WHERE id_genero = p_id_genero) THEN
        RAISE EXCEPTION 'El género seleccionado no existe en la base de datos';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM estados WHERE id_estado = p_id_estado) THEN
        RAISE EXCEPTION 'El estado seleccionado no existe en la base de datos';
    END IF;

    v_autor := COALESCE(NULLIF(btrim(pautor), ''), 'Desconocido');
	IF lower(v_autor) = 'desconocido' THEN
    	v_autor := 'Desconocido';
	END IF;
    
    IF EXISTS (SELECT 1 FROM novelas WHERE titulo = btrim(ptitulo) AND autor = v_autor) THEN
        RAISE EXCEPTION 'Ya existe una novela con ese titulo y autor';
    END IF;

INSERT INTO novelas (titulo, sinopsis, portada, id_estado, id_genero, autor)
VALUES (btrim(ptitulo), btrim(psinopsis), btrim(pportada), p_id_estado, p_id_genero, v_autor)
    RETURNING id_novela INTO v_id;

    RETURN v_id;
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION fn_baja_novela(p_id_novela INT)
RETURNS TEXT
AS
$$
BEGIN
    -- Verificar que la novela exista
    IF NOT EXISTS (SELECT id_novela FROM novelas WHERE id_novela = p_id_novela) THEN
        RAISE EXCEPTION 'La novela no existe en la base de datos';
    END IF;

    -- Eliminar la novela y sus capítulos
    DELETE FROM novelas
    WHERE id_novela = p_id_novela;

    RETURN 'Novela eliminada correctamente';

END;
$$
LANGUAGE plpgsql;



-- Funcion para modificar novela

CREATE OR REPLACE FUNCTION fn_modificar_novela(
    p_id_novela INT,
    p_Datos_A_Modificar JSONB
)
RETURNS novelas
AS $$
DECLARE
    var_aux novelas;
    var_textual TEXT;
    var_clave TEXT;
	var_id int;
	var_numeric numeric;
    v_hubo_cambios BOOLEAN := FALSE;
BEGIN
    -- 1. Comprobar que los datos no sean SQL NULL
    IF p_Datos_A_Modificar IS NULL THEN
        RAISE EXCEPTION 'Los datos para modificar no pueden ser nulos';
    END IF;

    -- 2. Comprobar que el JSONB sea un objeto
    IF jsonb_typeof(p_Datos_A_Modificar)
       IS DISTINCT FROM 'object' THEN
        RAISE EXCEPTION 'Los datos deben ser un objeto JSON válido';
    END IF;

    -- 3. Comprobar que el objeto no esté vacío
    IF p_Datos_A_Modificar = '{}'::jsonb THEN
        RAISE EXCEPTION 'No se especificaron datos para modificar';
    END IF;

    -- 4. Comprobar que no tenga más de seis claves
    IF (
        SELECT COUNT(*)
        FROM jsonb_object_keys(p_Datos_A_Modificar)
    ) > 6 THEN
        RAISE EXCEPTION 'No se pueden modificar más de seis campos';
    END IF;

    -- 5. Comprobar que las claves sean válidas
    FOR var_clave IN
        SELECT jsonb_object_keys(p_Datos_A_Modificar)
    LOOP
        IF var_clave NOT IN (
            'titulo', 'sinopsis', 'portada',
            'autor', 'genero', 'estado'
        ) THEN
            RAISE EXCEPTION 'Clave inválida: %', var_clave;
        END IF;
    END LOOP;

    -- 6. Buscar la novela
    SELECT *
    INTO var_aux
    FROM novelas
    WHERE id_novela = p_id_novela;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se encontró ninguna novela con ese ID';
    END IF;

    -- 7. Modificar el título
    IF p_Datos_A_Modificar ? 'titulo' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'titulo')
           IS DISTINCT FROM 'string' THEN
            RAISE EXCEPTION 'El nuevo valor para el título no es válido';
        END IF;

        var_textual := btrim(p_Datos_A_Modificar ->> 'titulo');

        IF var_textual = '' THEN
            RAISE EXCEPTION 'No se puede ingresar una cadena vacía para el titulo';
        END IF;
		
        IF var_textual IS DISTINCT FROM var_aux.titulo THEN
            var_aux.titulo := var_textual;
            v_hubo_cambios := TRUE;
        END IF;
    END IF;

    -- 8. Modificar la sinopsis
    IF p_Datos_A_Modificar ? 'sinopsis' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'sinopsis')
           IS DISTINCT FROM 'string'
           AND jsonb_typeof(p_Datos_A_Modificar -> 'sinopsis')
           IS DISTINCT FROM 'null' THEN
            RAISE EXCEPTION 'El nuevo valor para la sinopsis no es válido';
        END IF;

        var_textual := btrim(p_Datos_A_Modificar ->> 'sinopsis');

		-- la sinopsis puede retener una cadena vacia
        IF var_textual IS DISTINCT FROM var_aux.sinopsis THEN
            var_aux.sinopsis := var_textual;
            v_hubo_cambios := TRUE;
        END IF;
    END IF;

    -- 9. Modificar la portada
    IF p_Datos_A_Modificar ? 'portada' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'portada')
           IS DISTINCT FROM 'string' THEN
            RAISE EXCEPTION 'El nuevo valor para la portada no es válido';
        END IF;

        var_textual := btrim(p_Datos_A_Modificar ->> 'portada');

        IF (var_textual) = '' THEN
            RAISE EXCEPTION 'No se puede ingresar una cadena vacía para la portada';
        END IF;
		-- rdta parte no garantiza que sea un url valido
        IF lower(var_textual) LIKE 'http://%'
           OR lower(var_textual) LIKE 'https://%' THEN

            IF var_textual IS DISTINCT FROM var_aux.portada THEN
                var_aux.portada := var_textual;
                v_hubo_cambios := TRUE;
            END IF;

        ELSE
            RAISE EXCEPTION 'No es una dirección válida para la portada';
        END IF;
    END IF;

    -- 10. Modificar el autor
    IF p_Datos_A_Modificar ? 'autor' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'autor')
           IS DISTINCT FROM 'string' 
		   AND jsonb_typeof(p_Datos_A_Modificar -> 'autor')
		   IS DISTINCT FROM 'null' THEN
            RAISE EXCEPTION 'El nuevo valor para el autor no es válido';
        END IF;

        var_textual := btrim(p_Datos_A_Modificar ->> 'autor');

        IF (var_textual) = '' OR var_textual is NULL then 
			var_textual:= 'Desconocido';
		end if;

		IF lower(var_textual) = 'desconocido' THEN
    		var_textual := 'Desconocido';
		END IF;

        IF var_textual IS DISTINCT FROM var_aux.autor THEN
            var_aux.autor := var_textual;
            v_hubo_cambios := TRUE;
        END IF;
    END IF;

    -- 11. Modificar el género
    IF p_Datos_A_Modificar ? 'genero' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'genero')
           IS DISTINCT FROM 'number' THEN
            RAISE EXCEPTION 'El nuevo valor para el género no es válido';
        END IF;

		var_numeric:= (p_Datos_A_Modificar ->> 'genero')::numeric;
		if var_numeric > 2147483647 then
			raise exception 'El valor pasado para genero supera el limite de int';
		end if;
		if var_numeric <= 0 then 
			raise exception 'El valo pasado para genero no puede ser negativo o cero';
		end if;
		if trunc(var_numeric)!= var_numeric then 
			raise exception 'El valor pasado para genero no corresponde a un numero entero';
		end if;
        var_id := (var_numeric)::int;
		IF NOT EXISTS (SELECT 1
    				   FROM generos
    				   WHERE id_genero = var_id
		) THEN
    		RAISE EXCEPTION 'El género indicado no existe';
		END IF;
		if var_id is distinct from var_aux.id_genero then
			var_aux.id_genero := var_id;
			v_hubo_cambios := true;
		end if;
    END IF;

    -- 12. Modificar el estado
    IF p_Datos_A_Modificar ? 'estado' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'estado')
           IS DISTINCT FROM 'number' THEN
            RAISE EXCEPTION 'El nuevo valor para el estado no es válido';
        END IF;

        var_numeric:= (p_Datos_A_Modificar ->> 'estado')::numeric;
		if var_numeric > 2147483647 then
			raise exception 'El valor pasado para estado supera el limite de int';
		end if;
		if var_numeric <= 0 then 
			raise exception 'El valor pasado para estado no puede ser negativo o cero';
		end if;
		if trunc(var_numeric)!= var_numeric then 
			raise exception 'El valor pasado para estado no corresponde a un numero entero';
		end if;
        var_id := (var_numeric)::int;
		IF NOT EXISTS (SELECT 1
    				   FROM estados
    				   WHERE id_estado = var_id
		) THEN
    		RAISE EXCEPTION 'El estado indicado no existe';
		END IF;
		if var_id is distinct from var_aux.id_estado then
			var_aux.id_estado := var_id;
			v_hubo_cambios := true;
		end if;
    END IF;

    -- 13. Comprobar que haya habido al menos una modificación
    IF NOT v_hubo_cambios THEN
        RAISE EXCEPTION 'No se realizaron modificaciones';
    END IF;

	-- Antes de guardar los cambios en la tabla controlaremos que no exista una novela
 	-- con esas caracteristicas 
	if (exists (select 1
				from novelas 
				where autor = var_aux.autor
				and titulo = var_aux.titulo
				and id_novela is distinct from p_id_novela)) then
 		raise exception 'Ya existe una novela con ese titulo y autor';
	end if;

    -- 14. Guardar los cambios en la tabla
    UPDATE novelas
    SET
        titulo = var_aux.titulo,
        sinopsis = var_aux.sinopsis,
        portada = var_aux.portada,
        autor = var_aux.autor,
        id_genero = var_aux.id_genero,
        id_estado = var_aux.id_estado
    WHERE id_novela = p_id_novela
    RETURNING * INTO var_aux;

    -- 15. Comprobar que se haya actualizado la novela
    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se pudo actualizar la novela';
    END IF;

    -- 16. Devolver la novela actualizada
    RETURN var_aux;

END;
$$ LANGUAGE plpgsql;


---------------------------------------------------------
-- 5. FUNCIONES DE CAPÍTULOS
---------------------------------------------------------

CREATE OR REPLACE FUNCTION fn_altaCapitulo(
    p_id_novela INT,
    p_nombre VARCHAR,
    p_numero INT,
    p_texto TEXT
) RETURNS INT AS $$
DECLARE
    v_nuevo_id INT;
BEGIN
    IF p_id_novela IS NULL OR p_numero IS NULL THEN
        RAISE EXCEPTION 'El ID de la novela y el número de capítulo son obligatorios';
    END IF;

    IF length(btrim(COALESCE(p_nombre, ''))) > 255 THEN
    	RAISE EXCEPTION 'El nombre no puede superar los 255 caracteres';
	END IF;

    IF btrim(COALESCE(p_texto, '')) = '' THEN
        RAISE EXCEPTION 'El texto del capítulo no puede estar vacío';
    END IF;

    IF p_numero < 0 THEN
        RAISE EXCEPTION 'El número de capítulo no debe ser negativo';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM novelas WHERE id_novela = p_id_novela) THEN
        RAISE EXCEPTION 'La novela indicada no existe en la base de datos';
    END IF;

    IF EXISTS (SELECT 1 FROM capitulos WHERE id_novela = p_id_novela AND numero = p_numero) THEN
        RAISE EXCEPTION 'El capítulo número % ya existe para esta novela', p_numero;
    END IF;

    INSERT INTO capitulos (id_novela, nombre, numero, texto)
	VALUES (p_id_novela, NULLIF(btrim(p_nombre), ''), p_numero, btrim(p_texto))

    RETURNING capitulo_id INTO v_nuevo_id;

    RETURN v_nuevo_id;
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION fn_bajaCapitulo(p_id_capitulo INT) 
RETURNS TEXT AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM capitulos WHERE capitulo_id = p_id_capitulo) THEN
        RAISE EXCEPTION 'El capítulo que intenta eliminar no existe';
    END IF;

    DELETE FROM capitulos WHERE capitulo_id = p_id_capitulo;

    RETURN 'Capítulo eliminado correctamente';
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_modificarCapitulo(
    p_id_capitulo INT,
    p_id_novela INT,
    p_datos_a_modificar JSONB
)
RETURNS capitulos
AS 
$$
DECLARE
    var_aux capitulos;
    var_textual TEXT;
    var_aux_ent int;
	var_aux_numeric numeric;
    var_hubo_cambios BOOLEAN := false;
    var_cantidad_claves INT;
BEGIN
    -- 1. VERIFICAR QUE EL CAPITULO PERTENEZCA A LA NOVELA
    SELECT *
    INTO var_aux
    FROM capitulos
    WHERE capitulo_id = p_id_capitulo 
      AND id_novela = p_id_novela;
      
    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se encontró el capítulo indicado o no pertenece a la novela especificada';
    END IF;

    -- 2. CONTROLES GENERALES DEL JSONB
    IF p_datos_a_modificar IS NULL THEN
        RAISE EXCEPTION 'Los datos a modificar no pueden ser NULL';
    END IF;
    
    IF jsonb_typeof(p_datos_a_modificar) IS DISTINCT FROM 'object' THEN
        RAISE EXCEPTION 'Los datos deben tener formato de objeto JSON';
    END IF;
    
    SELECT COUNT(*)
    INTO var_cantidad_claves
    FROM jsonb_object_keys(p_datos_a_modificar);
    
    IF var_cantidad_claves = 0 THEN
        RAISE EXCEPTION 'No se proporcionaron atributos para modificar';
    END IF;
    
    IF var_cantidad_claves > 3 THEN
        RAISE EXCEPTION 'Se superó la cantidad de atributos permitidos';
    END IF;
    
    IF EXISTS (
        SELECT 1
        FROM jsonb_object_keys(p_datos_a_modificar) AS claves(clave)
        WHERE clave NOT IN ('nombre', 'numero', 'texto')
    ) THEN
        RAISE EXCEPTION 'Se proporcionaron atributos no permitidos';
    END IF;

    -- 3. MODIFICAR NOMBRE
    IF p_datos_a_modificar ? 'nombre' THEN
        IF jsonb_typeof(p_datos_a_modificar->'nombre') IS DISTINCT FROM 'string'
           AND jsonb_typeof(p_datos_a_modificar->'nombre') IS DISTINCT FROM 'null' THEN
            RAISE EXCEPTION 'El valor pasado no corresponde a un nombre de capítulo';
        END IF;
        
        var_textual := btrim(p_datos_a_modificar->>'nombre');
        
        IF length(var_textual) > 255 THEN
            RAISE EXCEPTION 'El nombre no puede superar los 255 caracteres';
        END IF;

        IF var_textual = '' THEN
            var_textual := NULL;
        END IF;
        
        IF var_textual IS DISTINCT FROM var_aux.nombre THEN
            var_aux.nombre := var_textual;
            var_hubo_cambios := true;
        END IF;
    END IF;

    -- 4. MODIFICAR NUMERO
    IF p_datos_a_modificar ? 'numero' THEN
        IF jsonb_typeof(p_datos_a_modificar->'numero') IS DISTINCT FROM 'number' THEN
            RAISE EXCEPTION 'El valor pasado no corresponde a un número';
        END IF;

		var_aux_numeric := (p_datos_a_modificar->>'numero')::numeric;
		
		if(var_aux_numeric > 2147483647) then 
			raise exception 'El valor pasado para el capitulo supera el limite permitido';
		end if;
		
		if(var_aux_numeric < 0) then
			raise exception 'El valor pasado para el capitulo es un numero negativo';
		end if;

		if (trunc(var_aux_numeric) != var_aux_numeric) then
			raise exception 'El valor pasado para el capitulo no es un numero entero';
		end if;

		var_aux_ent := trunc(var_aux_numeric)::int;
		
		if (exists(select 1
					   from capitulos 
					   where numero = var_aux_ent
      						 AND id_novela = p_id_novela
							 AND capitulo_id is distinct from p_id_capitulo)) then -- Corregido: capitulo_id
			raise exception 'El numero de capitulo que queremos colocar, ya existe';
		end if;

        IF var_aux_ent IS DISTINCT FROM var_aux.numero THEN
            var_aux.numero := var_aux_ent;
            var_hubo_cambios := true;
        END IF;
    END IF;

    -- 5. MODIFICAR TEXTO
    IF p_datos_a_modificar ? 'texto' THEN
        IF jsonb_typeof(p_datos_a_modificar->'texto') IS DISTINCT FROM 'string' THEN
            RAISE EXCEPTION 'El valor pasado no corresponde al texto del capítulo';
        END IF;
        
        var_textual := btrim(p_datos_a_modificar->>'texto'); 
        
        IF var_textual = '' THEN
            RAISE EXCEPTION 'El texto del capítulo no puede estar vacío';
        END IF;
        
        IF var_textual IS DISTINCT FROM var_aux.texto THEN
            var_aux.texto := var_textual;
            var_hubo_cambios := true;
        END IF;
    END IF;

    -- 6. VERIFICAR SI HUBO CAMBIOS
    IF NOT var_hubo_cambios THEN
        RAISE EXCEPTION 'No se realizaron modificaciones';
    END IF;

    -- 7. ACTUALIZAR CAPITULO
    UPDATE capitulos
    SET nombre = var_aux.nombre,
        numero = var_aux.numero,
        texto = var_aux.texto
    WHERE id_novela = p_id_novela
      AND capitulo_id = p_id_capitulo 
	RETURNING * INTO var_aux;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se puede actualizar el capítulo';
    END IF;

    -- 8. DEVOLVER CAPITULO MODIFICADO
    RETURN var_aux;
END;
$$
language plpgsql;


---------------------------------------------------------
-- 6. FUNCIONES DE LECTURA DE PÁGINA
---------------------------------------------------------

CREATE OR REPLACE FUNCTION fn_get_novela(p_id INT)
RETURNS vw_novelas_completas AS $$
DECLARE
    v_fila vw_novelas_completas;
BEGIN
    SELECT * INTO v_fila FROM vw_novelas_completas WHERE id_novela = p_id;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No existe ninguna novela con id %', p_id;
    END IF;

    RETURN v_fila;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fn_get_capitulo(p_id INT)
RETURNS vw_capitulos_completos AS $$
DECLARE
    v_fila vw_capitulos_completos;
BEGIN
    SELECT * INTO v_fila FROM vw_capitulos_completos WHERE capitulo_id = p_id;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'No existe ningún capítulo con id %', p_id;
    END IF;

    RETURN v_fila;
END;
$$ LANGUAGE plpgsql;
