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






