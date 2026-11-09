/*PROYECTO BASE DE DATOS*/

create table  novelas (
    id_novela serial primary key,
    titulo varchar(255) not null ,
    sinopsis text,
    portada varchar(255)
);

create table capitulos (
    id_capitulo serial primary key,
    id_novela int references novelas(id_novela),
    nombre varchar(255),
    texto text 
);



create or replace procedure fn_altaDeNovelas(ptitulo varchar, psinopsis text, pportada varchar)
as
$$
declare 
v_id integer;
begin 
	insert into novelas(titulo, sinopsis,portada) 
	values (ptitulo,psinopsis,pportada);
	returning id_novela into v_id;

	return v_id;
end;
$$
language plpgsql;






