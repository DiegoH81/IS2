--
-- PostgreSQL database dump
--

\restrict IcaqRL5c43z8pORWyWJ4Yj6rUzXUkT1ca9GetMNiDaahpsrOIE4YBBuUKiGVXC8



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
	
END;
$$;


ALTER FUNCTION public.actualizardatosusuario(p_usuario character varying, p_nombre character varying, p_contrasena character varying, p_rol character varying) OWNER TO postgres;


/* FUN-BD-03 calcularOcurrenciasHastaFinAnio
        Calcula cuántas veces ocurrirá un evento recurrente entre una fecha de referencia y
        el fin del año , según su periodicidad.
        
        Devuelve: Número total de ocurrencias */

CREATE FUNCTION public.calcular_ocurrencias_hasta_fin_anio(p_fecha_inicio date, p_fecha_fin date, p_periodicidad character varying, p_periodo integer, p_fecha_referencia date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    fecha_fin_anio DATE;
    fecha_actual DATE;
    contador INTEGER := 0;
    dias_periodo INTEGER;
BEGIN
    /*  Fin del año actual */

    
    fecha_fin_anio := DATE_TRUNC('year', p_fecha_referencia) + INTERVAL '1 year' - INTERVAL '1 day';
    
    /* Si el concepto acaba antes del año actual */
    IF p_fecha_fin < fecha_fin_anio THEN
        fecha_fin_anio := p_fecha_fin;
    END IF;
    
    /* Si el concepto empieza después del fin del año, retornar 0 */
    IF p_fecha_inicio > fecha_fin_anio THEN
        RETURN 0;
    END IF;
    
    /* Desde donde empezamos a contar */
    IF p_fecha_inicio > p_fecha_referencia THEN
        fecha_actual := p_fecha_inicio;
    ELSE
        fecha_actual := p_fecha_referencia;
    END IF;
    
    /* Dias segun periodicidad */
    CASE p_periodicidad
        WHEN 'Diario' THEN
            dias_periodo := 1;
        WHEN 'Semanal' THEN
            dias_periodo := 7;
        WHEN 'Quincenal' THEN
            dias_periodo := 15;
        WHEN 'Mensual' THEN
            /* Para mensual de manera especial calculamos */
            WHILE fecha_actual <= fecha_fin_anio LOOP
                contador := contador + 1;
                fecha_actual := fecha_actual + INTERVAL '1 month';
            END LOOP;
            RETURN contador;
        WHEN 'Personalizado' THEN
            dias_periodo := p_periodo;
        ELSE
            RETURN 0;
    END CASE;
    
    /* Si no es mensual */
    IF p_periodicidad != 'Mensual' THEN
        contador := FLOOR((fecha_fin_anio - fecha_actual)::NUMERIC / dias_periodo) + 1;
    END IF;
    
    RETURN contador;
END;
$$;


ALTER FUNCTION public.calcular_ocurrencias_hasta_fin_anio(p_fecha_inicio date, p_fecha_fin date, p_periodicidad character varying, p_periodo integer, p_fecha_referencia date) OWNER TO postgres;


/* FUN-BD-04 calcularProximaFacturacion
        Calcula la próxima fecha de facturacion de un concepto a partir de su fecha de inicio, y la fecha de referencia
        y su periodicidad.
        
        Devuelve: La proxima fecha de facturación*/

CREATE FUNCTION public.calcular_proxima_facturacion(fecha_inicio date, fecha_referencia date, periodicidad character varying, periodo integer) RETURNS date
    LANGUAGE plpgsql
    AS $$
DECLARE
    proxima_fecha DATE;
    dias_transcurridos INTEGER;
    ciclos_completos INTEGER;
BEGIN
    /* Si la fecha de inicio es posterior a la fecha de referencia, esa es la próxima */
    IF fecha_inicio > fecha_referencia THEN
        RETURN fecha_inicio;
    END IF;
    
    /* Calcular proxima fecha segun su periodicidad */
    CASE periodicidad
        WHEN 'Diario' THEN
            dias_transcurridos := fecha_referencia - fecha_inicio;
            ciclos_completos := dias_transcurridos / 1;
            proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * INTERVAL '1 day');
        
        WHEN 'Semanal' THEN
            dias_transcurridos := fecha_referencia - fecha_inicio;
            ciclos_completos := dias_transcurridos / 7;
            proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * INTERVAL '7 days');
        
        WHEN 'Quincenal' THEN
            dias_transcurridos := fecha_referencia - fecha_inicio;
            ciclos_completos := dias_transcurridos / 15;
            proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * INTERVAL '15 days');
        
        WHEN 'Mensual' THEN
            ciclos_completos := EXTRACT(YEAR FROM AGE(fecha_referencia, fecha_inicio)) * 12 + 
                               EXTRACT(MONTH FROM AGE(fecha_referencia, fecha_inicio));
            proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * INTERVAL '1 month');
        
        WHEN 'Personalizado' THEN
            dias_transcurridos := fecha_referencia - fecha_inicio;
            ciclos_completos := dias_transcurridos / periodo;
            proxima_fecha := fecha_inicio + ((ciclos_completos + 1) * (periodo || ' days')::INTERVAL);
        
        ELSE
            proxima_fecha := fecha_referencia;
    END CASE;
    
    RETURN proxima_fecha;
END;
$$;


ALTER FUNCTION public.calcular_proxima_facturacion(fecha_inicio date, fecha_referencia date, periodicidad character varying, periodo integer) OWNER TO postgres;


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

CREATE FUNCTION public.crearconcepto(p_nombre character varying, p_tipo character varying, p_monto numeric, p_periodo integer, p_periodicidad character varying, p_fecha_inicio date, p_fecha_fin date, p_familia_id integer, p_usuario_id integer, p_categoria_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO Concepto (
        nombre, 
        tipo, 
        monto, 
        periodo, 
        periodicidad, 
        fechaInicio, 
        fechaFin,
        idFamilia,
        idUsuario,
        idCategoria
    )
    VALUES (
       p_nombre,
       p_tipo,
       p_monto,
       p_periodo,
       p_periodicidad,
       p_fecha_inicio,
       p_fecha_fin,
       p_familia_id,
       p_usuario_id,
       p_categoria_id
    );
END;
$$;


ALTER FUNCTION public.crearconcepto(p_nombre character varying, p_tipo character varying, p_monto numeric, p_periodo integer, p_periodicidad character varying, p_fecha_inicio date, p_fecha_fin date, p_familia_id integer, p_usuario_id integer, p_categoria_id integer) OWNER TO postgres;


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


/* FUN-BD-10 editarConcepto
        Editar un concepto en base a una ID, especificando:
        El nombre, el tipo, el monto, el periodo, periodicidad, periodo,
        id de la categoria a la que pertenece
        
        Devuelve: Nada */
CREATE FUNCTION public.editarconcepto(p_id_concepto integer, p_nombre character varying, p_tipo character varying, p_monto numeric, p_periodo integer, p_periodicidad character varying, p_fecha_inicio date, p_fecha_fin date, p_id_categoria integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE Concepto
    SET 
        nombre = p_nombre,
        tipo = p_tipo,
        monto = p_monto,
        periodo = p_periodo,
        periodicidad = p_periodicidad,
        fechaInicio = p_fecha_inicio,
        fechaFin = p_fecha_fin,
        idCategoria = p_id_categoria
    WHERE idConcepto = p_id_concepto;
END;
$$;


ALTER FUNCTION public.editarconcepto(p_id_concepto integer, p_nombre character varying, p_tipo character varying, p_monto numeric, p_periodo integer, p_periodicidad character varying, p_fecha_inicio date, p_fecha_fin date, p_id_categoria integer) OWNER TO postgres;


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


/* FUN-BD-15 generarTransaccionesPeriodicas
        Genera automáticamente las transacciones correspondientes a todos los
        conceptos activos, con su periodicidad, fecha de inicio
        y fecha fin
        
        Devuelve: Nada */

CREATE FUNCTION public.generar_transacciones_periodicas() RETURNS TABLE(conceptos_procesados integer, transacciones_creadas integer, mensaje text)
    LANGUAGE plpgsql
    AS $$
DECLARE
    rec_concepto RECORD;
    fecha_transaccion DATE;
    contador_conceptos INTEGER := 0;
    contador_transacciones INTEGER := 0;
    dias_periodo INTEGER;
    existe_transaccion BOOLEAN;
BEGIN
    /* Iterando sobre los conceptos */
    FOR rec_concepto IN 
        SELECT 
            idConcepto,
            nombre,
            tipo,
            monto,
            periodo,
            periodicidad,
            fechaInicio,
            fechaFin,
            idFamilia,
            idUsuario
        FROM concepto
        WHERE 
            estado = TRUE
            AND fechaInicio <= CURRENT_DATE
            AND fechaFin >= CURRENT_DATE
    LOOP
        contador_conceptos := contador_conceptos + 1;
        
        /* Transformar periodos a numeros */
        CASE rec_concepto.periodicidad
            WHEN 'Diario' THEN
                dias_periodo := 1;
            WHEN 'Semanal' THEN
                dias_periodo := 7;
            WHEN 'Quincenal' THEN
                dias_periodo := 15;
            WHEN 'Mensual' THEN
                dias_periodo := 30;
            WHEN 'Personalizado' THEN
                dias_periodo := rec_concepto.periodo;
            ELSE
                dias_periodo := rec_concepto.periodo;
        END CASE;
        
        /* Inicio como el inicio del concepto */
        fecha_transaccion := rec_concepto.fechaInicio;


		/* Solo para eventual */
		IF rec_concepto.periodicidad = 'Eventual' THEN
		    
		    /* Verifica existencia */
		    SELECT EXISTS(
		        SELECT 1 
		        FROM transaccion 
		        WHERE idConcepto = rec_concepto.idConcepto 
		        AND fecha = rec_concepto.fechaInicio
		    ) INTO existe_transaccion;
		
		    /* Si no, crearla */
		    IF NOT existe_transaccion THEN
		        INSERT INTO transaccion (
		            fecha,
		            monto,
		            tipo,
		            idFamilia,
		            idConcepto,
		            idUsuario
		        ) VALUES (
		            rec_concepto.fechaInicio,
		            rec_concepto.monto,
		            rec_concepto.tipo,
		            rec_concepto.idFamilia,
		            rec_concepto.idConcepto,
		            rec_concepto.idUsuario
		        );
		
		        contador_transacciones := contador_transacciones + 1;
		    END IF;
		
		    CONTINUE;
		END IF;

		
        /* Generar transacciones hasta el dia de hoy */
        WHILE fecha_transaccion <= CURRENT_DATE LOOP
            
            /* Verifica existencia en esa fecha*/
            SELECT EXISTS(
                SELECT 1 
                FROM transaccion 
                WHERE 
                    idConcepto = rec_concepto.idConcepto 
                    AND fecha = fecha_transaccion
            ) INTO existe_transaccion;
            
            /* Si no, crearla */
            IF NOT existe_transaccion THEN
                INSERT INTO transaccion (
                    fecha,
                    monto,
                    tipo,
                    idFamilia,
                    idConcepto,
                    idUsuario
                ) VALUES (
                    fecha_transaccion,
                    rec_concepto.monto,
                    rec_concepto.tipo,
                    rec_concepto.idFamilia,
                    rec_concepto.idConcepto,
                    rec_concepto.idUsuario
                );
                
                contador_transacciones := contador_transacciones + 1;
            END IF;
            
            /* Siguiemte fecha de facturacion */
            IF rec_concepto.periodicidad = 'Mensual' THEN
                fecha_transaccion := fecha_transaccion + INTERVAL '1 month';
            ELSE
                fecha_transaccion := fecha_transaccion + (dias_periodo || ' days')::INTERVAL;
            END IF;
        END LOOP;
    END LOOP;
    
END;
$$;


ALTER FUNCTION public.generar_transacciones_periodicas() OWNER TO postgres;



/* FUN-BD-16 generarTransaccionesPeriodicasFamilia
        Genera automáticamente las transacciones correspondientes a todos los
        conceptos activos, con su periodicidad, fecha de inicio
        y fecha fin
        
        Devuelve: Nada */

CREATE FUNCTION public.generar_transacciones_periodicas_familia(p_idfamilia integer) RETURNS TABLE(conceptos_procesados integer, transacciones_creadas integer, mensaje text)
    LANGUAGE plpgsql
    AS $$
DECLARE
    rec_concepto RECORD;
    fecha_transaccion DATE;
    contador_conceptos INTEGER := 0;
    contador_transacciones INTEGER := 0;
    dias_periodo INTEGER;
    existe_transaccion BOOLEAN;
BEGIN
    /* Iterando sobre los conceptos */
    FOR rec_concepto IN 
        SELECT 
            idConcepto,
            nombre,
            tipo,
            monto,
            periodo,
            periodicidad,
            fechaInicio,
            fechaFin,
            idFamilia,
            idUsuario
        FROM concepto
        WHERE 
            estado = TRUE
            AND fechaInicio <= CURRENT_DATE
            AND fechaFin >= CURRENT_DATE
            AND idFamilia = p_idFamilia
    LOOP
        contador_conceptos := contador_conceptos + 1;
        
        CASE rec_concepto.periodicidad
            WHEN 'Diario' THEN
                dias_periodo := 1;
            WHEN 'Semanal' THEN
                dias_periodo := 7;
            WHEN 'Quincenal' THEN
                dias_periodo := 15;
            WHEN 'Mensual' THEN
                dias_periodo := 30;
            WHEN 'Personalizado' THEN
                dias_periodo := rec_concepto.periodo;
            ELSE
                dias_periodo := rec_concepto.periodo;
        END CASE;
        
        /* Inicio como el inicio del concepto */
        fecha_transaccion := rec_concepto.fechaInicio;

        /* Solo para eventual */
		IF rec_concepto.periodicidad = 'Eventual' THEN
			
			/* Verificar existencia */
			SELECT EXISTS(
				SELECT 1 
				FROM transaccion 
				WHERE idConcepto = rec_concepto.idConcepto 
				AND fecha = rec_concepto.fechaInicio
			) INTO existe_transaccion;
		
			/* Si no crearla */
			IF NOT existe_transaccion THEN
				INSERT INTO transaccion (
					fecha,
					monto,
					tipo,
					idFamilia,
					idConcepto,
					idUsuario
				) VALUES (
					rec_concepto.fechaInicio,
					rec_concepto.monto,
					rec_concepto.tipo,
					rec_concepto.idFamilia,
					rec_concepto.idConcepto,
					rec_concepto.idUsuario
				);
		
				contador_transacciones := contador_transacciones + 1;
			END IF;
		
			
			CONTINUE;
		END IF;
		
        /* Generar transacciones hasta el dia de hoy */
        WHILE fecha_transaccion <= CURRENT_DATE LOOP

            /* Verifica existencia en esa fecha*/
            SELECT EXISTS(
                SELECT 1 
                FROM transaccion 
                WHERE 
                    idConcepto = rec_concepto.idConcepto 
                    AND fecha = fecha_transaccion
            ) INTO existe_transaccion;
            
            /* Si no crearla */
            IF NOT existe_transaccion THEN
                INSERT INTO transaccion (
                    fecha,
                    monto,
                    tipo,
                    idFamilia,
                    idConcepto,
                    idUsuario
                ) VALUES (
                    fecha_transaccion,
                    rec_concepto.monto,
                    rec_concepto.tipo,
                    rec_concepto.idFamilia,
                    rec_concepto.idConcepto,
                    rec_concepto.idUsuario
                );
                
                contador_transacciones := contador_transacciones + 1;
            END IF;
            
            /* Obtener la siguiente fecha */
            IF rec_concepto.periodicidad = 'Mensual' THEN
                fecha_transaccion := fecha_transaccion + INTERVAL '1 month';
            ELSE
                fecha_transaccion := fecha_transaccion + (dias_periodo || ' days')::INTERVAL;
            END IF;
        END LOOP;
    END LOOP;
    
END;
$$;


ALTER FUNCTION public.generar_transacciones_periodicas_familia(p_idfamilia integer) OWNER TO postgres;


/* FUN-BD-17 hallar_proyeccion_egresos
        Calcula la proyección total de egresos que ocurrirán desde la fecha
        de referencia hasta fin de año, de una familia
        
        Devuelve: Monto total de egresos esperados */

CREATE FUNCTION public.hallar_proyeccion_egresos(p_idfamilia integer, p_fecha_referencia date DEFAULT CURRENT_DATE) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    total_egresos DECIMAL(10,2) := 0;
    rec RECORD;
    ocurrencias INTEGER;
BEGIN
    /* Iterar sobre los egresos activos */
    FOR rec IN 
        SELECT 
            idConcepto,
            nombre,
            monto,
            periodo,
            periodicidad,
            fechaInicio,
            fechaFin
        FROM concepto
        WHERE 
            idFamilia = p_idFamilia
            AND tipo = 'Egreso'
            AND estado = TRUE
            AND fechaFin >= p_fecha_referencia
    LOOP
        /* Hallar las ocurrencias */
        ocurrencias := calcular_ocurrencias_hasta_fin_anio(
            rec.fechaInicio,
            rec.fechaFin,
            rec.periodicidad,
            rec.periodo,
            p_fecha_referencia
        );
        
        /* Multiplicar ocurrencias por el monto y sumarlas al total*/
        total_egresos := total_egresos + (rec.monto * ocurrencias);
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
    total_ingresos DECIMAL(10,2) := 0;
    rec RECORD;
    ocurrencias INTEGER;
BEGIN
    /* Iterar sobre los ingresos activos */
    FOR rec IN 
        SELECT 
            idConcepto,
            nombre,
            monto,
            periodo,
            periodicidad,
            fechaInicio,
            fechaFin
        FROM concepto
        WHERE 
            idFamilia = p_idFamilia
            AND tipo = 'Ingreso'
            AND estado = TRUE
            AND fechaFin >= p_fecha_referencia
    LOOP
        /* Calcular ocurrencias */
        ocurrencias := calcular_ocurrencias_hasta_fin_anio(
            rec.fechaInicio,
            rec.fechaFin,
            rec.periodicidad,
            rec.periodo,
            p_fecha_referencia
        );
        
        /* Multiplicar ocurrencias por el monto y sumarlas al total*/
        total_ingresos := total_ingresos + (rec.monto * ocurrencias);
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

    /* Suma de ingresos */
    SELECT COALESCE(SUM(t.monto), 0) INTO total_ingresos
    FROM transaccion t
    WHERE t.tipo = 'Ingreso'
    AND t.fecha BETWEEN fecha_inicio AND fecha_fin
    AND t.idFamilia = $1;

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

CREATE FUNCTION public.obtenerconceptoporid(p_concepto_id integer) RETURNS TABLE(id_concepto integer, nombre character varying, tipo character varying, categoria_id integer, usuario_id integer, monto numeric, periodo integer, periodicidad character varying, estado boolean, fechainicio date, fechafin date)
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
        c.monto,
        c.periodo,
        c.periodicidad,
        c.estado,
        c.fechainicio,
        c.fechafin
    FROM Concepto c
    WHERE c.idConcepto = p_concepto_id
    LIMIT 1;
END;
$$;


ALTER FUNCTION public.obtenerconceptoporid(p_concepto_id integer) OWNER TO postgres;


/* FUN-BD-23 obtenerconceptos
        Obtiene todos los conceptos registrados en una familia,
        
        Devuelve: Los conceptos de una familia */

CREATE FUNCTION public.obtenerconceptos(p_familia_id integer) RETURNS TABLE(id_concepto integer, nombre character varying, tipo character varying, categoria_id integer, usuario_id integer, monto numeric, periodo integer, periodicidad character varying, estado boolean)
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
        c.monto,
        c.periodo,
        c.periodicidad,
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

CREATE FUNCTION public.obtenerconceptosporfecha(p_fecha date, p_idfamilia integer) RETURNS TABLE(tipo character varying, categoria character varying, nombre character varying, monto numeric, proxima_fecha date, dias_restantes integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.tipo,
        cat.nombre AS categoria,
        c.nombre,
        c.monto,

        /* Proxima facturacion */
        calcular_proxima_facturacion(c.fechaInicio, p_fecha, c.periodicidad, c.periodo) AS proxima_fecha,
        
        /* Dias restantes */
        (calcular_proxima_facturacion(c.fechaInicio, p_fecha, c.periodicidad, c.periodo) - p_fecha)::INTEGER AS dias_restantes
    FROM concepto c
    INNER JOIN categoria cat ON c.idCategoria = cat.idCategoria
    WHERE 
        c.idFamilia = p_idFamilia
        AND c.estado = TRUE
        AND c.fechaFin > p_fecha
    ORDER BY dias_restantes ASC;
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

    /* Suma de egresos en el rango dado */
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

    /* Suma de egresos en el rango dado de un usuario*/
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

    /* Suma de ingresos en el rango dado */
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

    /* Suma de ingresos en el rango dado por un usuario*/
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

SET default_tablespace = '';

SET default_table_access_method = heap;


/*
//////////////////////////////////////////////
//                  TABLAS                 //
/////////////////////////////////////////////
*/



/* Creacion de la tabla de categoría */
CREATE TABLE public.categoria (
    idcategoria integer NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion character varying(255) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    idusuario integer NOT NULL,
    idfamilia integer NOT NULL
);


ALTER TABLE public.categoria OWNER TO postgres;


CREATE SEQUENCE public.categoria_idcategoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categoria_idcategoria_seq OWNER TO postgres;

--
-- TOC entry 5107 (class 0 OID 0)
-- Dependencies: 220
-- Name: categoria_idcategoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categoria_idcategoria_seq OWNED BY public.categoria.idcategoria;


--
-- TOC entry 221 (class 1259 OID 18985)
-- Name: concepto; Type: TABLE; Schema: public; Owner: postgres
--


/* Creacion de la tabla de concepto */

CREATE TABLE public.concepto (
    idconcepto integer NOT NULL,
    nombre character varying(50) NOT NULL,
    tipo character varying(20) NOT NULL,
    monto numeric(10,2) NOT NULL,
    estado boolean DEFAULT true NOT NULL,
    periodo integer NOT NULL,
    periodicidad character varying(20) NOT NULL,
    fechainicio date NOT NULL,
    fechafin date NOT NULL,
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
-- TOC entry 5108 (class 0 OID 0)
-- Dependencies: 222
-- Name: concepto_idconcepto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.concepto_idconcepto_seq OWNED BY public.concepto.idconcepto;


--
-- TOC entry 223 (class 1259 OID 19002)
-- Name: familia; Type: TABLE; Schema: public; Owner: postgres
--


/* Creacion de la tabla de familia */
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
-- TOC entry 5109 (class 0 OID 0)
-- Dependencies: 224
-- Name: familia_idfamilia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.familia_idfamilia_seq OWNED BY public.familia.idfamilia;


--
-- TOC entry 225 (class 1259 OID 19011)
-- Name: transaccion; Type: TABLE; Schema: public; Owner: postgres
--


/* Creacion de la tabla de transacción */
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
-- TOC entry 5110 (class 0 OID 0)
-- Dependencies: 226
-- Name: transaccion_idtransaccion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaccion_idtransaccion_seq OWNED BY public.transaccion.idtransaccion;


--
-- TOC entry 227 (class 1259 OID 19022)
-- Name: usuario; Type: TABLE; Schema: public; Owner: postgres
--


/* Creacion de la tabla de usuario */
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
-- TOC entry 5111 (class 0 OID 0)
-- Dependencies: 228
-- Name: usuario_idusuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuario_idusuario_seq OWNED BY public.usuario.idusuario;


--
-- TOC entry 4913 (class 2604 OID 19034)
-- Name: categoria idcategoria; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria ALTER COLUMN idcategoria SET DEFAULT nextval('public.categoria_idcategoria_seq'::regclass);


--
-- TOC entry 4915 (class 2604 OID 19035)
-- Name: concepto idconcepto; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto ALTER COLUMN idconcepto SET DEFAULT nextval('public.concepto_idconcepto_seq'::regclass);


--
-- TOC entry 4917 (class 2604 OID 19036)
-- Name: familia idfamilia; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia ALTER COLUMN idfamilia SET DEFAULT nextval('public.familia_idfamilia_seq'::regclass);


--
-- TOC entry 4919 (class 2604 OID 19037)
-- Name: transaccion idtransaccion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion ALTER COLUMN idtransaccion SET DEFAULT nextval('public.transaccion_idtransaccion_seq'::regclass);


--
-- TOC entry 4920 (class 2604 OID 19038)
-- Name: usuario idusuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario ALTER COLUMN idusuario SET DEFAULT nextval('public.usuario_idusuario_seq'::regclass);


--
-- TOC entry 5092 (class 0 OID 18974)
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
-- TOC entry 5094 (class 0 OID 18985)
-- Dependencies: 221
-- Data for Name: concepto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.concepto (idconcepto, nombre, tipo, monto, estado, periodo, periodicidad, fechainicio, fechafin, idfamilia, idusuario, idcategoria) FROM stdin;
22	Café diario	Egreso	4.50	t	1	Diario	2025-11-01	2026-01-31	1	1	30
36	Productos cosméticos	Egreso	40.00	t	15	Quincenal	2025-11-01	2026-03-01	1	4	33
41	Snack diario	Egreso	3.50	t	1	Diario	2025-11-01	2026-03-01	1	1	16
42	Botella de agua	Egreso	1.20	t	1	Diario	2025-11-01	2026-01-31	1	2	30
43	Medicamento control diario	Egreso	2.00	t	1	Diario	2025-10-15	2026-02-15	1	3	5
44	Transporte diario al trabajo	Egreso	5.00	t	1	Diario	2025-10-01	2026-03-01	1	4	3
49	Gastos diarios en juegos móviles	Egreso	1.00	t	1	Diario	2025-11-10	2026-01-10	1	3	11
21	Compra semanal de frutas	Egreso	45.00	t	7	Semanal	2025-10-01	2026-02-28	1	1	16
30	Clases de natación	Egreso	50.00	t	7	Semanal	2025-10-10	2026-02-10	1	4	27
9	Pension colegio	Egreso	750.00	t	30	Mensual	2025-11-01	2026-09-25	1	1	4
24	Productos de limpieza mensual	Egreso	35.00	t	30	Mensual	2025-11-01	2026-04-01	1	2	10
221	Reparación de computadoras	Ingreso	75.00	t	0	Eventual	2025-07-17	2025-07-17	1	5	26
28	Compra de nube Google One	Egreso	8.00	t	30	Mensual	2025-11-01	2026-11-01	1	3	32
29	Membresía gimnasio	Egreso	70.00	t	30	Mensual	2025-11-01	2026-04-01	1	4	27
31	Ahorro para viaje anual	Egreso	120.00	t	30	Mensual	2025-11-01	2026-10-01	1	5	28
35	Corte de cabello	Egreso	25.00	t	30	Mensual	2025-11-05	2026-05-05	1	4	33
39	Aporte a fondo mutuo	Egreso	100.00	t	30	Mensual	2025-11-01	2026-11-01	1	6	35
45	Entrenamiento funcional	Egreso	25.00	t	7	Semanal	2025-10-10	2026-04-10	1	4	27
46	Snacks para mascota	Egreso	8.00	t	7	Semanal	2025-10-20	2026-02-20	1	5	9
47	Spotify semanal	Egreso	6.00	t	7	Semanal	2025-11-01	2026-03-01	1	3	32
48	Clases de refuerzo académico	Egreso	40.00	t	7	Semanal	2025-11-05	2026-02-05	1	2	4
50	Compra de pan semanal	Egreso	7.00	t	7	Semanal	2025-11-01	2026-03-01	1	6	24
8	Death Stranding	Egreso	40.00	t	0	Eventual	2025-10-31	2025-10-31	1	1	11
23	Compra de silla ergonómica	Egreso	180.00	t	0	Eventual	2025-11-15	2025-11-15	1	1	24
27	Reparación de laptop	Egreso	150.00	t	0	Eventual	2025-09-20	2025-09-20	1	3	26
32	Reserva de hotel	Egreso	300.00	t	0	Eventual	2025-12-15	2025-12-15	1	5	28
34	Venta de artículos usados	Ingreso	65.00	t	0	Eventual	2025-10-28	2025-10-28	1	3	7
37	Regalo de cumpleaños	Egreso	90.00	t	0	Eventual	2025-12-05	2025-12-05	1	5	34
38	Regalos navideños	Egreso	250.00	t	0	Eventual	2025-12-20	2025-12-20	1	5	34
40	Compra de criptomonedas	Egreso	150.00	t	0	Eventual	2025-10-10	2025-10-10	1	6	35
17	Transporte en taxi	Egreso	12.00	t	0	Eventual	2025-10-26	2025-10-26	1	1	3
26	Arbitrios municipales	Egreso	60.00	t	30	Mensual	2025-07-21	2025-10-22	1	2	25
25	Impuesto vehicular anual	Egreso	420.00	t	0	Eventual	2025-07-18	2025-09-30	1	2	25
240	Premio menor de lotería	Ingreso	10.00	t	0	Eventual	2025-10-15	2025-10-15	1	2	7
211	Venta diaria - Bodega	Ingreso	120.00	t	1	Diario	2025-07-22	2026-12-25	1	6	35
201	Sueldo mensual - María	Ingreso	750.00	t	30	Mensual	2025-07-09	2026-07-04	1	2	24
202	Sueldo mensual - Ana	Ingreso	500.00	t	30	Mensual	2025-07-07	2026-07-04	1	3	24
233	Alquiler de habitación	Ingreso	100.00	t	30	Mensual	2025-07-19	2026-12-04	1	6	24
234	Pago de alquiler de terreno	Ingreso	20.00	t	30	Mensual	2025-07-06	2026-12-06	1	1	24
230	Rendimiento de cuenta de ahorros	Ingreso	50.00	t	30	Mensual	2025-07-18	2026-12-25	1	3	7
222	Clases particulares de matemáticas	Ingreso	100.00	t	7	Semanal	2025-07-27	2026-12-20	1	6	4
212	Delivery de comida - Ganancias	Ingreso	150.00	t	7	Semanal	2025-07-28	2026-12-26	1	1	16
214	Servicios de panadería casera	Ingreso	150.00	t	7	Semanal	2025-07-26	2026-12-20	1	3	16
33	Pago de salario	Ingreso	300.00	t	30	Mensual	2025-11-01	2026-05-01	1	2	24
7	Cyberpunk 2077	Egreso	150.50	t	0	Eventual	2025-10-25	2025-10-31	1	3	6
243	Regalo monetario familiar	Ingreso	25.00	t	0	Eventual	2025-08-05	2025-08-05	1	5	34
244	Reembolso por compras	Ingreso	8.00	t	0	Eventual	2025-09-10	2026-09-10	1	6	8
231	Dividendos bancarios	Ingreso	100.00	t	30	Mensual	2025-07-13	2026-12-20	1	4	25
232	Intereses por préstamo personal	Ingreso	150.00	t	30	Mensual	2025-07-26	2026-12-20	1	5	25
210	Ingresos tienda familiar	Ingreso	400.00	t	30	Mensual	2025-07-07	2026-12-20	1	5	35
213	Venta de ropa por catálogo	Ingreso	200.00	t	30	Mensual	2025-07-08	2026-12-20	1	2	33
203	Sueldo mensual - Teresa	Ingreso	500.00	t	30	Mensual	2025-07-09	2026-12-20	1	4	24
200	Sueldo mensual - Juan	Ingreso	600.00	t	30	Mensual	2025-07-15	2026-12-20	1	1	24
223	Asesoría contable ocasional	Ingreso	75.00	t	0	Eventual	2025-07-02	2025-07-02	1	1	7
224	Venta de muebles usados	Ingreso	100.00	t	0	Eventual	2025-07-09	2025-07-09	1	2	10
241	Bono anual por desempeño	Ingreso	50.00	t	0	Eventual	2025-07-05	2025-07-05	1	3	24
242	Devolución de impuestos	Ingreso	20.00	t	0	Eventual	2025-07-14	2025-07-14	1	4	25
220	Freelance de diseño gráfico	Ingreso	200.00	t	0	Eventual	2025-07-27	2025-07-27	1	4	32
\.


--
-- TOC entry 5096 (class 0 OID 19002)
-- Dependencies: 223
-- Data for Name: familia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.familia (idfamilia, codigofamilia, nombrefamilia, estado) FROM stdin;
1	FROD_123	FRODR	t
\.


--
-- TOC entry 5098 (class 0 OID 19011)
-- Dependencies: 225
-- Data for Name: transaccion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transaccion (idtransaccion, fecha, monto, tipo, idfamilia, idconcepto, idusuario) FROM stdin;
1	2025-11-01	4.50	Egreso	1	22	1
2	2025-11-02	4.50	Egreso	1	22	1
3	2025-11-03	4.50	Egreso	1	22	1
4	2025-11-04	4.50	Egreso	1	22	1
5	2025-11-05	4.50	Egreso	1	22	1
6	2025-11-06	4.50	Egreso	1	22	1
7	2025-11-07	4.50	Egreso	1	22	1
8	2025-11-08	4.50	Egreso	1	22	1
9	2025-11-09	4.50	Egreso	1	22	1
10	2025-11-10	4.50	Egreso	1	22	1
11	2025-11-11	4.50	Egreso	1	22	1
12	2025-11-12	4.50	Egreso	1	22	1
13	2025-11-13	4.50	Egreso	1	22	1
14	2025-11-14	4.50	Egreso	1	22	1
15	2025-11-15	4.50	Egreso	1	22	1
16	2025-11-16	4.50	Egreso	1	22	1
17	2025-11-17	4.50	Egreso	1	22	1
18	2025-11-18	4.50	Egreso	1	22	1
19	2025-11-19	4.50	Egreso	1	22	1
20	2025-11-20	4.50	Egreso	1	22	1
21	2025-11-21	4.50	Egreso	1	22	1
22	2025-11-22	4.50	Egreso	1	22	1
23	2025-11-23	4.50	Egreso	1	22	1
24	2025-11-24	4.50	Egreso	1	22	1
25	2025-11-25	4.50	Egreso	1	22	1
26	2025-11-26	4.50	Egreso	1	22	1
27	2025-11-27	4.50	Egreso	1	22	1
28	2025-11-01	40.00	Egreso	1	36	4
29	2025-11-16	40.00	Egreso	1	36	4
30	2025-11-01	3.50	Egreso	1	41	1
31	2025-11-02	3.50	Egreso	1	41	1
32	2025-11-03	3.50	Egreso	1	41	1
33	2025-11-04	3.50	Egreso	1	41	1
34	2025-11-05	3.50	Egreso	1	41	1
35	2025-11-06	3.50	Egreso	1	41	1
36	2025-11-07	3.50	Egreso	1	41	1
37	2025-11-08	3.50	Egreso	1	41	1
38	2025-11-09	3.50	Egreso	1	41	1
39	2025-11-10	3.50	Egreso	1	41	1
40	2025-11-11	3.50	Egreso	1	41	1
41	2025-11-12	3.50	Egreso	1	41	1
42	2025-11-13	3.50	Egreso	1	41	1
43	2025-11-14	3.50	Egreso	1	41	1
44	2025-11-15	3.50	Egreso	1	41	1
45	2025-11-16	3.50	Egreso	1	41	1
46	2025-11-17	3.50	Egreso	1	41	1
47	2025-11-18	3.50	Egreso	1	41	1
48	2025-11-19	3.50	Egreso	1	41	1
49	2025-11-20	3.50	Egreso	1	41	1
50	2025-11-21	3.50	Egreso	1	41	1
51	2025-11-22	3.50	Egreso	1	41	1
52	2025-11-23	3.50	Egreso	1	41	1
53	2025-11-24	3.50	Egreso	1	41	1
54	2025-11-25	3.50	Egreso	1	41	1
55	2025-11-26	3.50	Egreso	1	41	1
56	2025-11-27	3.50	Egreso	1	41	1
57	2025-11-01	1.20	Egreso	1	42	2
58	2025-11-02	1.20	Egreso	1	42	2
59	2025-11-03	1.20	Egreso	1	42	2
60	2025-11-04	1.20	Egreso	1	42	2
61	2025-11-05	1.20	Egreso	1	42	2
62	2025-11-06	1.20	Egreso	1	42	2
63	2025-11-07	1.20	Egreso	1	42	2
64	2025-11-08	1.20	Egreso	1	42	2
65	2025-11-09	1.20	Egreso	1	42	2
66	2025-11-10	1.20	Egreso	1	42	2
67	2025-11-11	1.20	Egreso	1	42	2
68	2025-11-12	1.20	Egreso	1	42	2
69	2025-11-13	1.20	Egreso	1	42	2
70	2025-11-14	1.20	Egreso	1	42	2
71	2025-11-15	1.20	Egreso	1	42	2
72	2025-11-16	1.20	Egreso	1	42	2
73	2025-11-17	1.20	Egreso	1	42	2
74	2025-11-18	1.20	Egreso	1	42	2
75	2025-11-19	1.20	Egreso	1	42	2
76	2025-11-20	1.20	Egreso	1	42	2
77	2025-11-21	1.20	Egreso	1	42	2
78	2025-11-22	1.20	Egreso	1	42	2
79	2025-11-23	1.20	Egreso	1	42	2
80	2025-11-24	1.20	Egreso	1	42	2
81	2025-11-25	1.20	Egreso	1	42	2
82	2025-11-26	1.20	Egreso	1	42	2
83	2025-11-27	1.20	Egreso	1	42	2
84	2025-10-15	2.00	Egreso	1	43	3
85	2025-10-16	2.00	Egreso	1	43	3
86	2025-10-17	2.00	Egreso	1	43	3
87	2025-10-18	2.00	Egreso	1	43	3
88	2025-10-19	2.00	Egreso	1	43	3
89	2025-10-20	2.00	Egreso	1	43	3
90	2025-10-21	2.00	Egreso	1	43	3
91	2025-10-22	2.00	Egreso	1	43	3
92	2025-10-23	2.00	Egreso	1	43	3
93	2025-10-24	2.00	Egreso	1	43	3
94	2025-10-25	2.00	Egreso	1	43	3
95	2025-10-26	2.00	Egreso	1	43	3
96	2025-10-27	2.00	Egreso	1	43	3
97	2025-10-28	2.00	Egreso	1	43	3
98	2025-10-29	2.00	Egreso	1	43	3
99	2025-10-30	2.00	Egreso	1	43	3
100	2025-10-31	2.00	Egreso	1	43	3
101	2025-11-01	2.00	Egreso	1	43	3
102	2025-11-02	2.00	Egreso	1	43	3
103	2025-11-03	2.00	Egreso	1	43	3
104	2025-11-04	2.00	Egreso	1	43	3
105	2025-11-05	2.00	Egreso	1	43	3
106	2025-11-06	2.00	Egreso	1	43	3
107	2025-11-07	2.00	Egreso	1	43	3
108	2025-11-08	2.00	Egreso	1	43	3
109	2025-11-09	2.00	Egreso	1	43	3
110	2025-11-10	2.00	Egreso	1	43	3
111	2025-11-11	2.00	Egreso	1	43	3
112	2025-11-12	2.00	Egreso	1	43	3
113	2025-11-13	2.00	Egreso	1	43	3
114	2025-11-14	2.00	Egreso	1	43	3
115	2025-11-15	2.00	Egreso	1	43	3
116	2025-11-16	2.00	Egreso	1	43	3
117	2025-11-17	2.00	Egreso	1	43	3
118	2025-11-18	2.00	Egreso	1	43	3
119	2025-11-19	2.00	Egreso	1	43	3
120	2025-11-20	2.00	Egreso	1	43	3
121	2025-11-21	2.00	Egreso	1	43	3
122	2025-11-22	2.00	Egreso	1	43	3
123	2025-11-23	2.00	Egreso	1	43	3
124	2025-11-24	2.00	Egreso	1	43	3
125	2025-11-25	2.00	Egreso	1	43	3
126	2025-11-26	2.00	Egreso	1	43	3
127	2025-11-27	2.00	Egreso	1	43	3
128	2025-10-01	5.00	Egreso	1	44	4
129	2025-10-02	5.00	Egreso	1	44	4
130	2025-10-03	5.00	Egreso	1	44	4
131	2025-10-04	5.00	Egreso	1	44	4
132	2025-10-05	5.00	Egreso	1	44	4
133	2025-10-06	5.00	Egreso	1	44	4
134	2025-10-07	5.00	Egreso	1	44	4
135	2025-10-08	5.00	Egreso	1	44	4
136	2025-10-09	5.00	Egreso	1	44	4
137	2025-10-10	5.00	Egreso	1	44	4
138	2025-10-11	5.00	Egreso	1	44	4
139	2025-10-12	5.00	Egreso	1	44	4
140	2025-10-13	5.00	Egreso	1	44	4
141	2025-10-14	5.00	Egreso	1	44	4
142	2025-10-15	5.00	Egreso	1	44	4
143	2025-10-16	5.00	Egreso	1	44	4
144	2025-10-17	5.00	Egreso	1	44	4
145	2025-10-18	5.00	Egreso	1	44	4
146	2025-10-19	5.00	Egreso	1	44	4
147	2025-10-20	5.00	Egreso	1	44	4
148	2025-10-21	5.00	Egreso	1	44	4
149	2025-10-22	5.00	Egreso	1	44	4
150	2025-10-23	5.00	Egreso	1	44	4
151	2025-10-24	5.00	Egreso	1	44	4
152	2025-10-25	5.00	Egreso	1	44	4
153	2025-10-26	5.00	Egreso	1	44	4
154	2025-10-27	5.00	Egreso	1	44	4
155	2025-10-28	5.00	Egreso	1	44	4
156	2025-10-29	5.00	Egreso	1	44	4
157	2025-10-30	5.00	Egreso	1	44	4
158	2025-10-31	5.00	Egreso	1	44	4
159	2025-11-01	5.00	Egreso	1	44	4
160	2025-11-02	5.00	Egreso	1	44	4
161	2025-11-03	5.00	Egreso	1	44	4
162	2025-11-04	5.00	Egreso	1	44	4
163	2025-11-05	5.00	Egreso	1	44	4
164	2025-11-06	5.00	Egreso	1	44	4
165	2025-11-07	5.00	Egreso	1	44	4
166	2025-11-08	5.00	Egreso	1	44	4
167	2025-11-09	5.00	Egreso	1	44	4
168	2025-11-10	5.00	Egreso	1	44	4
169	2025-11-11	5.00	Egreso	1	44	4
170	2025-11-12	5.00	Egreso	1	44	4
171	2025-11-13	5.00	Egreso	1	44	4
172	2025-11-14	5.00	Egreso	1	44	4
173	2025-11-15	5.00	Egreso	1	44	4
174	2025-11-16	5.00	Egreso	1	44	4
175	2025-11-17	5.00	Egreso	1	44	4
176	2025-11-18	5.00	Egreso	1	44	4
177	2025-11-19	5.00	Egreso	1	44	4
178	2025-11-20	5.00	Egreso	1	44	4
179	2025-11-21	5.00	Egreso	1	44	4
180	2025-11-22	5.00	Egreso	1	44	4
181	2025-11-23	5.00	Egreso	1	44	4
182	2025-11-24	5.00	Egreso	1	44	4
183	2025-11-25	5.00	Egreso	1	44	4
184	2025-11-26	5.00	Egreso	1	44	4
185	2025-11-27	5.00	Egreso	1	44	4
186	2025-11-10	1.00	Egreso	1	49	3
187	2025-11-11	1.00	Egreso	1	49	3
188	2025-11-12	1.00	Egreso	1	49	3
189	2025-11-13	1.00	Egreso	1	49	3
190	2025-11-14	1.00	Egreso	1	49	3
191	2025-11-15	1.00	Egreso	1	49	3
192	2025-11-16	1.00	Egreso	1	49	3
193	2025-11-17	1.00	Egreso	1	49	3
194	2025-11-18	1.00	Egreso	1	49	3
195	2025-11-19	1.00	Egreso	1	49	3
196	2025-11-20	1.00	Egreso	1	49	3
197	2025-11-21	1.00	Egreso	1	49	3
198	2025-11-22	1.00	Egreso	1	49	3
199	2025-11-23	1.00	Egreso	1	49	3
200	2025-11-24	1.00	Egreso	1	49	3
201	2025-11-25	1.00	Egreso	1	49	3
202	2025-11-26	1.00	Egreso	1	49	3
203	2025-11-27	1.00	Egreso	1	49	3
204	2025-10-01	45.00	Egreso	1	21	1
205	2025-10-08	45.00	Egreso	1	21	1
206	2025-10-15	45.00	Egreso	1	21	1
207	2025-10-22	45.00	Egreso	1	21	1
208	2025-10-29	45.00	Egreso	1	21	1
209	2025-11-05	45.00	Egreso	1	21	1
210	2025-11-12	45.00	Egreso	1	21	1
211	2025-11-19	45.00	Egreso	1	21	1
212	2025-11-26	45.00	Egreso	1	21	1
213	2025-10-10	50.00	Egreso	1	30	4
214	2025-10-17	50.00	Egreso	1	30	4
215	2025-10-24	50.00	Egreso	1	30	4
216	2025-10-31	50.00	Egreso	1	30	4
217	2025-11-07	50.00	Egreso	1	30	4
218	2025-11-14	50.00	Egreso	1	30	4
219	2025-11-21	50.00	Egreso	1	30	4
220	2025-11-01	750.00	Egreso	1	9	1
221	2025-11-01	35.00	Egreso	1	24	2
222	2025-11-01	8.00	Egreso	1	28	3
223	2025-11-01	70.00	Egreso	1	29	4
224	2025-11-01	120.00	Egreso	1	31	5
225	2025-11-05	25.00	Egreso	1	35	4
226	2025-11-01	100.00	Egreso	1	39	6
227	2025-10-10	25.00	Egreso	1	45	4
228	2025-10-17	25.00	Egreso	1	45	4
229	2025-10-24	25.00	Egreso	1	45	4
230	2025-10-31	25.00	Egreso	1	45	4
231	2025-11-07	25.00	Egreso	1	45	4
232	2025-11-14	25.00	Egreso	1	45	4
233	2025-11-21	25.00	Egreso	1	45	4
234	2025-10-20	8.00	Egreso	1	46	5
235	2025-10-27	8.00	Egreso	1	46	5
236	2025-11-03	8.00	Egreso	1	46	5
237	2025-11-10	8.00	Egreso	1	46	5
238	2025-11-17	8.00	Egreso	1	46	5
239	2025-11-24	8.00	Egreso	1	46	5
240	2025-11-01	6.00	Egreso	1	47	3
241	2025-11-08	6.00	Egreso	1	47	3
242	2025-11-15	6.00	Egreso	1	47	3
243	2025-11-22	6.00	Egreso	1	47	3
244	2025-11-05	40.00	Egreso	1	48	2
245	2025-11-12	40.00	Egreso	1	48	2
246	2025-11-19	40.00	Egreso	1	48	2
247	2025-11-26	40.00	Egreso	1	48	2
248	2025-11-01	7.00	Egreso	1	50	6
249	2025-11-08	7.00	Egreso	1	50	6
250	2025-11-15	7.00	Egreso	1	50	6
251	2025-11-22	7.00	Egreso	1	50	6
252	2025-07-22	120.00	Ingreso	1	211	6
253	2025-07-23	120.00	Ingreso	1	211	6
254	2025-07-24	120.00	Ingreso	1	211	6
255	2025-07-25	120.00	Ingreso	1	211	6
256	2025-07-26	120.00	Ingreso	1	211	6
257	2025-07-27	120.00	Ingreso	1	211	6
258	2025-07-28	120.00	Ingreso	1	211	6
259	2025-07-29	120.00	Ingreso	1	211	6
260	2025-07-30	120.00	Ingreso	1	211	6
261	2025-07-31	120.00	Ingreso	1	211	6
262	2025-08-01	120.00	Ingreso	1	211	6
263	2025-08-02	120.00	Ingreso	1	211	6
264	2025-08-03	120.00	Ingreso	1	211	6
265	2025-08-04	120.00	Ingreso	1	211	6
266	2025-08-05	120.00	Ingreso	1	211	6
267	2025-08-06	120.00	Ingreso	1	211	6
268	2025-08-07	120.00	Ingreso	1	211	6
269	2025-08-08	120.00	Ingreso	1	211	6
270	2025-08-09	120.00	Ingreso	1	211	6
271	2025-08-10	120.00	Ingreso	1	211	6
272	2025-08-11	120.00	Ingreso	1	211	6
273	2025-08-12	120.00	Ingreso	1	211	6
274	2025-08-13	120.00	Ingreso	1	211	6
275	2025-08-14	120.00	Ingreso	1	211	6
276	2025-08-15	120.00	Ingreso	1	211	6
277	2025-08-16	120.00	Ingreso	1	211	6
278	2025-08-17	120.00	Ingreso	1	211	6
279	2025-08-18	120.00	Ingreso	1	211	6
280	2025-08-19	120.00	Ingreso	1	211	6
281	2025-08-20	120.00	Ingreso	1	211	6
282	2025-08-21	120.00	Ingreso	1	211	6
283	2025-08-22	120.00	Ingreso	1	211	6
284	2025-08-23	120.00	Ingreso	1	211	6
285	2025-08-24	120.00	Ingreso	1	211	6
286	2025-08-25	120.00	Ingreso	1	211	6
287	2025-08-26	120.00	Ingreso	1	211	6
288	2025-08-27	120.00	Ingreso	1	211	6
289	2025-08-28	120.00	Ingreso	1	211	6
290	2025-08-29	120.00	Ingreso	1	211	6
291	2025-08-30	120.00	Ingreso	1	211	6
292	2025-08-31	120.00	Ingreso	1	211	6
293	2025-09-01	120.00	Ingreso	1	211	6
294	2025-09-02	120.00	Ingreso	1	211	6
295	2025-09-03	120.00	Ingreso	1	211	6
296	2025-09-04	120.00	Ingreso	1	211	6
297	2025-09-05	120.00	Ingreso	1	211	6
298	2025-09-06	120.00	Ingreso	1	211	6
299	2025-09-07	120.00	Ingreso	1	211	6
300	2025-09-08	120.00	Ingreso	1	211	6
301	2025-09-09	120.00	Ingreso	1	211	6
302	2025-09-10	120.00	Ingreso	1	211	6
303	2025-09-11	120.00	Ingreso	1	211	6
304	2025-09-12	120.00	Ingreso	1	211	6
305	2025-09-13	120.00	Ingreso	1	211	6
306	2025-09-14	120.00	Ingreso	1	211	6
307	2025-09-15	120.00	Ingreso	1	211	6
308	2025-09-16	120.00	Ingreso	1	211	6
309	2025-09-17	120.00	Ingreso	1	211	6
310	2025-09-18	120.00	Ingreso	1	211	6
311	2025-09-19	120.00	Ingreso	1	211	6
312	2025-09-20	120.00	Ingreso	1	211	6
313	2025-09-21	120.00	Ingreso	1	211	6
314	2025-09-22	120.00	Ingreso	1	211	6
315	2025-09-23	120.00	Ingreso	1	211	6
316	2025-09-24	120.00	Ingreso	1	211	6
317	2025-09-25	120.00	Ingreso	1	211	6
318	2025-09-26	120.00	Ingreso	1	211	6
319	2025-09-27	120.00	Ingreso	1	211	6
320	2025-09-28	120.00	Ingreso	1	211	6
321	2025-09-29	120.00	Ingreso	1	211	6
322	2025-09-30	120.00	Ingreso	1	211	6
323	2025-10-01	120.00	Ingreso	1	211	6
324	2025-10-02	120.00	Ingreso	1	211	6
325	2025-10-03	120.00	Ingreso	1	211	6
326	2025-10-04	120.00	Ingreso	1	211	6
327	2025-10-05	120.00	Ingreso	1	211	6
328	2025-10-06	120.00	Ingreso	1	211	6
329	2025-10-07	120.00	Ingreso	1	211	6
330	2025-10-08	120.00	Ingreso	1	211	6
331	2025-10-09	120.00	Ingreso	1	211	6
332	2025-10-10	120.00	Ingreso	1	211	6
333	2025-10-11	120.00	Ingreso	1	211	6
334	2025-10-12	120.00	Ingreso	1	211	6
335	2025-10-13	120.00	Ingreso	1	211	6
336	2025-10-14	120.00	Ingreso	1	211	6
337	2025-10-15	120.00	Ingreso	1	211	6
338	2025-10-16	120.00	Ingreso	1	211	6
339	2025-10-17	120.00	Ingreso	1	211	6
340	2025-10-18	120.00	Ingreso	1	211	6
341	2025-10-19	120.00	Ingreso	1	211	6
342	2025-10-20	120.00	Ingreso	1	211	6
343	2025-10-21	120.00	Ingreso	1	211	6
344	2025-10-22	120.00	Ingreso	1	211	6
345	2025-10-23	120.00	Ingreso	1	211	6
346	2025-10-24	120.00	Ingreso	1	211	6
347	2025-10-25	120.00	Ingreso	1	211	6
348	2025-10-26	120.00	Ingreso	1	211	6
349	2025-10-27	120.00	Ingreso	1	211	6
350	2025-10-28	120.00	Ingreso	1	211	6
351	2025-10-29	120.00	Ingreso	1	211	6
352	2025-10-30	120.00	Ingreso	1	211	6
353	2025-10-31	120.00	Ingreso	1	211	6
354	2025-11-01	120.00	Ingreso	1	211	6
355	2025-11-02	120.00	Ingreso	1	211	6
356	2025-11-03	120.00	Ingreso	1	211	6
357	2025-11-04	120.00	Ingreso	1	211	6
358	2025-11-05	120.00	Ingreso	1	211	6
359	2025-11-06	120.00	Ingreso	1	211	6
360	2025-11-07	120.00	Ingreso	1	211	6
361	2025-11-08	120.00	Ingreso	1	211	6
362	2025-11-09	120.00	Ingreso	1	211	6
363	2025-11-10	120.00	Ingreso	1	211	6
364	2025-11-11	120.00	Ingreso	1	211	6
365	2025-11-12	120.00	Ingreso	1	211	6
366	2025-11-13	120.00	Ingreso	1	211	6
367	2025-11-14	120.00	Ingreso	1	211	6
368	2025-11-15	120.00	Ingreso	1	211	6
369	2025-11-16	120.00	Ingreso	1	211	6
370	2025-11-17	120.00	Ingreso	1	211	6
371	2025-11-18	120.00	Ingreso	1	211	6
372	2025-11-19	120.00	Ingreso	1	211	6
373	2025-11-20	120.00	Ingreso	1	211	6
374	2025-11-21	120.00	Ingreso	1	211	6
375	2025-11-22	120.00	Ingreso	1	211	6
376	2025-11-23	120.00	Ingreso	1	211	6
377	2025-11-24	120.00	Ingreso	1	211	6
378	2025-11-25	120.00	Ingreso	1	211	6
379	2025-11-26	120.00	Ingreso	1	211	6
380	2025-11-27	120.00	Ingreso	1	211	6
381	2025-07-09	750.00	Ingreso	1	201	2
382	2025-08-09	750.00	Ingreso	1	201	2
383	2025-09-09	750.00	Ingreso	1	201	2
384	2025-10-09	750.00	Ingreso	1	201	2
385	2025-11-09	750.00	Ingreso	1	201	2
386	2025-07-07	500.00	Ingreso	1	202	3
387	2025-08-07	500.00	Ingreso	1	202	3
388	2025-09-07	500.00	Ingreso	1	202	3
389	2025-10-07	500.00	Ingreso	1	202	3
390	2025-11-07	500.00	Ingreso	1	202	3
391	2025-07-19	100.00	Ingreso	1	233	6
392	2025-08-19	100.00	Ingreso	1	233	6
393	2025-09-19	100.00	Ingreso	1	233	6
394	2025-10-19	100.00	Ingreso	1	233	6
395	2025-11-19	100.00	Ingreso	1	233	6
396	2025-07-06	20.00	Ingreso	1	234	1
397	2025-08-06	20.00	Ingreso	1	234	1
398	2025-09-06	20.00	Ingreso	1	234	1
399	2025-10-06	20.00	Ingreso	1	234	1
400	2025-11-06	20.00	Ingreso	1	234	1
401	2025-07-18	50.00	Ingreso	1	230	3
402	2025-08-18	50.00	Ingreso	1	230	3
403	2025-09-18	50.00	Ingreso	1	230	3
404	2025-10-18	50.00	Ingreso	1	230	3
405	2025-11-18	50.00	Ingreso	1	230	3
406	2025-07-27	100.00	Ingreso	1	222	6
407	2025-08-03	100.00	Ingreso	1	222	6
408	2025-08-10	100.00	Ingreso	1	222	6
409	2025-08-17	100.00	Ingreso	1	222	6
410	2025-08-24	100.00	Ingreso	1	222	6
411	2025-08-31	100.00	Ingreso	1	222	6
412	2025-09-07	100.00	Ingreso	1	222	6
413	2025-09-14	100.00	Ingreso	1	222	6
414	2025-09-21	100.00	Ingreso	1	222	6
415	2025-09-28	100.00	Ingreso	1	222	6
416	2025-10-05	100.00	Ingreso	1	222	6
417	2025-10-12	100.00	Ingreso	1	222	6
418	2025-10-19	100.00	Ingreso	1	222	6
419	2025-10-26	100.00	Ingreso	1	222	6
420	2025-11-02	100.00	Ingreso	1	222	6
421	2025-11-09	100.00	Ingreso	1	222	6
422	2025-11-16	100.00	Ingreso	1	222	6
423	2025-11-23	100.00	Ingreso	1	222	6
424	2025-07-28	150.00	Ingreso	1	212	1
425	2025-08-04	150.00	Ingreso	1	212	1
426	2025-08-11	150.00	Ingreso	1	212	1
427	2025-08-18	150.00	Ingreso	1	212	1
428	2025-08-25	150.00	Ingreso	1	212	1
429	2025-09-01	150.00	Ingreso	1	212	1
430	2025-09-08	150.00	Ingreso	1	212	1
431	2025-09-15	150.00	Ingreso	1	212	1
432	2025-09-22	150.00	Ingreso	1	212	1
433	2025-09-29	150.00	Ingreso	1	212	1
434	2025-10-06	150.00	Ingreso	1	212	1
435	2025-10-13	150.00	Ingreso	1	212	1
436	2025-10-20	150.00	Ingreso	1	212	1
437	2025-10-27	150.00	Ingreso	1	212	1
438	2025-11-03	150.00	Ingreso	1	212	1
439	2025-11-10	150.00	Ingreso	1	212	1
440	2025-11-17	150.00	Ingreso	1	212	1
441	2025-11-24	150.00	Ingreso	1	212	1
442	2025-07-26	150.00	Ingreso	1	214	3
443	2025-08-02	150.00	Ingreso	1	214	3
444	2025-08-09	150.00	Ingreso	1	214	3
445	2025-08-16	150.00	Ingreso	1	214	3
446	2025-08-23	150.00	Ingreso	1	214	3
447	2025-08-30	150.00	Ingreso	1	214	3
448	2025-09-06	150.00	Ingreso	1	214	3
449	2025-09-13	150.00	Ingreso	1	214	3
450	2025-09-20	150.00	Ingreso	1	214	3
451	2025-09-27	150.00	Ingreso	1	214	3
452	2025-10-04	150.00	Ingreso	1	214	3
453	2025-10-11	150.00	Ingreso	1	214	3
454	2025-10-18	150.00	Ingreso	1	214	3
455	2025-10-25	150.00	Ingreso	1	214	3
456	2025-11-01	150.00	Ingreso	1	214	3
457	2025-11-08	150.00	Ingreso	1	214	3
458	2025-11-15	150.00	Ingreso	1	214	3
459	2025-11-22	150.00	Ingreso	1	214	3
460	2025-11-01	300.00	Ingreso	1	33	2
461	2025-09-10	8.00	Ingreso	1	244	6
462	2025-07-13	100.00	Ingreso	1	231	4
463	2025-08-13	100.00	Ingreso	1	231	4
464	2025-09-13	100.00	Ingreso	1	231	4
465	2025-10-13	100.00	Ingreso	1	231	4
466	2025-11-13	100.00	Ingreso	1	231	4
467	2025-07-26	150.00	Ingreso	1	232	5
468	2025-08-26	150.00	Ingreso	1	232	5
469	2025-09-26	150.00	Ingreso	1	232	5
470	2025-10-26	150.00	Ingreso	1	232	5
471	2025-11-26	150.00	Ingreso	1	232	5
472	2025-07-07	400.00	Ingreso	1	210	5
473	2025-08-07	400.00	Ingreso	1	210	5
474	2025-09-07	400.00	Ingreso	1	210	5
475	2025-10-07	400.00	Ingreso	1	210	5
476	2025-11-07	400.00	Ingreso	1	210	5
477	2025-07-08	200.00	Ingreso	1	213	2
478	2025-08-08	200.00	Ingreso	1	213	2
479	2025-09-08	200.00	Ingreso	1	213	2
480	2025-10-08	200.00	Ingreso	1	213	2
481	2025-11-08	200.00	Ingreso	1	213	2
482	2025-07-09	500.00	Ingreso	1	203	4
483	2025-08-09	500.00	Ingreso	1	203	4
484	2025-09-09	500.00	Ingreso	1	203	4
485	2025-10-09	500.00	Ingreso	1	203	4
486	2025-11-09	500.00	Ingreso	1	203	4
487	2025-07-15	600.00	Ingreso	1	200	1
488	2025-08-15	600.00	Ingreso	1	200	1
489	2025-09-15	600.00	Ingreso	1	200	1
490	2025-10-15	600.00	Ingreso	1	200	1
491	2025-11-15	600.00	Ingreso	1	200	1
\.


--
-- TOC entry 5100 (class 0 OID 19022)
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
-- TOC entry 5112 (class 0 OID 0)
-- Dependencies: 220
-- Name: categoria_idcategoria_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categoria_idcategoria_seq', 50, false);


--
-- TOC entry 5113 (class 0 OID 0)
-- Dependencies: 222
-- Name: concepto_idconcepto_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.concepto_idconcepto_seq', 521, true);


--
-- TOC entry 5114 (class 0 OID 0)
-- Dependencies: 224
-- Name: familia_idfamilia_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.familia_idfamilia_seq', 1, true);


--
-- TOC entry 5115 (class 0 OID 0)
-- Dependencies: 226
-- Name: transaccion_idtransaccion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transaccion_idtransaccion_seq', 492, true);


--
-- TOC entry 5116 (class 0 OID 0)
-- Dependencies: 228
-- Name: usuario_idusuario_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuario_idusuario_seq', 50, false);


--
-- TOC entry 4923 (class 2606 OID 19040)
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (idcategoria);


--
-- TOC entry 4925 (class 2606 OID 19042)
-- Name: concepto concepto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT concepto_pkey PRIMARY KEY (idconcepto);


--
-- TOC entry 4927 (class 2606 OID 19044)
-- Name: familia familia_codigofamilia_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia
    ADD CONSTRAINT familia_codigofamilia_key UNIQUE (codigofamilia);


--
-- TOC entry 4929 (class 2606 OID 19046)
-- Name: familia familia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familia
    ADD CONSTRAINT familia_pkey PRIMARY KEY (idfamilia);


--
-- TOC entry 4931 (class 2606 OID 19048)
-- Name: transaccion transaccion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT transaccion_pkey PRIMARY KEY (idtransaccion);


--
-- TOC entry 4933 (class 2606 OID 19050)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (idusuario);


--
-- TOC entry 4935 (class 2606 OID 19052)
-- Name: usuario usuario_usuario_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_usuario_key UNIQUE (usuario);


--
-- TOC entry 4938 (class 2606 OID 19053)
-- Name: concepto fk_categoria; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_categoria FOREIGN KEY (idcategoria) REFERENCES public.categoria(idcategoria) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4941 (class 2606 OID 19058)
-- Name: transaccion fk_concepto; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_concepto FOREIGN KEY (idconcepto) REFERENCES public.concepto(idconcepto) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4944 (class 2606 OID 19063)
-- Name: usuario fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4936 (class 2606 OID 19068)
-- Name: categoria fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4939 (class 2606 OID 19073)
-- Name: concepto fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4942 (class 2606 OID 19078)
-- Name: transaccion fk_familia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_familia FOREIGN KEY (idfamilia) REFERENCES public.familia(idfamilia) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4937 (class 2606 OID 19083)
-- Name: categoria fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4940 (class 2606 OID 19088)
-- Name: concepto fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.concepto
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4943 (class 2606 OID 19093)
-- Name: transaccion fk_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaccion
    ADD CONSTRAINT fk_usuario FOREIGN KEY (idusuario) REFERENCES public.usuario(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


-- Completed on 2025-11-27 23:46:19

--
-- PostgreSQL database dump complete
--

\unrestrict IcaqRL5c43z8pORWyWJ4Yj6rUzXUkT1ca9GetMNiDaahpsrOIE4YBBuUKiGVXC8

