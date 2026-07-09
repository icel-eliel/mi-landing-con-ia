# Café Eliel Landing Page

Landing page moderna y acogedora para Café Eliel, pensada para jóvenes universitarios con un estilo limpio, minimalista y responsive.

## Archivos principales

- index.php: landing page con saludo por nombre y acceso a login/dashboard.
- login.php: formulario de acceso por correo y contraseña.
- dashboard.php: panel para crear/editar usuarios y consultar ventas del día.
- config.php: conexión a base de datos y sesión de PHP.
- database/schema.sql: script inicial de MySQL con tablas de usuarios y ventas.

## Despliegue en Railway

1. Crea un servicio PHP en Railway.
2. Sube este repositorio.
3. Define las variables de entorno del archivo .env.example.
4. Ejecuta el script database/schema.sql en tu base de datos MySQL de Railway.
5. Abre la URL generada por Railway para ver la landing.

