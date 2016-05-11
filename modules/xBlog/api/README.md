# Editor HTML5 Ximdex CMS

Servicio que provee un editor HTML a Ximdex que interactúa con la API REST de Ximdex CMS. En config.yml se encuentra la configuración básica para 'conectarlo' con Ximdex CMS.

### Ejecución para desarrollo

Para montarlo en el servidor de PHP. Ejecutar en la raíz:

```sh
$ php -S localhost:8080 -t ./public/
```

### Instalación de dependencias

```sh
$ npm install
$ bower install
$ composer update
```

### Compilación de hojas de estilos y scripts

```sh
$ grunt
```

### Watcher de compilación de scripts

```sh
$ grunt watch
```