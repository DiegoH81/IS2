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

3.Git

Descargar: https://git-scm.com/
Configurar usuario con los siguientes comandos:
git config --global user.name "Tu Nombre de Usuario"
git config --global user.email "tuemail@ejemplo.com"

# Configuración

1. **Crear base de datos**

En pgAdmin crear una base de datos con el nombre: OAB_DB (o 

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
