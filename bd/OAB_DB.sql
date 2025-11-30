--
-- PostgreSQL database dump
--

\restrict O6N9UMbamusP1uAbau8b3J9qbPJTOLrzHXixGP7q82wYGVpZZMW19peEKTYYrV0

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-11-30 13:18:48

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 229 (class 1255 OID 18939)
-- Name: actualizarcategoria(integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

/* FUN-BD-01 actualizarCategoria
        Permite actualizar una categoria ya existente, ingresando la id afectada, el nombre, y la descripción

        Devuelve: Nada*/

CREATE FUNCTION public.actualizarcategoria(p_id integer, p_nombre character varying, p_descripcion character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE categoria
    SET nombre = p_nombre,
        descripcion = p_descripcion
    WHERE idCategoria = p_id;
END;
$$;


ALTER FUNCTION public.actualizarcategoria(p_id integer, p_nombre character varying, p_descripcion character varying) OWNER TO postgres;


/* FUN-BD-02 actualizarDatosUsuario
        Permite actualizar un usuario, en base a una id de usuario, modificando el nombre, contraseña y rol

        Devuelve: Nada */
CREATE FUNCTION public.actualizardatosusuario(p_usuario character varying, p_nombre character varying, p_contrasena character varying, p_rol character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
    filas_actualizadas INTEGER;
BEGIN
    UPDATE Usuario
    SET nombre = p_nombre,
        contrasena = p_contrasena,
        rol = p_rol
    WHERE usuario = p_usuario;

	GET DIAGNOSTICS filas_actualizadas = ROW_COUNT;

    RETURN filas_actualizadas > 0;
	
END;
$$;


ALTER FUNCTION public.actualizardatosusuario(p_usuario character varying, p_nombre character varying, p_contrasena character varying, p_rol character varying) OWNER TO postgres;

/* FUN-BD-03 calcularOcurrenciasHastaFinAnio
        Calcula cuántas veces ocurrirá un evento recurrente entre una fecha de referencia y
        el fin del año , según su periodicidad.
        
        Devuelve: Número total de ocurrencias */

CREATE FUNCTION public.calcular_ocurrencias_hasta_fin_anio(p_fecha_inicio date, p_periodo integer, p_fecha_referencia date DEFAULT CURRENT_DATE) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    fecha_fin_anio DATE;
    fecha_actual DATE;
    contador INTEGER := 0;
    dias_disponibles INTEGER;
BEGIN
    /* Calcular el fin del año actual */
    fecha_fin_anio := DATE_TRUNC('year', p_fecha_referencia)::DATE + INTERVAL '1 year' - INTERVAL '1 day';
    
    /* Si el concepto empieza después del fin del año, retornar */
    IF p_fecha_inicio > fecha_fin_anio THEN
        RETURN 0;
    END IF;
    
    /* Solo contar 1 vez a los eventuales */
    IF p_periodo = 0 THEN
        IF p_fecha_inicio >= p_fecha_referencia AND p_fecha_inicio <= fecha_fin_anio THEN
            RETURN 1;
        ELSE
            RETURN 0;
        END IF;
    END IF;
    
    /* Desde donde empezamos a contar */
    IF p_fecha_inicio > p_fecha_referencia THEN
        fecha_actual := p_fecha_inicio;
    ELSE
        /* Proxima ocurrencia */
        fecha_actual := calcular_proxima_facturacion(p_fecha_inicio, p_fecha_referencia, p_periodo);
    END IF;
    
    /* Si la próxima ocurrencia es después del fin del año, retornar 0 */
    IF fecha_actual IS NULL OR fecha_actual > fecha_fin_anio THEN
        RETURN 0;
    END IF;
    
    
    dias_disponibles := fecha_fin_anio - fecha_actual;
    
    contador := FLOOR(dias_disponibles::NUMERIC / p_periodo) + 1;
    IF contador < 0 THEN
        contador := 0;
    END IF;
    
    RETURN contador;
END;
$$;


ALTER FUNCTION public.calcular_ocurrencias_hasta_fin_anio(p_fecha_inicio date, p_periodo integer, p_fecha_referencia date) OWNER TO postgres;



/* FUN-BD-04 calcularProximaFacturacion
        Calcula la próxima fecha de facturacion de un concepto a partir de su fecha de inicio, y la fecha de referencia
        y su periodicidad.
        
        Devuelve: La proxima fecha de facturación*/

CREATE FUNCTION public.calcular_proxima_facturacion(fecha_inicio date, fecha_referencia date, periodo integer) RETURNS date
    LANGUAGE plpgsql
    AS $$
DECLARE
    proxima_fecha DATE;
    dias_transcurridos INTEGER;
    ciclos_completos INTEGER;
BEGIN
    /* Si el período es 0 (Eventual), solo ocurre una vez en la fecha de inicio */
    IF periodo = 0 THEN
        IF fecha_inicio >= fecha_referencia THEN
            RETURN fecha_inicio;
        ELSE
            RETURN NULL;  /* Ya pasó, no hay próxima fecha */
        END IF;
    END IF;
    
    /* Inicio luego de referencia */
    IF fecha_inicio > fecha_referencia THEN
        RETURN fecha_inicio;
    END IF;
    
    dias_transcurridos := fecha_referencia - fecha_inicio;
    
    /* Calcular cuántos ciclos completos han pasado */
    ciclos_completos := FLOOR(dias_transcurridos::NUMERIC / periodo);
    
    /* Calcular la próxima fecha sumando un ciclo más */
    proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * periodo);
    
    RETURN proxima_fecha;
END;
$$;


ALTER FUNCTION public.calcular_proxima_facturacion(fecha_inicio date, fecha_referencia date, periodo integer) OWNER TO postgres;



/* FUN-BD-05 consultarExistenciaUsuario
        Consulta la existencia de un usuario
        
        Devuelve: Un booleano que indica si existe o no */

CREATE FUNCTION public.consultarexistenciausuario(p_usuario character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_existe BOOLEAN;
BEGIN
    SELECT EXISTS(
        SELECT 1 FROM Usuario WHERE usuario = p_usuario
    ) INTO v_existe;

    RETURN v_existe;
END;
$$;


ALTER FUNCTION public.consultarexistenciausuario(p_usuario character varying) OWNER TO postgres;

/* FUN-BD-06 crearCategoria
        Crea una nueva categoria para una familia especifica, dando como parametros:
        el nombre, la descripción y el usuario el que lo creo
        
        Devuelve: Nada */

CREATE FUNCTION public.crearcategoria(p_nombre character varying, p_descripcion character varying, p_familia_id integer, p_usuario_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO categoria (nombre, descripcion, idFamilia, idUsuario)
    VALUES (p_nombre, p_descripcion, p_familia_id, p_usuario_id);
END;
$$;


ALTER FUNCTION public.crearcategoria(p_nombre character varying, p_descripcion character varying, p_familia_id integer, p_usuario_id integer) OWNER TO postgres;

/* FUN-BD-07 crearConcepto
        Crea un nuevo concepto para una familia especifica. dando como parametros:
        el nombre, el tipo, el monto, el periodo, periodicidad, periodo,
        id del usuario que lo creo, id de la categoria a la que pertenece
        
        Devuelve: Nada */
CREATE FUNCTION public.crearconcepto(p_nombre character varying, p_tipo character varying, p_periodo integer, p_fecha_inicio date, p_familia_id integer, p_usuario_id integer, p_categoria_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO Concepto (
        nombre, 
        tipo, 
        periodo, 
        fechaInicio, 
        idFamilia,
        idUsuario,
        idCategoria
    )
    VALUES (
       p_nombre,
       p_tipo,
       p_periodo,
       p_fecha_inicio,
       p_familia_id,
       p_usuario_id,
       p_categoria_id
    );
END;
$$;


ALTER FUNCTION public.crearconcepto(p_nombre character varying, p_tipo character varying, p_periodo integer, p_fecha_inicio date, p_familia_id integer, p_usuario_id integer, p_categoria_id integer) OWNER TO postgres;

/* FUN-BD-08 crearFamilia   
        Crea una nueva familia en el sistema, dando como parametros:
        EL codigo familiar y el nombre de la familia
        
        Devuelve: Nada */
CREATE FUNCTION public.crearfamilia(p_nombre_familia character varying, p_codigo_familiar character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_nuevo_id INT;
BEGIN
    INSERT INTO familia(codigoFamilia, nombreFamilia)
    VALUES (p_nombre_familia , p_codigo_familiar )
    RETURNING idFamilia INTO v_nuevo_id;
    
    RETURN v_nuevo_id;
END;
$$;


ALTER FUNCTION public.crearfamilia(p_nombre_familia character varying, p_codigo_familiar character varying) OWNER TO postgres;

--
-- TOC entry 267 (class 1255 OID 19289)
-- Name: creartransaccion(date, numeric, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--



/* FUN-BD-09 crearUsuario
        Crea un nuevo usuario para una familia en especifico, dando como parametros:
        El nombre de usuario, el nombre, la contraseña, rol y la id de la familia
        
        Devuelve: Nada */
CREATE FUNCTION public.crearusuario(p_usuario character varying, p_nombre character varying, p_contrasena character varying, p_rol character varying, p_familia_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO Usuario (usuario, nombre, contrasena, rol, idFamilia)
    VALUES (p_usuario, p_nombre, p_contrasena, p_rol, p_familia_id);
END;
$$;


ALTER FUNCTION public.crearusuario(p_usuario character varying, p_nombre character varying, p_contrasena character varying, p_rol character varying, p_familia_id integer) OWNER TO postgres;



--
-- TOC entry 275 (class 1255 OID 19310)
-- Name: editar_transaccion(integer, date, numeric, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--



ALTER FUNCTION public.editar_transaccion(p_idtransaccion integer, p_fecha date, p_monto numeric, p_tipo character varying, p_idfamilia integer, p_idconcepto integer, p_idusuario integer) OWNER TO postgres;

/* FUN-BD-10 editarConcepto
        Editar un concepto en base a una ID, especificando:
        El nombre, el tipo, el monto, el periodo, periodicidad, periodo,
        id de la categoria a la que pertenece
        
        Devuelve: Nada */

CREATE FUNCTION public.editarconcepto(p_id_concepto integer, p_nombre character varying, p_tipo character varying, p_periodo integer, p_fecha_inicio date, p_id_categoria integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE Concepto
    SET 
        nombre = p_nombre,
        tipo = p_tipo,
        periodo = p_periodo,
        fechaInicio = p_fecha_inicio,
        idCategoria = p_id_categoria
    WHERE idConcepto = p_id_concepto;
END;
$$;


ALTER FUNCTION public.editarconcepto(p_id_concepto integer, p_nombre character varying, p_tipo character varying, p_periodo integer, p_fecha_inicio date, p_id_categoria integer) OWNER TO postgres;

/* FUN-BD-11 editarestadocategoria
        Permite modificar el estado (habilitado/deshabilitado) de una categoría,
        recibiendo como parámetros la ID de la categoría y el nuevo estado.
        
        Devuelve: Nada */
CREATE FUNCTION public.editarestadocategoria(p_id_categoria integer, p_estado boolean) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE Categoria
    SET estado = p_estado
    WHERE idCategoria = p_id_categoria;
END;
$$;


ALTER FUNCTION public.editarestadocategoria(p_id_categoria integer, p_estado boolean) OWNER TO postgres;

/* FUN-BD-12 editarestadoconcepto
        Permite modificar el estado de un concepto, habilitándolo
        o deshabilitándolo según el parámetro proporcionado.
        
        Devuelve: Nada */
CREATE FUNCTION public.editarestadoconcepto(p_id_concepto integer, p_estado boolean) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE Concepto
    SET estado = p_estado
    WHERE idConcepto = p_id_concepto;
END;
$$;


ALTER FUNCTION public.editarestadoconcepto(p_id_concepto integer, p_estado boolean) OWNER TO postgres;

/* FUN-BD-13 editarestadousuario
        Actualiza el estado de un usuario en base a su ID,
        aplicando el nuevo estado recibido como parámetro.
        
        Devuelve: Nada */
CREATE FUNCTION public.editarestadousuario(p_id integer, p_nuevo_estado boolean) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE Usuario
    SET estado = p_nuevo_estado
    WHERE idusuario = p_id;
END;
$$;


ALTER FUNCTION public.editarestadousuario(p_id integer, p_nuevo_estado boolean) OWNER TO postgres;

/* FUN-BD-14 existecontrasenafamiliar
        Verifica si existe una familia registrada con el código proporcionado
        y cuyo estado esté habilitado
        
        Devuelve: Booleano indicando si la contraseña familiar existe y está activa */
CREATE FUNCTION public.existecontrasenafamiliar(p_contrasena_familiar character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_existe BOOLEAN;
BEGIN
    SELECT EXISTS(
        SELECT 1 
        FROM Familia 
        WHERE codigoFamilia = p_contrasena_familiar
        AND estado = TRUE
    ) INTO v_existe;
    
    RETURN v_existe;
END;
$$;


ALTER FUNCTION public.existecontrasenafamiliar(p_contrasena_familiar character varying) OWNER TO postgres;


/* FUN-BD-14 crearTransaccion
        Crea una nueva transaccion en el sistema
        
        Devuelve: La nueva id de la transaccion creada */

CREATE FUNCTION public.creartransaccion(p_fecha date, p_monto numeric, p_tipo character varying, p_idfamilia integer, p_idconcepto integer, p_idusuario integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    new_id INTEGER;
BEGIN
    INSERT INTO transaccion(fecha, monto, tipo, idfamilia, idconcepto, idusuario)
    VALUES (p_fecha, p_monto, p_tipo, p_idfamilia, p_idconcepto, p_idusuario)
    RETURNING idtransaccion INTO new_id;

    RETURN new_id;
END;
$$;


ALTER FUNCTION public.creartransaccion(p_fecha date, p_monto numeric, p_tipo character varying, p_idfamilia integer, p_idconcepto integer, p_idusuario integer) OWNER TO postgres;

/* FUN-BD-16 editar_transaccion
        EDita una transaccion existente en basea su id
        
        Devuelve: Nada */

CREATE FUNCTION public.editar_transaccion(p_idtransaccion integer, p_fecha date, p_monto numeric, p_tipo character varying, p_idfamilia integer, p_idconcepto integer, p_idusuario integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE transaccion
    SET
        fecha      = p_fecha,
        monto      = p_monto,
        tipo       = p_tipo,
        idFamilia  = p_idFamilia,
        idConcepto = p_idConcepto,
        idUsuario  = p_idUsuario
    WHERE idTransaccion = p_idTransaccion;
END;
$$;



/* FUN-BD-17 hallar_proyeccion_egresos
        Calcula la proyección total de egresos que ocurrirán desde la fecha
        de referencia hasta fin de año, de una familia
        
        Devuelve: Monto total de egresos esperados */

CREATE FUNCTION public.hallar_proyeccion_egresos(p_idfamilia integer, p_fecha_referencia date DEFAULT CURRENT_DATE) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    total_egresos NUMERIC(10,2) := 0;
    rec RECORD;
    ocurrencias INTEGER;
    monto_promedio NUMERIC(10,2);
BEGIN
    /* Iterar sobre todos los conceptos de tipo Egreso activos */
    FOR rec IN 
        SELECT 
            idConcepto,
            nombre,
            periodo,
            fechaInicio
        FROM concepto
        WHERE 
            idFamilia = p_idFamilia
            AND tipo = 'Egreso'
            AND estado = TRUE
    LOOP
        /* Obtener el monto promedio de las transacciones históricas */
        monto_promedio := obtener_monto_promedio_concepto(rec.idConcepto);
        
        /* Si no hay historial, ignorar este concepto */
        IF monto_promedio > 0 THEN
            /* Calcular cuántas veces ocurrirá este egreso hasta fin de año */
            ocurrencias := calcular_ocurrencias_hasta_fin_anio(
                rec.fechaInicio,
                rec.periodo,
                p_fecha_referencia
            );
            
            total_egresos := total_egresos + (monto_promedio * ocurrencias);
        END IF;
    END LOOP;
    
    RETURN total_egresos;
END;
$$;


ALTER FUNCTION public.hallar_proyeccion_egresos(p_idfamilia integer, p_fecha_referencia date) OWNER TO postgres;




/* FUN-BD-18 hallarProyeccionIngresos
        Calcula la proyección total de ingresos que ocurrirán desde la fecha
        de referencia hasta fin de año, de una familia
        
        Devuelve: Monto total de ingresos esperados */

CREATE FUNCTION public.hallar_proyeccion_ingresos(p_idfamilia integer, p_fecha_referencia date DEFAULT CURRENT_DATE) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    total_ingresos NUMERIC(10,2) := 0;
    rec RECORD;
    ocurrencias INTEGER;
    monto_promedio NUMERIC(10,2);
BEGIN
    /* Iterar sobre todos los conceptos de tipo Ingreso activos */
    FOR rec IN 
        SELECT 
            idConcepto,
            nombre,
            periodo,
            fechaInicio
        FROM concepto
        WHERE 
            idFamilia = p_idFamilia
            AND tipo = 'Ingreso'
            AND estado = TRUE
    LOOP
        /* Obtener el monto promedio de las transacciones históricas */
        monto_promedio := obtener_monto_promedio_concepto(rec.idConcepto);
        
        /* Si no hay historial, ignorar este concepto */
        IF monto_promedio > 0 THEN
            /* Calcular cuántas veces ocurrirá este ingreso hasta fin de año */
            ocurrencias := calcular_ocurrencias_hasta_fin_anio(
                rec.fechaInicio,
                rec.periodo,
                p_fecha_referencia
            );
            
            total_ingresos := total_ingresos + (monto_promedio * ocurrencias);
        END IF;
    END LOOP;
    
    RETURN total_ingresos;
END;
$$;


ALTER FUNCTION public.hallar_proyeccion_ingresos(p_idfamilia integer, p_fecha_referencia date) OWNER TO postgres;




/* FUN-BD-19 obtenerbalance
        Calcula el balance neto para una familia específica dentro de un rango
        de fechas
        
        Devuelve: Balance resultante */
CREATE FUNCTION public.obtenerbalance(idfamilia integer, fecha_inicio date, fecha_fin date) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
    total_ingresos DECIMAL(10,2);
    total_egresos DECIMAL(10,2);
    balance DECIMAL(10,2);
BEGIN
    total_ingresos := 0;
    total_egresos := 0;

    /* SUma de ingresos */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_ingresos
    FROM transaccion t
    WHERE t.tipo = 'Ingreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idFamilia = $1;  -- Hacemos referencia explícita a la columna t.idFamilia

    /* Suma de egresos */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_egresos
    FROM transaccion t
    WHERE t.tipo = 'Egreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idFamilia = $1;

    
    /* Balance */
    balance := total_ingresos - total_egresos;

    RETURN balance;
END;
$_$;


ALTER FUNCTION public.obtenerbalance(idfamilia integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;


/* FUN-BD-20 obtenerCategoriaPorId
        Obtiene una categoría en base a su ID
        
        Devuelve: Una categoría valida */

CREATE FUNCTION public.obtenercategoriaporid(p_idcategoria integer) RETURNS TABLE(idcategoria integer, nombre character varying, descripcion character varying, idusuario integer, estado character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.idCategoria,
        c.nombre,
        c.descripcion,
        c.idUsuario,
        (CASE WHEN c.estado THEN 'Habilitado' ELSE 'Deshabilitado' END)::VARCHAR(20) AS estado
    FROM categoria c
    WHERE c.idCategoria = p_idCategoria;
END;
$$;


ALTER FUNCTION public.obtenercategoriaporid(p_idcategoria integer) OWNER TO postgres;

/* FUN-BD-21 obtenerCategorias
        Lista todas las categorías de una familia,
        
        Devuelve: Las categorías de una familia */
CREATE FUNCTION public.obtenercategorias(p_familia_id integer) RETURNS TABLE(idcategoria integer, nombre character varying, descripcion character varying, idusuario integer, estado character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.idCategoria,
        c.nombre,
        c.descripcion,
        c.idUsuario,
        (CASE WHEN c.estado THEN 'Habilitado' ELSE 'Deshabilitado' END)::VARCHAR(20) AS estado
    FROM categoria c
    WHERE c.idFamilia = p_familia_id
    ORDER BY c.idCategoria;
END;
$$;


ALTER FUNCTION public.obtenercategorias(p_familia_id integer) OWNER TO postgres;


/* FUN-BD-22 obtenerConceptoPorId
        Devuelve un concepto específico en base a su ID
        
        Devuelve: Concepto especifico por ID */
CREATE FUNCTION public.obtenerconceptoporid(p_concepto_id integer) RETURNS TABLE(id_concepto integer, nombre character varying, tipo character varying, categoria_id integer, usuario_id integer, periodo integer, estado boolean, fechainicio date)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.idConcepto,
        c.nombre,
        c.tipo,
        c.idCategoria,
        c.idUsuario,
        c.periodo,
        c.estado,
        c.fechainicio
    FROM Concepto c
    WHERE c.idConcepto = p_concepto_id
    LIMIT 1;
END;
$$;


ALTER FUNCTION public.obtenerconceptoporid(p_concepto_id integer) OWNER TO postgres;

/* FUN-BD-23 obtenerConceptos
        Obtiene todos los conceptos registrados en una familia,
        
        Devuelve: Los conceptos de una familia */
CREATE FUNCTION public.obtenerconceptos(p_familia_id integer) RETURNS TABLE(id_concepto integer, nombre character varying, tipo character varying, categoria_id integer, usuario_id integer, periodo integer, estado boolean)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.idConcepto,
        c.nombre,
        c.tipo,
        c.idCategoria,
        c.idUsuario,
        c.periodo,
        c.estado
    FROM Concepto c WHERE c.idFamilia = p_familia_id
    ORDER BY c.idConcepto DESC;
END;
$$;


ALTER FUNCTION public.obtenerconceptos(p_familia_id integer) OWNER TO postgres;

/* FUN-BD-24 obtenerConceptosPorFecha
        Obtiene informacion de los conceptos activos,
        de la siguiente fecha de facturacion partiendo de la fecha actual
        
        Devuelve: Los conceptos, su próxima facturación, los dias restantes */

CREATE FUNCTION public.obtenerconceptosporfecha(p_fecha date, p_idfamilia integer) RETURNS TABLE(tipo character varying, categoria character varying, nombre character varying, monto_promedio numeric, proxima_fecha date, dias_restantes integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.tipo,
        cat.nombre AS categoria,
        c.nombre,
        
        /* Obtener el monto promedio de las transacciones históricas */
        obtener_monto_promedio_concepto(c.idConcepto) AS monto_promedio,
        /* Calcular la próxima fecha de facturación */
        calcular_proxima_facturacion(c.fechaInicio, p_fecha, c.periodo) AS proxima_fecha,
        /* Calcular días restantes */
        (calcular_proxima_facturacion(c.fechaInicio, p_fecha, c.periodo) - p_fecha)::INTEGER AS dias_restantes
    FROM concepto c
    INNER JOIN categoria cat ON c.idCategoria = cat.idCategoria
    WHERE 
        c.idFamilia = p_idFamilia
        AND c.estado = TRUE
        AND c.periodo > 0 /* No contar eventuales */
        AND calcular_proxima_facturacion(c.fechaInicio, p_fecha, c.periodo) IS NOT NULL
    ORDER BY proxima_fecha ASC;
END;
$$;


ALTER FUNCTION public.obtenerconceptosporfecha(p_fecha date, p_idfamilia integer) OWNER TO postgres;



/* FUN-BD-25 obtenerEgreso
        Calcula el total de egresos de una familia dentro de un rango de fechas
        
        Devuelve: Monto de egresos */
CREATE FUNCTION public.obteneregreso(idfamilia integer, fecha_inicio date, fecha_fin date) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
    total_egresos DECIMAL(10,2);
BEGIN
    total_egresos := 0;

    /* Suma de egresos en el rango */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_egresos
    FROM transaccion t
    WHERE t.tipo = 'Egreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idFamilia = $1;

    
    RETURN total_egresos;
END;
$_$;


ALTER FUNCTION public.obteneregreso(idfamilia integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;

/* FUN-BD-26 obtenerEgresoPorUsuario
        Calcular los egresos de un usuario dentro del rango de fechas especificado
        
        Devuelve: Egresos del usuario */
CREATE FUNCTION public.obteneregresoporusuario(idusuario integer, fecha_inicio date, fecha_fin date) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
    total_egresos DECIMAL(10,2);
BEGIN
    total_egresos := 0;

    /* Suma de egresos en el rango del usuario especifico */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_egresos
    FROM transaccion t
    WHERE t.tipo = 'Egreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idUsuario = $1;

    RETURN total_egresos;
END;
$_$;


ALTER FUNCTION public.obteneregresoporusuario(idusuario integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;

/* FUN-BD-27 obtenerFamiliaPorCodigo
        Busca y devuelve la ID de una familia utilizando su codigo familiar
        
        Devuelve: ID de la familia*/
CREATE FUNCTION public.obtenerfamiliaporcodigo(p_codigo_familia character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_idFamilia INTEGER;
BEGIN
    
    SELECT idFamilia
    INTO v_idFamilia
    FROM familia
    WHERE codigoFamilia = p_codigo_familia
    LIMIT 1;
    
    RETURN v_idFamilia;
END;
$$;


ALTER FUNCTION public.obtenerfamiliaporcodigo(p_codigo_familia character varying) OWNER TO postgres;

/* FUN-BD-28 obteneringreso
        Calcula el ingreso total registrados por una familia en un
        rango de fechas
        
        Devuelve: Ingresos del usuario */
CREATE FUNCTION public.obteneringreso(idfamilia integer, fecha_inicio date, fecha_fin date) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
    total_ingresos DECIMAL(10,2);
BEGIN
    total_ingresos := 0;

    /* Suma de ingresos en el rango */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_ingresos
    FROM transaccion t
    WHERE t.tipo = 'Ingreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idFamilia = $1;


    RETURN total_ingresos;
END;
$_$;


ALTER FUNCTION public.obteneringreso(idfamilia integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;


/* FUN-BD-29 obtenerIngresoPorUsuario
        Calcula los ingresos de un usuario en un rango de fechas
        
        Devuelve: Ingresos del usuario */

CREATE FUNCTION public.obteneringresoporusuario(idusuario integer, fecha_inicio date, fecha_fin date) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
DECLARE
    total_ingresos DECIMAL(10,2);
BEGIN
    total_ingresos := 0;

    /* Suma de ingresos en el rango del usuario especifico */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_ingresos
    FROM transaccion t
    WHERE t.tipo = 'Ingreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idUsuario = $1;

    RETURN total_ingresos;
END;
$_$;


ALTER FUNCTION public.obteneringresoporusuario(idusuario integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;

/* FUN-BD-30 obtenerTransaccionesPorFamilia
        Devuelve las transacciones de una familia
        
        Devuelve: Transacciones de una familia */
CREATE FUNCTION public.obtenertransaccionesporfamilia(idfamilia integer) RETURNS TABLE(idtransaccion integer, fecha date, monto numeric, tipo character varying, idconcepto integer, idusuario integer)
    LANGUAGE plpgsql
    AS $_$
BEGIN
    RETURN QUERY
    SELECT t.idTransaccion, t.fecha, t.monto, t.tipo, t.idConcepto, t.idUsuario
    FROM transaccion t
    WHERE t.idFamilia = $1
    ORDER BY t.monto DESC;
END;
$_$;


ALTER FUNCTION public.obtenertransaccionesporfamilia(idfamilia integer) OWNER TO postgres;

/* FUN-BD-31 obtenerTransaccionesRango
        Obtenemos las transacciones de una familia en unrago especifico de fechas
        
        Devuelve: Transacciones de familia por rango */

CREATE FUNCTION public.obtenertransaccionesrango(idfamilia integer, fecha_inicio date, fecha_fin date) RETURNS TABLE(idtransaccion integer, fecha date, monto numeric, tipo character varying, idconcepto integer, idusuario integer)
    LANGUAGE plpgsql
    AS $_$
BEGIN
    RETURN QUERY
    SELECT t.idTransaccion, t.fecha, t.monto, t.tipo, t.idConcepto, t.idUsuario
    FROM transaccion t
    WHERE t.idFamilia = $1
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin;
END;
$_$;


ALTER FUNCTION public.obtenertransaccionesrango(idfamilia integer, fecha_inicio date, fecha_fin date) OWNER TO postgres;

/* FUN-BD-32 obtenerUsuario
        Obtiene la información de un usuario según su nombre de usuario
        
        Devuelve: Un usuario */
CREATE FUNCTION public.obtenerusuario(p_usuario character varying) RETURNS TABLE(id_usuario integer, nombre character varying, usuario character varying, contrasena character varying, rol character varying, familia_id integer, nombre_familia character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        u.idUsuario,
        u.nombre,
        u.usuario,
        u.contrasena,
        u.rol,
        u.idFamilia,
        f.nombreFamilia 
    FROM usuario u
    INNER JOIN familia f ON u.idFamilia = f.idFamilia
    WHERE u.usuario = p_usuario
    AND u.estado = TRUE;
END;
$$;


ALTER FUNCTION public.obtenerusuario(p_usuario character varying) OWNER TO postgres;

/* FUN-BD-33 obtenerUsuarios
        Obtiene los usuarios pertenecientes a una familia
        
        Devuelve: Todos los usuarios de una familia */
CREATE FUNCTION public.obtenerusuarios(p_familia_id integer) RETURNS TABLE(id_usuario integer, nombre character varying, usuario character varying, rol character varying, estado character varying, familia_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT
        u.idUsuario,
        u.nombre,
        u.usuario,
        u.rol,
        (CASE WHEN u.estado THEN 'Habilitado' ELSE 'Deshabilitado' END)::VARCHAR(20) AS estado,
        u.idFamilia
    FROM usuario u WHERE u.idFamilia = p_familia_id
    ORDER BY u.idUsuario;
END;
$$;


ALTER FUNCTION public.obtenerusuarios(p_familia_id integer) OWNER TO postgres;


/* FUN-BD-34 validarCredenciales
        Verifica si existen el usuario y contraseña dados
        
        Devuelve: Booleano indicando si existe */
CREATE FUNCTION public.validarcredenciales(p_usuario character varying, p_contrasena character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_valido BOOLEAN;
BEGIN
    SELECT EXISTS(
        SELECT 1 
        FROM Usuario 
        WHERE usuario = p_usuario 
        AND contrasena = p_contrasena
        AND estado = TRUE
    ) INTO v_valido;
    
    RETURN v_valido;
END;
$$;


ALTER FUNCTION public.validarcredenciales(p_usuario character varying, p_contrasena character varying) OWNER TO postgres;


/* FUN-BD-35 obtener_monto_promedio_concepto
        Obtiene el monto promedio por transaccion de un concepto
        
        Devuelve: Monto promedio */

CREATE FUNCTION public.obtener_monto_promedio_concepto(p_id_concepto integer) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    monto_promedio NUMERIC(10,2);
BEGIN
    /* Calcular el promedio de todas las transacciones de este concepto */
    SELECT COALESCE(AVG(monto), 0)::NUMERIC(10,2)
    INTO monto_promedio
    FROM transaccion
    WHERE idConcepto = p_id_concepto;
    
    RETURN monto_promedio;
END;
$$;


ALTER FUNCTION public.obtener_monto_promedio_concepto(p_id_concepto integer) OWNER TO postgres;



/* FUN-BD-35 obtener_transaccion_por_id
        Obtiene una transaccion por la id
        
        Devuelve: Transaccion */

CREATE FUNCTION public.obtener_transaccion_por_id(p_idtransaccion integer) RETURNS TABLE(idtransaccion integer, fecha date, monto numeric, tipo character varying, idfamilia integer, idconcepto integer, idusuario integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        t.idTransaccion,
        t.fecha,
        t.monto,
        t.tipo,
        t.idFamilia,
        t.idConcepto,
        t.idUsuario
    FROM transaccion t
    WHERE t.idTransaccion = p_idTransaccion;
END;
$$;


ALTER FUNCTION public.obtener_transaccion_por_id(p_idtransaccion integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;



/* TABLAS */

CREATE TABLE public.categoria (
    idcategoria integer NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion character varying(255) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    idusuario integer NOT NULL,
    idfamilia integer NOT NULL
);


ALTER TABLE public.categoria OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 18984)
-- Name: categoria_idcategoria_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categoria_idcategoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categoria_idcategoria_seq OWNER TO postgres;

--
-- TOC entry 5112 (class 0 OID 0)
-- Dependencies: 220
-- Name: categoria_idcategoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categoria_idcategoria_seq OWNED BY public.categoria.idcategoria;


--
-- TOC entry 221 (class 1259 OID 18985)
-- Name: concepto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.concepto (
    idconcepto integer NOT NULL,
    nombre character varying(50) NOT NULL,
    tipo character varying(20) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    periodo integer NOT NULL,
    fechainicio date,
    idfamilia integer NOT NULL,
    idusuario integer NOT NULL,
    idcategoria integer NOT NULL
);


ALTER TABLE public.concepto OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 19001)
-- Name: concepto_idconcepto_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.concepto_idconcepto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.concepto_idconcepto_seq OWNER TO postgres;

--
-- TOC entry 5113 (class 0 OID 0)
-- Dependencies: 222
-- Name: concepto_idconcepto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.concepto_idconcepto_seq OWNED BY public.concepto.idconcepto;


--
-- TOC entry 223 (class 1259 OID 19002)
-- Name: familia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.familia (
    idfamilia integer NOT NULL,
    codigofamilia character varying(10) NOT NULL,
    nombrefamilia character varying(50) NOT NULL,
    estado boolean DEFAULT true NOT NULL
);


ALTER TABLE public.familia OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 19010)
-- Name: familia_idfamilia_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.familia_idfamilia_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.familia_idfamilia_seq OWNER TO postgres;

--
-- TOC entry 5114 (class 0 OID 0)
-- Dependencies: 224
-- Name: familia_idfamilia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.familia_idfamilia_seq OWNED BY public.familia.idfamilia;


--
-- TOC entry 225 (class 1259 OID 19011)
-- Name: transaccion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaccion (
    idtransaccion integer NOT NULL,
    fecha date NOT NULL,
    monto numeric(10,2) NOT NULL,
    tipo character varying(20) NOT NULL,
    idfamilia integer NOT NULL,
    idconcepto integer NOT NULL,
    idusuario integer NOT NULL
);


ALTER TABLE public.transaccion OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 19021)
-- Name: transaccion_idtransaccion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transaccion_idtransaccion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transaccion_idtransaccion_seq OWNER TO postgres;

--
-- TOC entry 5115 (class 0 OID 0)
-- Dependencies: 226
-- Name: transaccion_idtransaccion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaccion_idtransaccion_seq OWNED BY public.transaccion.idtransaccion;


--
-- TOC entry 227 (class 1259 OID 19022)
-- Name: usuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario (
    idusuario integer NOT NULL,
    usuario character varying(50) NOT NULL,
    nombre character varying(50) NOT NULL,
    contrasena character varying(32) NOT NULL,
    rol character varying(25) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    idfamilia integer NOT NULL
);


ALTER TABLE public.usuario OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 19033)
-- Name: usuario_idusuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuario_idusuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuario_idusuario_seq OWNER TO postgres;

--
-- TOC entry 5116 (class 0 OID 0)
-- Dependencies: 228
-- Name: usuario_idusuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuario_idusuario_seq OWNED BY public.usuario.idusuario;


--
-- TOC entry 4912 (class 2604 OID 19034)
-- Name: categoria idcategoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria ALTER COLUMN idcategoria SET DEFAULT nextval('public.categoria_idcategoria_seq'::regclass);


--
-- TOC entry 4914 (class 2604 OID 19035)
-- Name: concepto idconcepto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto ALTER COLUMN idconcepto SET DEFAULT nextval('public.concepto_idconcepto_seq'::regclass);


--
-- TOC entry 4916 (class 2604 OID 19036)
-- Name: familia idfamilia; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia ALTER COLUMN idfamilia SET DEFAULT nextval('public.familia_idfamilia_seq'::regclass);


--
-- TOC entry 4918 (class 2604 OID 19037)
-- Name: transaccion idtransaccion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion ALTER COLUMN idtransaccion SET DEFAULT nextval('public.transaccion_idtransaccion_seq'::regclass);


--
-- TOC entry 4919 (class 2604 OID 19038)
-- Name: usuario idusuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario ALTER COLUMN idusuario SET DEFAULT nextval('public.usuario_idusuario_seq'::regclass);


--
-- TOC entry 5091 (class 0 OID 18974)
-- Dependencies: 219
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categoria (idcategoria, nombre, descripcion, estado, idusuario, idfamilia) FROM stdin;
4	Educación	Útiles, matrículas, pensiones y cursos	t	2	1
6	Entretenimiento	Cines, juegos, streaming, salidas	t	2	1
8	Ropa	Compra de prendas de vestir y calzado	t	3	1
9	Mascotas	Comida, veterinario, accesorios para mascotas	t	4	1
7	Ahorros	Dinero destinado a ahorrar, para ser usado en emergencias	t	3	1
11	Videojuegos	Gastos relacionados a la compra de videojuegos.	t	3	1
5	Salud	Medicinas, seguros médicos, consultas	t	2	1
10	Mantenimiento del hogar	Reparaciones, limpieza, productos del hogar	f	4	1
1	Peliculas	Consumo de peliculas	t	1	1
16	Alimentos	Gastos relacionados a la alimentacion	t	1	1
2	Servicios	Pagos de luz, agua, internet, gas, etc.	t	1	1
3	Transporte	Gastos en gasolina, taxis, buses, mantenimiento del vehículo	f	1	1
23	Musica	Gastos relacionados a la musica	t	1	1
24	Hogar	Muebles, electrodomésticos y decoración	t	1	1
25	Impuestos	Tributos municipales, nacionales, vehiculares	t	2	1
26	Tecnología	Celulares, laptops, accesorios, reparaciones	t	3	1
27	Deportes	Gimnasio, implementos deportivos, clases	t	4	1
28	Viajes	Pasajes, hospedaje, tours y seguros de viaje	t	5	1
29	Donaciones	Aportes a ONGs, iglesias o eventos solidarios	t	6	1
30	Bebidas	Café, jugos, bebidas energéticas, etc.	t	1	1
31	Electrónica menor	Audífonos, cargadores, periféricos	t	2	1
32	Suscripciones	Apps, software, servicios mensuales	t	3	1
33	Belleza	Peluquería, cosméticos, tratamientos	t	4	1
34	Regalos	Cumpleaños, aniversarios, festividades	t	5	1
35	Inversiones	Fondos, acciones, criptomonedas	t	6	1
\.


--
-- TOC entry 5093 (class 0 OID 18985)
-- Dependencies: 221
-- Data for Name: concepto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.concepto (idconcepto, nombre, tipo, estado, periodo, fechainicio, idfamilia, idusuario, idcategoria) FROM stdin;
2	Desayuno rápido	Egreso	t	0	\N	1	1	16
3	Botella de agua	Egreso	t	0	\N	1	1	30
4	Aseo personal	Egreso	t	0	\N	1	1	33
5	Útiles escolares	Egreso	t	0	\N	1	1	33
1	Combi	Egreso	t	0	\N	1	1	16
6	Gas	Egreso	t	0	2025-11-30	1	1	2
7	Bus	Egreso	t	0	2025-11-30	1	3	3
8	Dividendos	Ingreso	t	0	2025-11-30	1	3	35
9	Propina	Ingreso	t	0	2025-11-30	1	3	7
10	Sueldo	Ingreso	t	0	2025-11-30	1	3	7
11	Pago del colegio	Egreso	t	30	2025-11-30	1	1	4
12	Netflix	Egreso	t	30	2025-11-30	1	1	6
13	Disney Plus	Egreso	t	30	2025-11-04	1	1	2
14	Medicina de Perro	Egreso	t	30	2025-10-03	1	1	9
\.


--
-- TOC entry 5095 (class 0 OID 19002)
-- Dependencies: 223
-- Data for Name: familia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.familia (idfamilia, codigofamilia, nombrefamilia, estado) FROM stdin;
1	FROD_123	FRODR	t
\.


--
-- TOC entry 5097 (class 0 OID 19011)
-- Dependencies: 225
-- Data for Name: transaccion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transaccion (idtransaccion, fecha, monto, tipo, idfamilia, idconcepto, idusuario) FROM stdin;
1	2025-11-30	8.50	Egreso	1	2	1
6	2025-11-30	60.00	Egreso	1	6	1
7	2025-11-30	1.20	Egreso	1	7	1
2	2025-11-30	12.90	Egreso	1	4	2
3	2025-11-30	1.50	Egreso	1	1	2
4	2025-11-30	2.00	Egreso	1	3	3
5	2025-11-30	35.00	Egreso	1	5	4
10	2025-10-30	1800.00	Ingreso	1	10	3
11	2025-09-30	1800.00	Ingreso	1	10	3
12	2025-11-15	120.50	Ingreso	1	8	3
13	2025-10-15	110.75	Ingreso	1	8	3
14	2025-09-15	130.00	Ingreso	1	8	3
15	2025-11-28	40.00	Ingreso	1	9	3
16	2025-11-25	25.00	Ingreso	1	9	3
17	2025-11-20	20.00	Ingreso	1	9	3
18	2025-11-30	20.00	Ingreso	1	9	3
19	2025-11-30	30.00	Ingreso	1	9	3
20	2025-11-30	30.00	Egreso	1	6	3
21	2025-11-30	1500.00	Ingreso	1	10	3
22	2025-11-30	123.00	Ingreso	1	9	1
23	2025-11-30	100.00	Egreso	1	12	1
24	2025-11-30	30.00	Egreso	1	12	1
9	2025-11-27	1500.00	Egreso	1	4	3
25	2025-02-01	1800.00	Ingreso	1	10	1
26	2025-01-01	1800.00	Ingreso	1	10	2
27	2025-01-01	2000.00	Ingreso	1	10	3
28	2024-12-01	1500.00	Ingreso	1	10	4
29	2024-12-01	1800.00	Ingreso	1	10	5
30	2024-11-30	1700.00	Ingreso	1	10	6
31	2024-11-01	1800.00	Ingreso	1	10	1
32	2024-10-01	1600.00	Ingreso	1	10	2
33	2024-10-01	1950.00	Ingreso	1	10	3
34	2024-10-01	2100.00	Ingreso	1	10	6
35	2025-01-15	120.50	Ingreso	1	9	3
36	2024-12-15	130.00	Ingreso	1	9	1
37	2024-11-15	98.20	Ingreso	1	9	4
38	2024-10-14	115.60	Ingreso	1	9	6
39	2024-09-14	140.90	Ingreso	1	9	2
40	2025-02-03	20.00	Ingreso	1	8	1
41	2025-02-02	30.00	Ingreso	1	8	3
42	2025-02-01	45.00	Ingreso	1	8	5
43	2025-01-29	15.00	Ingreso	1	8	2
44	2025-01-28	25.00	Ingreso	1	8	3
45	2025-01-27	60.00	Ingreso	1	8	6
46	2025-01-15	35.00	Ingreso	1	8	4
47	2025-01-10	22.00	Ingreso	1	8	6
48	2025-01-05	50.00	Ingreso	1	8	1
49	2025-01-03	10.00	Ingreso	1	8	5
50	2025-02-03	3.50	Egreso	1	3	1
51	2025-02-03	1.20	Egreso	1	7	2
52	2025-02-02	5.50	Egreso	1	2	3
53	2025-02-01	4.00	Egreso	1	3	4
54	2025-01-30	8.00	Egreso	1	2	6
55	2025-01-30	1.50	Egreso	1	1	5
56	2025-01-29	2.40	Egreso	1	7	1
57	2025-01-28	6.50	Egreso	1	4	3
58	2025-01-28	3.00	Egreso	1	2	6
59	2025-01-27	14.00	Egreso	1	5	4
60	2025-01-26	1.50	Egreso	1	7	1
61	2025-01-26	2.00	Egreso	1	3	5
62	2025-01-25	7.50	Egreso	1	2	3
63	2025-01-25	12.00	Egreso	1	4	2
64	2025-01-24	1.50	Egreso	1	1	6
65	2025-01-24	9.80	Egreso	1	5	5
66	2025-01-23	3.50	Egreso	1	3	1
67	2025-01-22	18.00	Egreso	1	4	6
68	2025-01-22	1.50	Egreso	1	7	3
69	2025-01-21	4.00	Egreso	1	2	5
70	2025-01-19	8.50	Egreso	1	4	4
71	2025-01-19	3.20	Egreso	1	3	2
72	2025-01-18	11.00	Egreso	1	5	1
73	2025-01-17	6.80	Egreso	1	4	6
74	2025-01-16	2.50	Egreso	1	3	3
75	2025-01-15	1.00	Egreso	1	7	6
76	2025-01-14	4.50	Egreso	1	2	4
77	2025-01-13	3.30	Egreso	1	3	2
78	2025-01-12	8.90	Egreso	1	5	1
79	2025-01-10	1.80	Egreso	1	7	3
80	2025-01-01	250.00	Egreso	1	11	1
81	2025-01-01	44.90	Egreso	1	12	3
82	2025-01-01	28.90	Egreso	1	13	5
83	2024-12-01	44.90	Egreso	1	12	6
84	2024-12-01	28.90	Egreso	1	13	4
85	2025-01-28	5.00	Egreso	1	2	4
86	2025-01-27	2.00	Egreso	1	3	2
87	2025-01-27	1.80	Egreso	1	7	6
88	2025-01-26	45.00	Ingreso	1	8	5
89	2025-01-25	300.00	Egreso	1	11	4
90	2025-01-24	90.00	Ingreso	1	9	2
91	2025-01-23	28.90	Egreso	1	12	6
92	2025-01-21	20.00	Egreso	1	4	5
93	2025-01-20	25.00	Egreso	1	5	4
94	2025-01-19	15.50	Egreso	1	3	2
95	2024-12-29	120.00	Ingreso	1	8	6
96	2024-12-28	44.00	Egreso	1	13	4
97	2024-12-26	59.00	Egreso	1	14	5
98	2024-12-25	12.00	Egreso	1	3	6
99	2024-12-23	90.00	Ingreso	1	9	4
100	2024-12-21	8.60	Egreso	1	2	5
101	2024-12-20	2.50	Egreso	1	7	2
102	2024-12-18	10.00	Egreso	1	1	6
103	2024-12-18	27.90	Egreso	1	12	5
104	2024-12-17	3000.00	Ingreso	1	10	4
105	2024-11-29	6.40	Egreso	1	4	6
106	2024-11-28	15.00	Ingreso	1	8	1
107	2024-11-26	120.00	Ingreso	1	9	4
108	2024-11-24	64.90	Egreso	1	14	6
109	2024-11-22	35.00	Egreso	1	5	3
110	2024-11-21	12.00	Egreso	1	2	2
111	2024-11-19	48.90	Egreso	1	13	4
112	2024-11-18	19.90	Egreso	1	12	5
113	2024-11-15	1.50	Egreso	1	7	1
114	2024-11-11	1780.00	Ingreso	1	10	6
115	2025-10-03	4.50	Egreso	1	2	4
116	2025-10-04	1.80	Egreso	1	7	6
117	2025-10-06	30.00	Ingreso	1	8	2
118	2025-10-07	18.00	Egreso	1	4	5
119	2025-10-09	300.00	Egreso	1	11	4
120	2025-10-11	120.50	Ingreso	1	9	1
121	2025-10-14	2.50	Egreso	1	3	3
122	2025-10-16	60.00	Ingreso	1	8	6
123	2025-10-19	44.90	Egreso	1	12	5
124	2025-10-22	1800.00	Ingreso	1	10	2
125	2025-11-02	12.90	Egreso	1	4	6
126	2025-11-03	2.00	Egreso	1	3	1
127	2025-11-05	150.00	Ingreso	1	9	3
128	2025-11-07	19.90	Egreso	1	13	5
129	2025-11-09	5.50	Egreso	1	2	4
130	2025-11-12	8.00	Egreso	1	3	2
131	2025-11-14	25.00	Ingreso	1	8	6
132	2025-11-17	3200.00	Ingreso	1	10	4
133	2025-11-20	12.00	Egreso	1	5	1
134	2025-11-28	280.00	Egreso	1	14	3
135	2025-12-01	28.90	Egreso	1	12	4
136	2025-12-02	2.20	Egreso	1	7	5
137	2025-12-04	7.50	Egreso	1	2	6
138	2025-12-06	95.00	Ingreso	1	9	2
139	2025-12-09	50.00	Ingreso	1	8	3
140	2025-12-12	38.00	Egreso	1	4	1
141	2025-12-15	2000.00	Ingreso	1	10	5
142	2025-12-18	9.80	Egreso	1	3	4
143	2025-12-21	300.00	Egreso	1	11	6
144	2025-12-27	31.00	Egreso	1	5	2
\.


--
-- TOC entry 5099 (class 0 OID 19022)
-- Dependencies: 227
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario (idusuario, usuario, nombre, contrasena, rol, estado, idfamilia) FROM stdin;
4	ana_rod	Ana Rodriguez	clave123	Familiar	t	1
2	maria_rod	Maria Rodriguez	clave123	Administrador familiar	t	1
6	tere_rod	Teresa Rodriguez	clave123	Familiar	t	1
5	diego_rod	Diego Rodriguez	clave123	Familiar	t	1
1	juan_rod	Juan Rodriguez	clave123	Familiar	t	1
3	jose_rod	Jose Rodriguez	abc	Administrador familiar	t	1
\.


--
-- TOC entry 5117 (class 0 OID 0)
-- Dependencies: 220
-- Name: categoria_idcategoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categoria_idcategoria_seq', 50, false);


--
-- TOC entry 5118 (class 0 OID 0)
-- Dependencies: 222
-- Name: concepto_idconcepto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.concepto_idconcepto_seq', 14, true);


--
-- TOC entry 5119 (class 0 OID 0)
-- Dependencies: 224
-- Name: familia_idfamilia_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.familia_idfamilia_seq', 1, true);


--
-- TOC entry 5120 (class 0 OID 0)
-- Dependencies: 226
-- Name: transaccion_idtransaccion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transaccion_idtransaccion_seq', 144, true);


--
-- TOC entry 5121 (class 0 OID 0)
-- Dependencies: 228
-- Name: usuario_idusuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuario_idusuario_seq', 50, false);


--
-- TOC entry 4922 (class 2606 OID 19040)
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (idcategoria);


--
-- TOC entry 4924 (class 2606 OID 19042)
-- Name: concepto concepto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT concepto_pkey PRIMARY KEY (idconcepto);


--
-- TOC entry 4926 (class 2606 OID 19044)
-- Name: familia familia_codigofamilia_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia
    ADD CONSTRAINT familia_codigofamilia_key UNIQUE (codigofamilia);


--
-- TOC entry 4928 (class 2606 OID 19046)
-- Name: familia familia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia
    ADD CONSTRAINT familia_pkey PRIMARY KEY (idfamilia);


--
-- TOC entry 4930 (class 2606 OID 19048)
-- Name: transaccion transaccion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT transaccion_pkey PRIMARY KEY (idtransaccion);


--
-- TOC entry 4932 (class 2606 OID 19050)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (idusuario);


--
-- TOC entry 4934 (class 2606 OID 19052)
-- Name: usuario usuario_usuario_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_usuario_key UNIQUE (usuario);


--
-- TOC entry 4937 (class 2606 OID 19053)
-- Name: concepto fk_categoria; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_categoria FOREIGN KEY (idcategoria) REFERENCES public.categoria(idcategoria) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4940 (class 2606 OID 19058)
-- Name: transaccion fk_concepto; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_concepto FOREIGN KEY (idconcepto) REFERENCES public.concepto(idconcepto) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4943 (class 2606 OID 19063)
-- Name: usuario fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4935 (class 2606 OID 19068)
-- Name: categoria fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4938 (class 2606 OID 19073)
-- Name: concepto fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4941 (class 2606 OID 19078)
-- Name: transaccion fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4936 (class 2606 OID 19083)
-- Name: categoria fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4939 (class 2606 OID 19088)
-- Name: concepto fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4942 (class 2606 OID 19093)
-- Name: transaccion fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2025-11-30 13:18:48

--
-- PostgreSQL database dump complete
--

\unrestrict O6N9UMbamusP1uAbau8b3J9qbPJTOLrzHXixGP7q82wYGVpZZMW19peEKTYYrV0

