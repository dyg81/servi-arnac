# SAC ARNAC

Aplicación web interna para gestionar servicios archivísticos: clientes, fondos, depósitos, libros y expedientes, además de investigaciones, cartas de autorización, transcripciones, reprografía y certificaciones. Incluye perfiles por área, bitácora de actividad, reportes y estadísticas.

## Tecnologías

- PHP; el plan define como objetivo de compatibilidad PHP 7.1.3 a 7.4.33. El runtime y Composer aún deben verificarse/actualizarse para reflejarlo.
- Symfony 4.4; la actualización objetivo es la última versión de esa rama, 4.4.51.
- Doctrine ORM y Doctrine Migrations; las entidades usan anotaciones de Doctrine.
- MariaDB 10.4, Apache y Docker Compose.
- Twig y recursos front-end servidos desde `public/assets` (AdminLTE, jQuery, DataTables, Select2 y Chart.js, entre otros).

## Particularidades importantes

- La rama Symfony 4.4 y PHP 7.4 están fuera de soporte. Se conservan por requisito del proyecto; no reciben nuevas correcciones upstream de seguridad.
- La plataforma objetivo para resolver dependencias es PHP 7.4.33, sin aceptar dependencias que requieran PHP 8. El límite inferior de Symfony 4.4 es PHP 7.1.3.
- Las rutas y clases actuales usan anotaciones, por lo que la modernización debe preservar esa configuración. El requisito de evitar PHP 8 se conserva como límite del proyecto; las anotaciones por sí solas no obligan a usar PHP 7.

## Configuración local

La configuración Docker actual define Apache en `http://localhost:8080` y MariaDB en el puerto de host `3307`. El Compose usa credenciales de desarrollo y monta el repositorio como volumen; no debe considerarse una receta segura ni reproducible de producción.

## Validación

No se debe asumir que el proyecto pasa pruebas por tener PHPUnit declarado. Las validaciones realizadas deben documentarse junto con los cambios correspondientes.
