# Configuración de Apache y .htaccess para rutas limpias en MAMP

## Objetivo
Permitir el uso de rutas limpias ("pretty URLs") en una API PHP, de modo que las peticiones como `/api/aulas/` sean gestionadas por `index.php` usando un archivo `.htaccess`.

---

## Pasos realizados

### 1. Habilitar AllowOverride en Apache
- Editamos el archivo de configuración de Apache en MAMP:
  `/Applications/MAMP/conf/apache/httpd.conf`
- Buscamos la sección correspondiente al directorio `htdocs`:
  ```
  <Directory "/Applications/MAMP/htdocs">
      ...
      AllowOverride All
      ...
  </Directory>
  ```
- Nos aseguramos de que la línea `AllowOverride All` esté presente (no `None`).

### 2. Habilitar el módulo mod_rewrite
- En el mismo archivo `httpd.conf`, buscamos la línea:
  ```
  #LoadModule rewrite_module modules/mod_rewrite.so
  ```
- Quitamos el `#` para descomentarla:
  ```
  LoadModule rewrite_module modules/mod_rewrite.so
  ```

### 3. Reiniciar los servidores de MAMP
- Guardamos los cambios y reiniciamos Apache desde la interfaz de MAMP.

### 4. Crear o ajustar el archivo `.htaccess`
- En la raíz del proyecto (`/Applications/MAMP/htdocs/api/`), dejamos el siguiente contenido en `.htaccess`:
  ```
  DirectoryIndex index.php
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php [QSA,L]
  ```

---

## Resultado
- Ahora, cualquier petición a `/api/loquesea` será gestionada por `index.php`, permitiendo implementar un router PHP y usar URLs limpias.
- Si el profesor o cualquier usuario descomprime el proyecto y sigue estos pasos, tendrá una API profesional y segura.

---

## Notas
- Si .htaccess no funciona, revisar los logs de Apache en `/Applications/MAMP/logs/apache_error.log`.
- Si se entrega el proyecto, incluir este README para facilitar la configuración.
