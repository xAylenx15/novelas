/*PROYECTO BASE DE DATOS*/

create table  novelas (
    id_novela serial primary key,
    titulo varchar(255) not null ,
    sinopsis text,
    portada varchar(255)
);

create table capitulos (
    capitulo_id serial primary key,
    id_novela int references novelas(id_novela),
    nombre varchar(255),
    numero int,
    texto text 
);



create or replace function fn_altaDeNovelas(ptitulo varchar, psinopsis text, pportada varchar)
returns integer
as
$$
declare
v_id integer;
begin
    insert into novelas(titulo, sinopsis,portada)
    values (ptitulo,psinopsis,pportada)
    RETURNING id_novela INTO v_id;
    
    return v_id;
end;
$$
language plpgsql;



--BAJA FISICA CON DELETE ON CASCADE
--Para que al eliminar una novela, tambien se eliminen sus capitulos
ALTER TABLE capitulos
DROP CONSTRAINT capitulos_id_novela_fkey;

ALTER TABLE capitulos
ADD CONSTRAINT capitulos_id_novela_fkey
FOREIGN KEY (id_novela)
REFERENCES novelas(id_novela)
ON DELETE CASCADE;

--FUNCION INDIVIDUAL BAJA
CREATE OR REPLACE FUNCTION fn_baja_novela(p_id_novela int)
RETURNS text
AS
$$
BEGIN

    IF NOT EXISTS (SELECT 1 FROM novelas WHERE id_novela = p_id_novela) THEN
        RETURN 'La novela no existe';
    END IF;
    DELETE FROM novelas
    WHERE id_novela = p_id_novela;
    RETURN 'Novela eliminada correctamente';

END;
$$
LANGUAGE plpgsql;


--UNA SOLA FUNCION ABM

CREATE OR REPLACE FUNCTION fn_abm_novela(p_accion text,p_id_novela int,p_titulo varchar,p_sinopsis text,p_portada varchar)
RETURNS text AS
$$
BEGIN
    -- Validar la accion
    IF p_accion IS NULL OR p_accion NOT IN ('A', 'M', 'B') THEN
        RAISE EXCEPTION 'Accion invalida';
    END IF;
    -- BAJA
    IF p_accion = 'B' THEN
        -- Verificar que se haya ingresado un ID
        IF p_id_novela IS NULL THEN
            RAISE EXCEPTION 'Debe ingresar el ID de la novela';
        END IF;
        -- Verificar que exista la novela
        IF NOT EXISTS (SELECT 1 FROM novelas WHERE id_novela = p_id_novela) THEN
            RAISE EXCEPTION 'La novela que desea eliminar no existe';
        END IF;
        -- Eliminar novela y sus capitulos
        DELETE FROM novelas
        WHERE id_novela = p_id_novela;

        RETURN 'Novela eliminada correctamente';

    END IF;

    RETURN 'AQUI VAN LOS OTROS IF...ALTA Y MODIFICACION';

END;
$$
LANGUAGE plpgsql;


-- Función para Alta de Capítulo
CREATE OR REPLACE FUNCTION fn_altaCapitulo(p_id_novela int, p_nombre varchar, p_numero int, p_texto text)
RETURNS void
AS $$
BEGIN
    INSERT INTO capitulos (id_novela, nombre, numero, texto) 
    VALUES (p_id_novela, p_nombre, p_numero, p_texto);
END;
$$ LANGUAGE plpgsql;

-- Función para Baja de Capítulo
CREATE OR REPLACE FUNCTION fn_bajaCapitulo(p_capitulo_id int)
RETURNS void
AS $$
BEGIN
    DELETE FROM capitulos WHERE capitulo_id = p_capitulo_id;
END;
$$ LANGUAGE plpgsql;


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

    -- 4. Comprobar que no tenga más de tres claves
    IF (
        SELECT COUNT(*)
        FROM jsonb_object_keys(p_Datos_A_Modificar)
    ) > 3 THEN
        RAISE EXCEPTION 'No se pueden modificar más de tres campos';
    END IF;

    -- 5. Comprobar que las claves sean válidas
    FOR var_clave IN
        SELECT jsonb_object_keys(p_Datos_A_Modificar)
    LOOP
        IF var_clave NOT IN ('titulo', 'sinopsis', 'portada') THEN
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

    -- 7. Modificar el título, si fue enviado
    IF p_Datos_A_Modificar ? 'titulo' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'titulo')
           IS DISTINCT FROM 'string' THEN
            RAISE EXCEPTION 'El nuevo valor para el título no es válido';
        END IF;

        var_textual := p_Datos_A_Modificar ->> 'titulo';

        IF btrim(var_textual) = '' THEN
            RAISE EXCEPTION 'No se puede ingresar una cadena vacía';
        END IF;

        IF var_textual IS DISTINCT FROM var_aux.titulo THEN
            var_aux.titulo := var_textual;
            v_hubo_cambios := TRUE;
        END IF;
    END IF;

    -- 8. Modificar la sinopsis, si fue enviada
    IF p_Datos_A_Modificar ? 'sinopsis' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'sinopsis')
           IS DISTINCT FROM 'string'
           AND jsonb_typeof(p_Datos_A_Modificar -> 'sinopsis')
           IS DISTINCT FROM 'null' THEN
            RAISE EXCEPTION 'El nuevo valor para la sinopsis no es válido';
        END IF;

        var_textual := p_Datos_A_Modificar ->> 'sinopsis';

        IF var_textual IS DISTINCT FROM var_aux.sinopsis THEN
            var_aux.sinopsis := var_textual;
            v_hubo_cambios := TRUE;
        END IF;
    END IF;

    -- 9. Modificar la portada, si fue enviada
    IF p_Datos_A_Modificar ? 'portada' THEN

        IF jsonb_typeof(p_Datos_A_Modificar -> 'portada')
           IS DISTINCT FROM 'string' THEN
            RAISE EXCEPTION 'El nuevo valor para la portada no es válido';
        END IF;

        var_textual := p_Datos_A_Modificar ->> 'portada';

        IF btrim(var_textual) = '' THEN
            RAISE EXCEPTION 'No se puede ingresar una cadena vacía';
        END IF;

        IF var_textual LIKE 'http://%'
           OR var_textual LIKE 'https://%' THEN

            IF var_textual IS DISTINCT FROM var_aux.portada THEN
                var_aux.portada := var_textual;
                v_hubo_cambios := TRUE;
            END IF;

        ELSE
            RAISE EXCEPTION 'No es una dirección válida';
        END IF;
    END IF;

    -- 10. Comprobar que haya habido al menos una modificación
    IF NOT v_hubo_cambios THEN
        RAISE EXCEPTION 'No se realizaron modificaciones';
    END IF;

    -- 11. Guardar los cambios en la tabla
    UPDATE novelas
    SET
        titulo = var_aux.titulo,
        sinopsis = var_aux.sinopsis,
        portada = var_aux.portada
    WHERE id_novela = p_id_novela
    RETURNING * INTO var_aux;

    -- 12. Comprobar que se haya actualizado la novela
    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se pudo actualizar la novela';
    END IF;

    -- 13. Devolver la novela actualizada
    RETURN var_aux;

END;
$$ LANGUAGE plpgsql;




