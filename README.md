# Tickera.com
Proyecto web de la materia Programación 2. El proyecto esta configurado para ser ejecutado en conjunto con XAMPP pero debería funcionar en cualquier entorno.

## Compilar el proyecto
1. Clonar el proyecto.
2. Crear el archivo .env
2.1 En la raíz de nuestro proyecto, crear un archivo con nombre **.env** y abrirlo con nuestro editor favorito
2.2 Copiar y pegar el siguiente fragmento:

        DB_HOST=localhost
        DB_NAME=tickera-system-php
        DB_USER=root
        DB_PASS=
        DB_DRIVER=mysql
2.3 Cerrrar y guardar.
3. Mover la carpeta del proyecto en el entorno en donde trabajremos. En el caso de xampp, copiarla a htdocs.
4.  Cambiar la ruta raiz de Apache server:
4.1 Abrir xampp, en la fila de Apache, presionamos Config -> `<`Browse`>` [Apache]
4.2 Ingresar a la carpeta conf y abrir con el editor preferido el archivo httpd.conf
4.3 Cambiar el DocumentRoot y el Directory por el path del proyecto. Eg: 

        DocumentRoot "C:/xampp/htdocs/tickera-system-php"
        <Directory "C:/xampp/htdocs/tickera-system-php">
5. Descargar dependencias. Sin este paso el proyecto no va a compilar:
5.1 En XAMPP, abrimos la terminal (botón `shell`)
5.2 Ejecutamos el comando `cd htdocs tickera-system-php`.
5.3 Ejecutamos el comando `php composer.phar update`.
5.4 Esperamos a que cree la carpeta vendor, descargue las dependencias y configure el autoload.
6. En XAMPP, activamos el servidor de Apache y de MySQL.
7. Importar archivo .sql:
7.1 Descargar el archivo .sql adjunto a la entrega (o pedírselos a los autores).
7.2 En el navegador, ingresar a `localhost/phpmyadmin`.
7.3 Crear una base de datos con el nombre **tickera-system-php**.
7.4 En la barra superior, ir a Import. 
7.5 Undir el botón Choose File y subir el archivo .sql. Ir al final de la página y undir Go.
8. Finalmente, ingresar a `localhost`.