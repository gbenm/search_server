# Search Engine

## Prerequisitos

### Uso de docker
- Docker 20+
- Docker composer v2

## Instalación
Cree una copia del archivo `.env.example` con el nombre `.env`
en el directorio raíz y modifique si es necesario (está
preconfigurado para funcionar con docker).

## Ejecución
```bash
docker compose --profile all up -d
```

> `dev`, `devapp`, `debug` son perfiles más especificos
> que permite iniciar sólo ciertas partes con docker. `dev`
> inicia las bases de datos utilizadas (mysql y redis),
> `devapp` ejecuta el contenedor php que tiene todo lo
> necesario para correr y descargar las dependencias y
> `debug` levanta la instancia de phpmyadmin para
> poder tener acceso a los datos de una forma más
> sencilla.

## API
Vea [api](./docs/api.md).

## Estructura del proyecto
Existen 3 carpetas que contienen el código de la aplicación
(excluyendo `vendor` que es dónde se guardan las dependencias
de terceros)

- `bootstrap` contiene el código necesario para iniciar
  la estructura personalizada de este proyecto, siendo
  `app.php` el punto inicial.
- `public` contiene los archivos que serán expuestos
  directamente en el servidor, siendo `index.php` el
  inicio de la aplicación
- `src` contiene el código base de la applicación,
  sigue el estándar PSR4 para auto-cargar los archivos.

### Desarrollo basado en módulos
La carpeta `src` contiene los módulos de la aplicación.

Cada módulo puede contener:
- `Application`: contiene los casos de uso del negocio.
- `Domain`: sigue el concepto de arquitectura hexagonal,
  los elementos definidos aquí son basados en el negocio
  y al ser la capa más interna están protegidos de
  cambiar sin que los requerimientos del negocio varien.
- `Infrastructure`: contiene el código que adapta los
  requerimientos de `Domain`, son los que sirven de medio
  a los recursos actuales.

Los módulos en `src/`:
- `Shared` almacena código compartido entre todos, por lo
  que no pertenece a un área en especifico.
- `Search` es el módulo que se encarga de buscar las
  respuestas.
- `Stats` se encarga de administrar las estadísticas.

## PHP nativo?
- Composer 2.4.4+
- PHP 8.1+
- Extensión mysqli
- Dependencias necesarias para que composer pueda
  descargar las dependencias
