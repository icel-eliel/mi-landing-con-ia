# Cafe Eliel Landing Page

Landing page PHP para demo de despliegue con login, dashboard basico y conexion a MySQL.

## Archivos principales

- `index.php`: landing page con saludo por nombre y acceso a login/dashboard.
- `login.php`: formulario de acceso por correo y contrasena.
- `dashboard.php`: panel para crear/editar usuarios y consultar ventas del dia.
- `config.php`: conexion a base de datos usando variables de entorno.
- `database/schema.sql`: tablas iniciales de MySQL y usuario demo.

## Despliegue en Railway

1. Crea el servicio web desde este repositorio.
2. Agrega un servicio MySQL dentro del mismo proyecto de Railway.
3. En el servicio web, verifica que existan las variables de MySQL. Railway suele exponerlas como `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` y/o `MYSQL_URL`.
4. Ejecuta el contenido de `database/schema.sql` en la base MySQL de Railway.
5. Despliega nuevamente el servicio web.

El contenedor usa la variable `PORT` que Railway asigna automaticamente al iniciar. No hace falta configurarla manualmente.

## Credenciales demo

- Correo: `admin@cafeeliel.com`
- Contrasena: `admin123`

## Variables locales opcionales

Si corres el proyecto fuera de Railway, puedes definir:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cafe_eliel
DB_USERNAME=root
DB_PASSWORD=
```
