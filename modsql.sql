
CREATE OR REPLACE FUNCTION insert_alertas_compras()
  RETURNS trigger AS
$BODY$
BEGIN
IF NEW.condicion_pago = 'cr' THEN
 IF NEW.is_gasto = FALSE THEN
  INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
  (generarid('alertas_id_alerta_seq'), 'compras', NEW.id_compra, 'compra', 'Vencimiento de la compra ' || NEW.serie || '-' || NEW.folio, DATE(NEW.fecha) + NEW.plazo_credito);
 ELSE
  INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
  (generarid('alertas_id_alerta_seq'), 'compras', NEW.id_compra, 'gasto', 'Vencimiento del gasto' || NEW.serie || '-' || NEW.folio, DATE(NEW.fecha) + NEW.plazo_credito);
 END IF; 
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION insert_alertas_compras() OWNER TO programa;


CREATE OR REPLACE FUNCTION insert_alertas_facturas()
  RETURNS trigger AS
$BODY$
BEGIN
IF NEW.condicion_pago = 'cr' THEN
INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
(generarid('alertas_id_alerta_seq'), 'facturacion', NEW.id_factura, '', 'Vencimiento de la factura ' || NEW.serie || '-' || NEW.folio, DATE(NEW.fecha) + NEW.plazo_credito);
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION insert_alertas_facturas() OWNER TO programa;


CREATE OR REPLACE FUNCTION insert_alertas_tickets()
  RETURNS trigger AS
$BODY$
BEGIN
IF NEW.tipo_pago = 'credito' THEN
INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
(generarid('alertas_id_alerta_seq'), 'tickets', NEW.id_ticket, '', 'Vencimiento del ticket ' || NEW.folio, DATE(NEW.fecha) + NEW.dias_credito);
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION insert_alertas_tickets() OWNER TO programa;


CREATE OR REPLACE FUNCTION update_alertas_compras()
  RETURNS trigger AS
$BODY$
BEGIN
IF NEW.status = 'pa' OR NEW.status = 'ca' OR NEW.status='n' THEN
 DELETE FROM alertas WHERE tabla_obj='compras' AND id_obj1=OLD.id_compra;
ELSEIF NEW.status = 'p' THEN
 IF OLD.is_gasto = FALSE THEN
  INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
  (generarid('alertas_id_alerta_seq'), 'compras', NEW.id_compra, 'compra', 'Vencimiento de la compra ' || NEW.serie || '-' || NEW.folio, DATE(NEW.fecha) + NEW.plazo_credito);
 ELSE
  INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
  (generarid('alertas_id_alerta_seq'), 'compras', NEW.id_compra, 'gasto', 'Vencimiento del gasto' || NEW.serie || '-' || NEW.folio, DATE(NEW.fecha) + NEW.plazo_credito);
 END IF;
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION update_alertas_compras() OWNER TO programa;


CREATE OR REPLACE FUNCTION update_alertas_facturas()
  RETURNS trigger AS
$BODY$
DECLARE
row_data RECORD;
BEGIN
IF NEW.status = 'pa' OR NEW.status = 'ca' THEN
DELETE FROM alertas WHERE tabla_obj='facturacion' AND id_obj1=OLD.id_factura;

IF NEW.status = 'ca' THEN
FOR row_data IN SELECT ft.id_ticket, t.folio, t.fecha, t.dias_credito, t.status
FROM facturacion_tickets ft
INNER JOIN tickets t ON ft.id_ticket=t.id_ticket
WHERE ft.id_factura=OLD.id_factura LOOP

IF row_data.status = 'p' THEN
INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES
(generarid('alertas_id_alerta_seq'), 'tickets', row_data.id_ticket, '', 'Vencimiento del ticket ' || row_data.folio, DATE(row_data.fecha) + row_data.dias_credito);
END IF;
END LOOP;
END IF;

ELSEIF NEW.status = 'p' THEN
INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES
(generarid('alertas_id_alerta_seq'), 'facturacion', OLD.id_factura, '', 'Vencimiento de la factura ' || OLD.serie || '-' || OLD.folio, DATE(NEW.fecha) + OLD.plazo_credito);
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION update_alertas_facturas() OWNER TO programa;


CREATE OR REPLACE FUNCTION update_alertas_facturas_tickets()
  RETURNS trigger AS
$BODY$
BEGIN
 DELETE FROM alertas WHERE tabla_obj='tickets' AND id_obj1=NEW.id_ticket;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION update_alertas_facturas_tickets() OWNER TO programa;


CREATE OR REPLACE FUNCTION update_alertas_tickets()
  RETURNS trigger AS
$BODY$
BEGIN
IF NEW.status = 'pa' OR NEW.status = 'ca' THEN
 DELETE FROM alertas WHERE tabla_obj='tickets' AND id_obj1=OLD.id_ticket;
ELSEIF NEW.status = 'p' THEN
 INSERT INTO alertas (id_alerta, tabla_obj, id_obj1, id_obj2, descripcion, fecha_vencimiento) VALUES 
(generarid('alertas_id_alerta_seq'), 'tickets', OLD.id_ticket, '', 'Vencimiento del ticket ' || NEW.folio, DATE(NEW.fecha) + OLD.dias_credito);
END IF;
return null;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION update_alertas_tickets() OWNER TO programa;



CREATE OR REPLACE VIEW get_tickets_pendientes AS 
 SELECT t.id_cliente, t.id_ticket, t.fecha, t.folio, c.nombre_fiscal AS cliente, get_clientes_vuelo(tv.id_vuelo, t.id_cliente) AS otros_clientes, count(*) AS vuelos
   FROM tickets t
   JOIN clientes c ON t.id_cliente::text = c.id_cliente::text
   LEFT JOIN tickets_vuelos tv ON t.id_ticket::text = tv.id_ticket::text
   LEFT JOIN ( SELECT ts.id_ticket, sum(ts.total) AS total
   FROM (         SELECT ft.id_ticket, count(f.id_factura) AS total
                   FROM facturacion f
              JOIN facturacion_tickets ft ON f.id_factura::text = ft.id_factura::text
             WHERE f.status::text <> 'ca'::text
             GROUP BY ft.id_ticket
        UNION 
                 SELECT nvt.id_ticket, count(nv.id_nota_venta) AS total
                   FROM tickets_notas_venta nv
              JOIN tickets_notas_venta_tickets nvt ON nv.id_nota_venta::text = nvt.id_nota_venta::text
             WHERE nv.status::text <> 'ca'::text
             GROUP BY nvt.id_ticket) ts
  GROUP BY ts.id_ticket) tts ON tts.id_ticket::text = t.id_ticket::text
  WHERE t.status::text <> 'ca'::text AND tts.total IS NULL
  GROUP BY t.id_cliente, t.id_ticket, t.fecha, t.folio, c.nombre_fiscal, get_clientes_vuelo(tv.id_vuelo, t.id_cliente);

ALTER TABLE get_tickets_pendientes OWNER TO programa;


CREATE OR REPLACE VIEW get_vuelos_pendientes AS 
 SELECT get_clientes_vuelo(v.id_vuelo, NULL::character varying) AS clientes, pi.nombre AS piloto, a.matricula, date(v.fecha) AS fecha, v.id_piloto, v.id_avion, count(*) AS total_vuelos, vc.id_cliente
   FROM vuelos v
   JOIN vuelos_clientes vc ON vc.id_vuelo::text = v.id_vuelo::text
   JOIN proveedores pi ON v.id_piloto::text = pi.id_proveedor::text
   JOIN aviones a ON v.id_avion::text = a.id_avion::text
   LEFT JOIN ( SELECT tv.id_vuelo
   FROM tickets_vuelos tv
   JOIN tickets t ON tv.id_ticket::text = t.id_ticket::text
  WHERE t.status::text <> 'ca'::text
  GROUP BY tv.id_vuelo) tva ON tva.id_vuelo::text = v.id_vuelo::text
  WHERE pi.status::text = 'ac'::text AND a.status::text = 'ac'::text AND tva.id_vuelo IS NULL
  GROUP BY tva.id_vuelo, get_clientes_vuelo(v.id_vuelo, NULL::character varying), pi.nombre, a.matricula, date(v.fecha), v.id_piloto, v.id_avion, vc.id_cliente
  ORDER BY count(*) DESC;

ALTER TABLE get_vuelos_pendientes OWNER TO programa;


CREATE OR REPLACE VIEW get_vuelos_piloto_pendientes AS 
 SELECT v.id_vuelo, get_clientes_vuelo(v.id_vuelo, NULL::character varying) AS clientes, pi.nombre AS piloto, a.matricula, date(v.fecha) AS fecha, v.id_piloto, v.id_avion, count(*) AS total_vuelos
   FROM vuelos v
   JOIN proveedores pi ON v.id_piloto::text = pi.id_proveedor::text
   JOIN aviones a ON v.id_avion::text = a.id_avion::text
   LEFT JOIN ( SELECT cgv.id_vuelo, count(cgv.id_vuelo) AS aux
   FROM compras_gastos_vuelos cgv
   JOIN compras c ON cgv.id_compra::text = c.id_compra::text
  WHERE c.status::text <> 'ca'::text
  GROUP BY cgv.id_vuelo) tt ON tt.id_vuelo::text = v.id_vuelo::text
  WHERE pi.status::text = 'ac'::text AND a.status::text = 'ac'::text AND tt.aux IS NULL
  GROUP BY get_clientes_vuelo(v.id_vuelo, NULL::character varying), pi.nombre, a.matricula, date(v.fecha), v.id_piloto, v.id_avion, v.id_vuelo
  ORDER BY date(v.fecha) DESC;

ALTER TABLE get_vuelos_piloto_pendientes OWNER TO programa;



CREATE TRIGGER insert_alerta
  AFTER INSERT
  ON compras
  FOR EACH ROW
  EXECUTE PROCEDURE insert_alertas_compras();

CREATE TRIGGER update_alerta
  AFTER UPDATE
  ON compras
  FOR EACH ROW
  EXECUTE PROCEDURE update_alertas_compras();

CREATE TRIGGER insert_alerta
  AFTER INSERT
  ON facturacion
  FOR EACH ROW
  EXECUTE PROCEDURE insert_alertas_facturas();

CREATE TRIGGER update_alerta
  AFTER UPDATE
  ON facturacion
  FOR EACH ROW
  EXECUTE PROCEDURE update_alertas_facturas();

CREATE TRIGGER update_alerta
  AFTER INSERT
  ON facturacion_tickets
  FOR EACH ROW
  EXECUTE PROCEDURE update_alertas_facturas_tickets();

CREATE TRIGGER insert_alerta
  AFTER INSERT
  ON tickets
  FOR EACH ROW
  EXECUTE PROCEDURE insert_alertas_tickets();

CREATE TRIGGER update_alerta
  AFTER UPDATE
  ON tickets
  FOR EACH ROW
  EXECUTE PROCEDURE update_alertas_tickets();


INSERT INTO "privilegios" ("id_privilegio", "nombre", "id_padre", "mostrar_menu", "url_accion", "url_icono", "target_blank") VALUES ('l5058a50a4542e6.27948034', 'Cobranza', 'l4fe231e0780ef5.09533640', 'f', 'alertas/cobranza/', '', 'f');
INSERT INTO "privilegios" ("id_privilegio", "nombre", "id_padre", "mostrar_menu", "url_accion", "url_icono", "target_blank") VALUES ('l5058a515aac0e0.98235896', 'Cuentas por pagar', 'l4fe231e0780ef5.09533640', 'f', 'alertas/cuentas_pagar/', '', 'f');