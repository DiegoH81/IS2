# Proyecto Ingenieria de Software II
---
### Profesor
- Guillermo Enrique, Calderón Ruiz
---

### Alumnos
- Alvarez Puma, Alan Patrizio
- Hidalgo Machaca, Diego Alejandro
- Huicho Perez, Anthony
- Valencia Flores, Neymi Arlyz
- Valenzuela Calderón, Luigi Yamil

---

## Sistema de gestion de economia familiar - On a Budget

El proyecto tiene como propósito principal el diseño de la aplicación web “On a Budget”, una herramienta sencilla e intuitiva que permitirá a los miembros de la familia visualizar, gestionar e interactuar con sus finanzas de manera efectiva, facilitando el control de sus gastos e ingresos diarios.

---

## Funcionalidades Principales

1. **Registro de Usuarios:** Permite que nuevos usuarios creen una cuenta dentro de la aplicación.
2. **Gestión de usuarios:** Permite visualizar la lista de usuarios registrados, editar su información y activar o desactivar sus cuentas.
3. **Gestión de conceptos:** Permite crear, editar y habilitar o deshabilitar conceptos asociados a las transacciones dentro del sistema.
4. **Gestión de categorías:** Permite agrupar varios conceptos bajo una misma categoría general.
5. **Visualizador de registro diario:** Permite ver las transacciones del día actual, asi como el balance diario respectivo y su corte semanal.
6. **Visualizador de balance:** Permite ver las transacciones realizadas desde el primer día del mes hasta el dia actual, cons sus respectivos balances.
7. **Balance por rango:** Permite ver las transacciones realizadas entre dos fechas escogidas manualmente.
8. **Visualizador de agenda:** Permite ver que conceptos tienen una fecha de pago próxima.
9. **Visualizador de ranking:** Permite ver que transacciones son las que generan montos mayores de ingresos y egresos.

---

## Tecnologías Usadas

1. **Frontend:** HTML5, CSS3, JavaScript
2. **Backend:**	PHP 8.x
3. **Base de Datos:**	PostgreSQL 18.x
4. **Servidor Web:**	Apache (vía XAMPP)

---

# Instalación

1. **XAMPP 8.x (PHP + Apache)**

Descargar: https://www.apachefriends.org/es/index.html
Durante instalación seleccionar: Apache y PHP

2. **PostgreSQL 18.x + pgAdmin**

Descargar: https://www.postgresql.org/download/
Durante la instalación: Crear una contraseña para el usuario postgres y
guardarla (se necesitará para la conexión)

3. **Git**

Descargar: https://git-scm.com/
Configurar usuario con los siguientes comandos:
git config --global user.name "Tu Nombre de Usuario"
git config --global user.email "tuemail@ejemplo.com"

# Configuración

1. **Crear base de datos**

En pgAdmin crear una base de datos con el nombre: OAB_DB

2. **Importar la BD desde el repositorio**

Dentro de la carpeta /bd/ está el archivo: OAB_DB.sql

Para importar:

- Clic derecho en la BD → Restore
- Seleccionar el archivo .sql
- Clic en Restore

3. **Configurar PHP para PostgreSQL (XAMPP)**

- Editar archivo: C:/xampp/php/php.ini
- Descomentar estas líneas (quitar el ;):
extension=pgsql
extension=pdo_pgsql

# Ejecución del Proyecto

1. Abrir XAMPP Control Panel
2. Activar Apache
3. Ir al botón Admin (del módulo Apache)
4. Se abrirá en el navegador la URL del proyecto, por ejemplo: http://localhost/IS2/

# Capturas de ejecución

Interfaz de Registro Diario, donde el usuario puede visualizar las transacciones del día.

<img width="1920" height="1080" alt="registroDiario" src="https://github.com/user-attachments/assets/04c8caab-ec90-4b4c-885f-59a6c91a23fd" />

Formulario de creación de transaccion, donde el usuario ingresa el concepto y monto de una transacción para crearla.

<img width="1920" height="1080" alt="crearTransaccion" src="https://github.com/user-attachments/assets/08da2d67-1ef8-42a0-92c8-1820d3ecebc7" />

Interfaz de agenda, donde el usuario puede ver que conceptos tienen una fecha cercana posible para instanciar una transacción.

<img width="1920" height="1080" alt="agenda" src="https://github.com/user-attachments/assets/a22d5d5b-4236-421f-8b8e-d2785315b45a" />

Interfaz de ranking, donde el usuario puede visualizar que transacciones de egresos e ingresos son los que tienen los montos mas altos.

<img width="1920" height="1080" alt="ranking" src="https://github.com/user-attachments/assets/173900b5-b2e1-45ed-a347-9c4951b1d10d" />

