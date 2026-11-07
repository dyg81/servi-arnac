# SAC ARNAC

Aplicación web interna para gestionar servicios archivísticos. Incluye registro de clientes, fondos, depósitos, libros y expedientes, y la tramitación de investigaciones, cartas de autorización, transcripciones, reprografía y certificaciones. Tiene perfiles por área, bitácora de actividad, reportes y estadísticas.

## Mapa del código

- `src/Controller/`: controladores web; `Registro/` administra el inventario archivístico y `Sala/` los servicios de atención.
- `src/Entity/`, `src/Repository/` y `src/Form/`: entidades Doctrine, consultas y formularios.
- `src/Security/`, `src/EventListener/`, `src/Service/` y `src/Extensions/`: autenticación, respuesta HTTP, servicios compartidos y extensiones Twig.
- `templates/`: vistas Twig organizadas por área funcional.
- `config/`: configuración de Symfony, Doctrine, seguridad, rutas y entornos.
- `migrations/`: migraciones Doctrine existentes; revisar su estado contra la base de datos antes de ejecutarlas.
- `public/assets/`: bibliotecas y recursos front-end; `public/uploads/` contiene ubicaciones de carga.
- `tests/`: suite unitaria y suite de integración separada; incluye pertenencia documental, carga, login, permisos, CSRF, validación del formulario y ciclo de migraciones en DB temporal.
- `Dockerfile` y `docker-compose.yml`: entorno de desarrollo Apache/PHP y MariaDB.

## Plataforma y dependencias

- Symfony 4.4, fijado por paquetes `4.4.*`; `symfony/framework-bundle` está en `4.4.51`.
- Composer declara PHP `>=7.1.3 <8.0` y resuelve dependencias con plataforma `7.4.33`.
- Doctrine ORM y rutas usan anotaciones. La modernización debe conservarlas.
- El Dockerfile parte de `php:7.4.33-apache` y usa Composer `2.8.12`; instala desde `composer.lock`. La imagen predeterminada omite dependencias de desarrollo.
- MariaDB 10.4, Apache, Twig y recursos JavaScript/CSS servidos desde `public/assets`.
- Symfony 4.4 y PHP 7.4 están fuera de soporte; se conservan por requisito del proyecto y esto mantiene un riesgo de seguridad.

### Auditoría de dependencias — referencia informativa

Instantánea obtenida el 2026-11-07 con `composer audit --locked --no-dev` sobre `composer.lock`: **47 advisories en 16 paquetes de producción**: 1 crítico, 10 altos, 11 medios, 10 bajos y 15 sin severidad asignada. El comando se ejecuta con el servicio Docker y `.env.local` para que Compose pueda interpolar las variables:

```sh
docker compose --env-file .env.local exec app composer audit --locked --no-dev
```

La lista siguiente registra los avisos que Composer asoció con las versiones bloqueadas. Es una referencia de riesgo del árbol de dependencias, no una prueba de que cada ruta vulnerable sea alcanzable desde la aplicación. Los avisos sin severidad conservan la clasificación que reportó Composer.

- `symfony/cache` **4.4.48**: CVE-2026-45073 — inyección SQL al limpiar `PdoAdapter`.
- `symfony/config` **4.4.44**: PKSA-9myg-zzdn-22rg — XXE/lectura de archivo local al procesar XML.
- `symfony/expression-language` **4.4.47**: CVE-2026-109449 — expresiones anidadas sin límite pueden agotar el proceso.
- `symfony/http-client` **4.4.51**: PKSA-yd1s-kk7g-k2j3 — envío de credenciales a otro origen; PKSA-gfw6-4rnd-4533 y CVE-2024-50342 — enumeración de direcciones/puertos internos mediante DNS rebinding.
- `symfony/http-foundation` **4.4.49**: CVE-2026-118514 — bypass de reglas de acceso con `HEAD`; PKSA-b2gg-vk1g-6d5q — host de URLs generado controlable por el cliente; CVE-2025-64500 — bypass limitado por `PATH_INFO`; CVE-2024-50345 — redirección abierta.
- `symfony/http-kernel` **4.4.51**: PKSA-3gxv-7xhq-mgmz — cabeceras reenviadas no confiables alteran host/esquema/puerto de URLs generadas.
- `symfony/mailer` **4.4.49**: CVE-2026-45068 — inyección de argumentos en `SendmailTransport`.
- `symfony/mime` **4.4.47**: CVE-2026-45070 — inyección en cabeceras MIME; CVE-2026-45067 — inyección CRLF en direcciones.
- `symfony/monolog-bridge` **4.4.43**: CVE-2026-45077 — deserialización de objetos PHP sin autenticación en el listener `server:log`.
- `symfony/process` **4.4.44**: CVE-2026-24739 y CVE-2024-51736 — escape incorrecto/hijack de comandos en Windows (MSYS2/Git Bash y `Process`).
- `symfony/routing` **4.4.44**: CVE-2026-118516 — coincidencia de host puede evadir control por ruta; CVE-2026-48784 — normalización de segmentos `../`; CVE-2026-45065 — requisito de ruta no anclado puede producir URL externa.
- `symfony/security-http` **4.4.50**: CVE-2026-118513 y CVE-2026-45063 — parsing/spoofing de DN en autenticación X509; CVE-2026-48489 — bypass del firewall mediante `failure_forward`.
- `symfony/serializer` **4.4.47**: CVE-2026-118515 — una petición puede crear `UploadedFile` apuntando a un archivo local; PKSA-nyd8-k74r-fsf2 — URI `data:` con esquema en mayúsculas puede abrir archivos locales.
- `symfony/validator` **4.4.48**: PKSA-68kf-8d6f-hdsy — bypass del filtro IP público/privado con IPv6 mapeada; CVE-2024-50343 — respuesta incorrecta si la entrada termina en salto de línea.
- `symfony/yaml` **4.4.45**: PKSA-nktx-f1n1-b4yd — parseo exponencial/cuadrático; CVE-2026-109413 — recursión sin límite; CVE-2026-45304 — expansión de alias consume memoria; CVE-2026-45305 — ReDoS; CVE-2026-45133 — agotamiento de pila.
- `twig/twig` **3.11.3**: CVE-2026-49981 — bypass de allow-list del sandbox; CVE-2026-48808/CVE-2026-46635 — bypass con `column`; CVE-2026-48805/CVE-2026-46636 — regresiones del estado del sandbox; CVE-2026-48806/CVE-2026-48807 — bypasses por `__toString()`; CVE-2026-46628 — `spaceless` marca salida como segura; **CVE-2026-46633 (crítico)** — inyección PHP mediante `{% use %}`; CVE-2026-47730 — XSS en el profiler; CVE-2026-46627 — agotamiento de recursos; CVE-2026-46638 — `sandbox/include` omite una comprobación; CVE-2026-24425 — posible bypass con source policy; CVE-2026-47732 — bypasses adicionales por coerción a cadena; CVE-2026-46634 — `template_from_string()` escapa el sandbox.

Composer reporta por separado estos paquetes abandonados: `behat/transliterator`, `doctrine/annotations`, `doctrine/cache`, `sensio/framework-extra-bundle` (sugiere Symfony), `symfony/debug` (sugiere `symfony/error-handler`), `symfony/inflector` (sugiere el componente String) y `symfony/security-guard`. Los paquetes pueden estar abandonados sin que eso equivalga a un advisory de seguridad; esta lista no los suma a los 47.

Symfony 4.4 llegó al fin de vida en noviembre de 2023 y PHP 7.4 también está fuera de soporte. Por decisión del proyecto se conserva esta plataforma. La actualización mayor de plataforma necesaria para consumir ramas corregidas no forma parte de esta estabilización; los advisories se dejan visibles como deuda/riesgo conocido y esta sección es informativa, no una declaración de que estén corregidos.

## Configuración y despliegue actual

`.env` contiene solo configuración pública no secreta. Los valores locales y secretos se guardan en `.env.local`, ignorado por Git. Usa `.env.local.example` como guía, establece un `APP_SECRET` aleatorio y credenciales locales propias; no reutilices valores de ejemplo en despliegues.

Para iniciar el entorno local, configura en `.env.local` `APP_ENV`, `APP_SECRET`, `DATABASE_URL`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD` y `MYSQL_ROOT_PASSWORD`; luego ejecuta:

```sh
docker compose --env-file .env.local up --build
```

Compose publica la aplicación en `http://localhost:8080` y MariaDB en el puerto `3307` de forma predeterminada. Los puertos pueden cambiar con `APP_PORT` y `DB_PORT`. La base usa el volumen persistente `db_data`; el código se monta para desarrollo y `vendor_data` conserva las dependencias instaladas en la imagen. La imagen local incluye dependencias de desarrollo. El build Docker predeterminado usa solo dependencias de producción.

El Dockerfile instala dependencias desde el lockfile sin ejecutar scripts que requieran secretos o base de datos durante el build; publica `public/` como raíz de Apache y habilita `mod_rewrite`. En despliegues, inyecta `APP_ENV=prod`, `APP_DEBUG=0`, `APP_SECRET` y `DATABASE_URL` desde el entorno/gestor de secretos. No publiques puertos de base de datos salvo que el despliegue lo requiera; usa volúmenes persistentes y backups gestionados por el entorno. No ejecutes migraciones automáticamente al construir la imagen.

`.env.test` conserva un secreto fijo exclusivamente para pruebas automatizadas; no debe usarse en despliegues.

## Comandos de referencia

Con PHP/Composer compatibles y dependencias instaladas:

```sh
composer install
php bin/console about
php bin/console doctrine:migrations:status
```

Para iniciar el entorno Docker de desarrollo (tras configurar `.env.local`):

```sh
docker compose --env-file .env.local up --build
```

Antes de aplicar migraciones, confirmar respaldo, conexión y estado de esquema. Ejecuta las pruebas con `vendor/bin/phpunit`; el resultado y el alcance comprobado de la suite se registran en `docs/ESTADO_TRABAJO.md`.

La suite unitaria no requiere DB. Para las pruebas HTTP/formulario y el ciclo de migraciones, usa la configuración temporal aislada: MariaDB corre en `tmpfs`, sin puertos expuestos ni volúmenes persistentes.

```sh
docker compose -p sac-test -f docker-compose.test.yml up -d --build
docker compose -p sac-test -f docker-compose.test.yml exec app sh tests/run-migration-check.sh
docker compose -p sac-test -f docker-compose.test.yml exec app php vendor/bin/phpunit --configuration phpunit.integration.xml.dist
docker compose -p sac-test -f docker-compose.test.yml down
```

El runner de migraciones rechaza una `DATABASE_URL` distinta de la base temporal `main_test` y una base que ya tenga tablas. Para repetir la comprobación, reinicia la pila con `down` y `up` (sin `-v`); la DB vive en `tmpfs` y se descarta con el contenedor.

Para auditar dependencias desde Compose, el archivo local debe pasarse explícitamente y el servicio se llama `app`:

```sh
docker compose --env-file .env.local exec app composer audit --locked
```

## Documentación de estabilización

El plan de trabajo, el estado, el inventario técnico y el pase entre chats viven en `docs/`. Esa carpeta está excluida de Git y se conserva localmente; `README.md` es documentación pública versionada.

### Seguridad web — seguimiento

Fase 5 se completó el 2026-11-07. Las mutaciones principales usan POST+CSRF; los flujos revisados comprueban algunas relaciones entre investigación y documento. Las cartas nuevas se almacenan en `var/private_uploads/cartas`; la ruta de descarga permite solo PDF bajo `/direccion` y `ROLE_DIRECTIVO`, sanea el nombre para la respuesta y usa el almacenamiento privado antes del fallback autenticado a `public/uploads/cartas`.

En el clon desechable `/tmp/sac-fase5-clon` se probaron las 9 cuentas (ocho roles y dos cuentas admin) con claves temporales solo en la copia. La matriz de 72 comprobaciones de área no produjo resultados inesperados; solicitudes con rol ajeno respondieron 403. El hash del dump no cambió tras la matriz. Un PDF registrado y presente solo en `public/uploads/cartas` respondió 200 como PDF a `ROLE_DIRECTIVO`, con nombre de `Content-Disposition` saneado; anónimo recibió 302 al login y los otros ocho usuarios 403. Apache devolvió 403 al acceso directo; el registro no PDF devolvió 404. Los rechazos por CSRF inválido, ID inexistente y relación investigación-documento cruzada no modificaron los datos del clon. Ver la matriz local `docs/MATRIZ_SEGURIDAD_FASE5.md`.

Los 18 documentos no PDF asociados a solicitudes se preservaron. El diseño vigente es de acceso compartido por rol de área: los usuarios con el mismo rol operan sobre la cola/entidades autorizadas para su área, con las restricciones de relaciones y estado implementadas. No se requiere propiedad individual para cerrar Fase 5. Como mejora futura, si cambian las necesidades del negocio, se podrá evaluar asignación por equipo/usuario y reglas de acceso por entidad/acción; no forma parte del diseño actual ni se implementó en esta fase. La matriz local documenta el alcance del control por rol y sus límites.

Las cargas nuevas de cartas aceptan PDF de hasta 10 MB, reciben un nombre aleatorio y se guardan en `var/private_uploads/cartas`; el movimiento fallido se propaga para que la operación no figure como exitosa. Los controladores registran excepciones de persistencia en el log de PHP y presentan un mensaje neutro. La validación de pertenencia de libros/expedientes a una investigación se comparte en `DocumentoInvestigacionValidator`.

La validación del servidor real queda para la revisión global del proyecto al concluir todas las fases. Fases 5 y 6 se completaron; el cierre de Fase 7 está registrado en la documentación local.

### Preparación del contenedor para despliegue

`docker-compose.yml` sigue siendo el entorno de desarrollo: activa dependencias de desarrollo, monta el workspace y publica MariaDB. `docker-compose.prod.yml` es el manifiesto separado para producción. Construye con `INSTALL_DEV=0`, no monta el código ni publica el puerto de MariaDB, conserva la base, los archivos privados y los archivos históricos en volúmenes nombrados, e incluye Caddy como proxy HTTPS. `.dockerignore` excluye secretos, `var/` y `public/uploads/`; los archivos históricos no se copian en la imagen.

Prepara un archivo de secretos local fuera del control de versiones y con permisos restrictivos:

```sh
cp .env.prod.example .env.prod
chmod 600 .env.prod
# Edita .env.prod: RELEASE_TAG único, APP_DOMAIN real y secretos/URL de DB aleatorios.
```

Los valores de ejemplo no sirven para desplegar. Inyecta `APP_SECRET`, `HASHER_ENTROPY`, `DATABASE_URL`, `MYSQL_PASSWORD` y `MYSQL_ROOT_PASSWORD` mediante el gestor de secretos de la plataforma o un `.env.prod` protegido; conserva estables `HASHER_ENTROPY`, `HASHER_PADDING` y `HASHER_CUSTOM_ALPHABET` para que los identificadores Hashids existentes sigan decodificando. `DATABASE_URL` debe usar el host `db`, el nombre/usuario/contraseña configurados y `serverVersion=mariadb-10.4.34`. DNS del dominio debe apuntar al servidor y los puertos 80/443 deben llegar a Caddy para que obtenga/renueve certificados. No publiques MariaDB.

Comandos de configuración, build, inspección y arranque (sin migración automática):

```sh
docker compose --env-file .env.prod -f docker-compose.prod.yml config --quiet
docker compose --env-file .env.prod -f docker-compose.prod.yml build --pull app
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php bin/console doctrine:migrations:status --env=prod --no-debug
# Después de respaldar DB y archivos, revisar el estado y aplicar manualmente si procede:
docker compose --env-file .env.prod -f docker-compose.prod.yml run --rm app php bin/console doctrine:migrations:migrate --no-interaction --env=prod --no-debug
docker compose --env-file .env.prod -f docker-compose.prod.yml up -d
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
```

Antes de migrar, respalda `db_data`, `private_uploads` y `legacy_uploads` con el mecanismo de backup del host/plataforma y verifica que los respaldos se puedan restaurar. Mantén los volúmenes en despliegues sucesivos. No ejecutes `down -v`. El build/configuración local no certifica un despliegue en el servidor real.

Fase 5 está cerrada. El acceso compartido por rol de área es el diseño vigente. La posible asignación por equipo/usuario queda como mejora futura, sujeta a una necesidad de negocio. La validación del servidor real se hará en la revisión final posterior a todas las fases.

Fases 6–8 se completaron en alcance local el 2026-11-07. Fase 8 cubre reglas de pertenencia, cargas, login, autorización por rol, CSRF, formulario y ciclo de migraciones en PHP 7.4.33. El límite de carga se fija en 10.000.000 bytes en formulario y servicio. Quedan advisories de seguridad porque se conserva Symfony 4.4/PHP 7.4; el despliegue real queda fuera de la validación local. Ver el registro en `docs/ESTADO_TRABAJO.md`.
