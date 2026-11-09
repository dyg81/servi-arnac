# Servi ARNAC

Servi ARNAC es una aplicación web para gestionar servicios archivísticos. Permite administrar clientes, fondos, depósitos, libros y expedientes, y tramitar solicitudes de investigación, cartas, transcripciones, reprografía y certificaciones.

## Requisitos

- Git
- Docker Engine y Docker Compose (recomendado)
- Para ejecutar la aplicación fuera de Docker: PHP 7.4 y Composer

## Clonar e instalar

```sh
git clone <URL_DEL_REPOSITORIO>
cd <DIRECTORIO_DEL_REPOSITORIO>
```

Con Docker, las dependencias de Composer se instalan durante la construcción de la imagen. Para una instalación local sin Docker:

```sh
composer install
```

## Desarrollo con Docker

Copia la plantilla de desarrollo y reemplaza los valores de ejemplo:

```sh
cp .env.local.example .env.local
```

Configura `APP_SECRET`, `DATABASE_URL`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD` y `MYSQL_ROOT_PASSWORD`. `DATABASE_URL` debe usar el host `db`, el puerto interno `3306`, el nombre y las credenciales de la base configurados en las variables `MYSQL_*`, y la versión MariaDB indicada en el ejemplo. No reutilices las credenciales de ejemplo.

Construye e inicia la aplicación y MariaDB:

```sh
docker compose --env-file .env.local up --build -d
```

La aplicación estará disponible en <http://localhost:8080>. `APP_PORT` cambia el puerto local de la aplicación y `DB_PORT` el puerto de MariaDB publicado para conexiones desde el host.

## Crear la base de datos y aplicar migraciones

El contenedor MariaDB crea automáticamente la base indicada por `MYSQL_DATABASE` al inicializar un volumen vacío. Después de iniciar los servicios, aplica la migración inicial:

```sh
docker compose --env-file .env.local exec app php bin/console doctrine:migrations:status
docker compose --env-file .env.local exec app php bin/console doctrine:migrations:migrate
```

Si ejecutas Symfony fuera de Docker, crea primero la base configurada en `DATABASE_URL` y luego aplica las migraciones:

```sh
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

El repositorio incluye una migración inicial que crea el esquema definido por las entidades actuales. Esta línea base es para instalaciones nuevas. Los cambios futuros del modelo deben añadirse como nuevas migraciones; no se debe volver a reemplazar la migración inicial una vez publicada.

Para generar una migración después de modificar entidades, inicia el entorno de desarrollo y ejecuta:

```sh
docker compose --env-file .env.local exec app php bin/console make:migration
```

Revisa el archivo generado y aplícalo con `doctrine:migrations:migrate` siguiendo el comando de instalación de arriba.

## Crear el usuario administrador

Con los servicios de desarrollo activos y la migración aplicada, ejecuta el comando y escribe la contraseña cuando se solicite:

```sh
docker compose --env-file .env.local exec -it app php bin/console app:crear-admin
```

Para ejecutar el comando fuera de Docker:

```sh
php bin/console app:crear-admin
```

## Producción con Docker

Copia la plantilla de producción y completa sus variables:

```sh
cp .env.prod.example .env.prod
```

Configura un `RELEASE_TAG` único, el dominio real en `APP_DOMAIN`, un `APP_SECRET` aleatorio, `DATABASE_URL` y las credenciales `MYSQL_*`. `DATABASE_URL` debe usar el host `db`, la misma base, usuario y contraseña configurados para MariaDB, y conservar el parámetro de versión incluido en la plantilla. Configura además `HASHER_ENTROPY`, `HASHER_PADDING` y `HASHER_CUSTOM_ALPHABET`; conserva estables estos valores entre despliegues para mantener la compatibilidad de los identificadores existentes. Protege `.env.prod` y no lo publiques.

El DNS de `APP_DOMAIN` debe apuntar al servidor y los puertos 80 y 443 deben estar disponibles para Caddy. Valida la configuración y construye la imagen:

```sh
docker compose --env-file .env.prod -f docker-compose.prod.yml config --quiet
docker compose --env-file .env.prod -f docker-compose.prod.yml build app
```

Antes de aplicar migraciones, respalda la base de datos y los archivos persistentes. Revisa y aplica las migraciones manualmente:

```sh
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php bin/console doctrine:migrations:status --env=prod --no-debug
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php bin/console doctrine:migrations:migrate --no-interaction --env=prod --no-debug
```

Inicia los servicios:

```sh
docker compose --env-file .env.prod -f docker-compose.prod.yml up -d
```

Con la aplicación y la base de datos disponibles, crea el administrador inicial:

```sh
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php bin/console app:crear-admin --env=prod --no-debug
```

MariaDB no publica un puerto al exterior. Conserva los volúmenes de Docker durante los despliegues para mantener la base de datos y los archivos.

## Archivos de configuración

- `docker-compose.yml`: entorno de desarrollo con el código montado y dependencias de desarrollo.
- `docker-compose.prod.yml`: servicios de producción con Caddy y almacenamiento persistente.
- `.env.local.example`: plantilla para desarrollo local.
- `.env.prod.example`: plantilla para producción.
