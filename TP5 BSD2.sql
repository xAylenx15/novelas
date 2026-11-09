/* TP 5 */
/* Ejercicio 1: Crear un disparador para impedir que ingresen
 * dos proveedores en el mimsmo domicilio (tener en cuenta el
 * país y ciudad) */


create or replace function validacion_proveedores()
returns trigger as
$$
begin
	if(exists(select *
			  from suppliers s
			  where lower(trim(s.address)) = lower(trim(new.address))
			  and lower(trim(s.city)) = lower(trim(new.city))
			  and lower(trim(s.country)) = lower(trim(new.country))
			  and s.supplierid is distinct from new.supplierid)) then
		raise exception 'Ya hay un proveedor con esa dirección';
	end if;
    return new;
end;
$$ language plpgsql;

create trigger tg_validacion_proveedores
before insert or update on suppliers
for each row
execute function validacion_proveedores();



/* 2. Realizar un disparador que impida incluir en un detalle de orden, cantidades no disponibles. */

create or replace function controlarStock()
returns trigger as
$$
declare
	stock_actual int;
	diferencia int;
begin
	if new.quantity is null or new.quantity <= 0 then
		raise exception 'La cantidad debe ser mayor a cero';
	end if;

	if tg_op = 'UPDATE' and new.productid = old.productid then
		diferencia := new.quantity - old.quantity;
	else
		diferencia := new.quantity;
	end if;

	select unitsinstock into stock_actual
	from products
	where productid = new.productid;

	if stock_actual < diferencia then
		raise exception 'No hay stock suficiente: disponible %, se necesitan % más',
			stock_actual, diferencia;
	end if;

	return new;
end;
$$
language plpgsql;

create trigger fn_stockControl
before insert or update on orderdetails
for each row
execute function controlarStock();

/*3. Realizar un disparador que actualice el nivel de stock cuando se crean, modifican o eliminan
órdenes (y sus detalles). */ 


CREATE OR REPLACE FUNCTION actualizar_stock()
RETURNS TRIGGER AS
$$
BEGIN
    IF TG_OP = 'INSERT' THEN
        UPDATE products
        SET unitsinstock = unitsinstock - NEW.quantity
        WHERE productid = NEW.productid;
        RETURN NEW;
    ELSIF TG_OP = 'UPDATE' THEN
        IF OLD.productid = NEW.productid THEN
            UPDATE products
            SET unitsinstock = unitsinstock + OLD.quantity - NEW.quantity
            WHERE productid = NEW.productid;
        ELSE
            UPDATE products
            SET unitsinstock = unitsinstock + OLD.quantity
            WHERE productid = OLD.productid;

            UPDATE products
            SET unitsinstock = unitsinstock - NEW.quantity
            WHERE productid = NEW.productid;
        END IF;
        RETURN NEW;
    ELSIF TG_OP = 'DELETE' THEN
        UPDATE products
        SET unitsinstock = unitsinstock + OLD.quantity
        WHERE productid = OLD.productid;
        RETURN OLD;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER fn_tg_stock
AFTER INSERT OR UPDATE OR DELETE ON orderdetails
FOR EACH ROW
EXECUTE FUNCTION actualizar_stock();

/*4. Realizar un disparador de auditoría sobre la actualización de datos de los clientes. Se debe
almacenar el nombre del usuario la fecha en la que se hizo la actualización, la operación
realizada (alta/baja/modificación) y el valor que tenía cada atributo al momento de la
operación.*/


CREATE TABLE auditoria_clientes (
    id_auditoria serial primary key,
    usuario varchar,
    fecha timestamp,
    operacion varchar,
    datos_completos text
);

create or replace function fn_ActuAuditoria()
returns trigger as
$$
begin 
	if TG_OP = 'INSERT' then
	 	INSERT INTO auditoria_clientes(usuario, fecha, operacion, datos_completos) 			
		values (current_user , current_timestamp , 'Alta' , new::text);
		return new;
	elsif TG_OP = 'UPDATE' then
		INSERT INTO auditoria_clientes(usuario, fecha, operacion, datos_completos) 			
		values (current_user , current_timestamp , 'Modificacion' , old::text);
		return new;
	elsif TG_OP = 'DELETE' then
	 	INSERT INTO auditoria_clientes(usuario, fecha, operacion, datos_completos) 			
		values (current_user , current_timestamp , 'Baja' , old::text);
		return old;
	end if;
end;
$$
language plpgsql;

create trigger tg_auditoria
after insert or update or delete on customers
for each row
execute function fn_ActuAuditoria();


/* 5. Agregar atributos en el encabezado de la Orden que registren la cantidad de artículos
del detalle y el importe total de cada orden. Realizar los triggers necesarios para
mantener la redundancia controlada de los nuevos atributos. */

alter table orders
	add column cantidad_articulos int default 0,
	add column importe_total numeric(12,2) default 0;

update orders o
set cantidad_articulos = coalesce((select sum(od.quantity)
                                   from orderdetails od
                                   where od.orderid = o.orderid), 0),
    importe_total = coalesce((select sum(od.quantity * od.unitprice * (1 - od.discount))
                              from orderdetails od
                              where od.orderid = o.orderid), 0);

create or replace function fn_actualizar_totales()
returns trigger as
$$
begin
    if TG_OP = 'INSERT' then
        update orders
        set cantidad_articulos = coalesce(cantidad_articulos, 0) + new.quantity,
            importe_total = coalesce(importe_total, 0) + (new.quantity * new.unitprice * (1 - new.discount))
        where orderid = new.orderid;
        return new;

    elsif TG_OP = 'DELETE' then
        update orders
        set cantidad_articulos = coalesce(cantidad_articulos, 0) - old.quantity,
            importe_total = coalesce(importe_total, 0) - (old.quantity * old.unitprice * (1 - old.discount))
        where orderid = old.orderid;
        return old;

    elsif TG_OP = 'UPDATE' then
        if old.orderid = new.orderid then
            update orders
            set cantidad_articulos = coalesce(cantidad_articulos, 0) - old.quantity + new.quantity,
                importe_total = coalesce(importe_total, 0)
                                - (old.quantity * old.unitprice * (1 - old.discount))
                                + (new.quantity * new.unitprice * (1 - new.discount))
            where orderid = new.orderid;
        else
            update orders
            set cantidad_articulos = coalesce(cantidad_articulos, 0) - old.quantity,
                importe_total = coalesce(importe_total, 0) - (old.quantity * old.unitprice * (1 - old.discount))
            where orderid = old.orderid;

            update orders
            set cantidad_articulos = coalesce(cantidad_articulos, 0) + new.quantity,
                importe_total = coalesce(importe_total, 0) + (new.quantity * new.unitprice * (1 - new.discount))
            where orderid = new.orderid;
        end if;
        return new;
    end if;
end;
$$
language plpgsql;

create trigger tg_totales_ordenes
after insert or update or delete on orderdetails
for each row
execute function fn_actualizar_totales();

create or replace function fn_proteger_totales()
returns trigger as
$$
begin
    if pg_trigger_depth() = 1
       and (new.cantidad_articulos is distinct from old.cantidad_articulos
            or new.importe_total is distinct from old.importe_total) then
        raise exception 'cantidad_articulos e importe_total no se pueden modificar manualmente';
    end if;
    return new;
end;
$$
language plpgsql;

create trigger tg_proteger_totales
before update on orders
for each row
execute function fn_proteger_totales();


/* Ejercicio 6.A */ 

/* 1 (a.i) - En los triggers a nivel de sentencia (FOR EACH STATEMENT), intentar acceder 
   a un atributo de la variable NEW (como NEW.columna) arroja un error en tiempo de 
   ejecución en PostgreSQL indicando que el registro no está asignado ("record 'new' is 
   not assigned yet"), en lugar de evaluar a un valor NULL ordinario. La variable NEW 
   solo está instanciada en triggers FOR EACH ROW. */

/* 2 (a.ii) - Se ignora el valor de retorno de un trigger tipo BEFORE FOR EACH STATEMENT. 
   Por lo tanto, retornar NULL o NEW no cancela la operación que se está realizando. */

/* 3 (a.iii) - Ante un RAISE EXCEPTION, el alcance transaccional dicta que se genera un 
   rollback completo de toda la sentencia SQL. La transacción se aborta de inmediato 
   y se deshace cualquier cambio previo que se haya realizado dentro de ese bloque. */


CREATE OR REPLACE FUNCTION registrar_intento_pedido()
RETURNS TRIGGER AS $$
BEGIN
    RAISE NOTICE 'Se ejecuto una sentencia INSERT sobre orders';

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER tg_registrar_pedido
BEFORE INSERT ON orders
FOR EACH STATEMENT
EXECUTE FUNCTION registrar_intento_pedido();

/*b. AFTER - FOR EACH ROW
iv. ¿Qué sucede con la variable NEW?
v. ¿Qué sucede ante una modificación de valores de NEW y RETURN NEW?
vi. ¿Qué sucede ante un RETURN NULL?
vii. ¿Qué sucede ante un RAISE EXCEPTION?*/

/* Ejercicio 6-b: Utilizaremos el trigger del ejercicio 5 para responder las preguntas 
 *  4- NEW si es el caso del insert contiene los valores de la nueva fila de orderdetails 
 *         si es el caso del update contiene los nuevos valores del detalle modificado
 *         si es el caso del delete no esta disponible porque solamente se utiliza old en ese caso
 *  5- Ante una modificación de los valores de NEW no pasa nada porque al ser AFTER esos valores ya se guardaron
 *     y si devuelvo new tampoco pasaria nada porque ya se registro el cambio
 *  6- Nada porque trabajamos con un trigger tipo After por lo cual el valor retornado se ignora por lo que return null o return new 
 *     no cancela ni revierte la operación original  
 * 7- Ante un RAISE EXCEPTION se aborta la transacción y se produce un rollback completo. 
   Aunque el trigger sea AFTER y la operación original ya se haya ejecutado, PostgreSQL 
   revierte todo el bloque transaccional, anulando los cambios realizados. */
 














