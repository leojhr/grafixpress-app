# ¿Para qué es esta App?

Fue creada con la finalidad de agilizar las ventas y mantener un seguimiento del inventario del negocio **GrafiXpress C.A**.

El proyecto es parte del proyecto sociotecnológico del 3er año de Ing. Informática en la **UNERMB**.

# Proceso de instalación y despliegue

Es necesario cumplir con los siguientes requisitos:

-   PHP 8.2
-   Laravel
-   Conexión MariaDB/MySQL

### Crear archivos y directorios

Se puede descargar el código fuente de este repositorio y subirlo directamente al servidor donde se ejecutará, la configuración puede variar dependiendo del servidor Web que se esté ejecutando (Apache o Nginx).

### Configuración del archivo .env

Es necesario que se genere un _APP_KEY_ nuevo, con el siguiente comando:

    `php artisan key:generate`

Para luego configurar con las credenciales de su base de datos.

### Instalación de dependencias

    `composer install`

### Permisos requeridos

Para que la aplicación esté disponible, es requerido que el servidor web tenga acceso a la carpeta _Storage_ del proyecto.

    `chmod -R 777 storage`

### Configurar las migraciones

Uno de los últimos pasos para dejar operativa la aplicación, es ejecutar las migraciones del proyecto, con el siguiente comando

    `php artisan migrate`

### Creación de un usuario

Para poder acceder al panel se necesitará de un usuario para ingresar, el cual se puede generar con el siguiente comando.

    `php artisan make:filament-user`
