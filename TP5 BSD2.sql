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










