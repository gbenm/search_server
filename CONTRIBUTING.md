# Guías para el código

## Estructura del proyecto
- `bootstrap/` contiene el código que sirve
  para correr y configurar la aplicación.
- `src/` contiene el código del servidor. Está
  organizado por módulos y cada módulo debe seguir
  las siguientes reglas:
  - `Application` contiene el código que solventa
    los requerimientos mediante las abstracciones
    del dominio.
  - `Domain` contiene las entidades e interfaces de
    la aplicación para separar la infraestructura de
    los requerimientos (el código definido aquí sólo
    cambiará si los requerimientos cambian).
  - `Infrastructure` contiene las implementaciones
    con los proveedores, librerías de terceros y
    cualquier código que sirve para lograr
    el objetivo de la aplicación en este momento.
- `tests/` debe contener las pruebas de cada
  módulo y sus respectivas **factories** de ser
  necesarias.

## Principios
- Para el código del servidor se prefiere **DRY**.
- Para el código de las pruebas se prefiere **DAMP**.
