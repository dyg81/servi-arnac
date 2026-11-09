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

Para crear el usuario administrador inicial (`admin`), ejecuta el comando desde la raíz del proyecto. Solicita una nueva contraseña sin mostrarla en pantalla y la codifica con el encoder `bcrypt` configurado en Symfony. Si ya existe el usuario `admin`, el comando informa el conflicto y no crea otro usuario.

```sh
php bin/console app:crear-admin
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

## Próxima etapa de mantenimiento del código

El 2026-11-07 se creó el commit `5866923` (`chore: cerrar estabilización de componentes y pruebas`), que cierra el estado acumulado de estabilización, configuración, contenedores y pruebas. No incluye los documentos de `docs/` (locales/ignorados) ni documentos históricos de `public/uploads/cartas/`; sí incluye la regla `public/uploads/.htaccess`. Fases 0–8 quedan cerradas en alcance local.

La Fase 9 se completó en alcance estático sobre ese commit. El inventario priorizado está en `docs/INVENTARIO_TECNICO.md`. Entre los hallazgos destaca el uso de valores persistidos en strings HTML para opciones de la cascada de investigación, además de consultas completas/repetidas en estadísticas y reportes y manejo de excepciones duplicado en controladores. Se revisaron los 126 Twig (34 contienen `<script>`) y los assets personalizados.

- **Fase 9 — Inventario y priorización de calidad:** completada. Hallazgos, ubicaciones, prioridades y lotes propuestos en el inventario técnico local.
- **Fase 10 — PHP y componentes Symfony:** completada en alcance local acotado. Estadísticas/reportes usan conteos y agrupaciones SQL en vez de materializar colecciones para contar; un clasificador compartido identifica violaciones conocidas de unicidad y FK sin invocar métodos inexistentes sobre `Exception` genérica. Se agregaron pruebas de caracterización. La política común de logging/transacciones y una extracción adicional de reglas de negocio quedan para un lote PHP posterior con invariantes/atomicidad definidos.
- **Fase 11 — Twig, HTML y JavaScript asociado:** completada en alcance local. Se corrigieron interpolaciones y DOM inseguro, se revisaron Select2/eventos, se extrajeron los sondeos de notificaciones, y se mejoraron estructura y nombres accesibles. Lint Twig pasa 126/126 y la prueba funcional aislada de las tres cascadas pasó 20 aserciones; regresión visual y con endpoints reales queda para Fase 13.
- **Fase 12 — `custom.css` y `custom.js`:** corregir problemas de estilos y scripts, duplicación y fragilidad, preservando comportamiento y compatibilidad de librerías.
- **Fase 13 — Regresión y cierre:** revisar los flujos afectados, ejecutar validaciones proporcionales, documentar resultados y proponer fases correctivas si quedan defectos.

El P1 de la cascada de investigación fue confirmado: JSON persistido se concatenaba como markup y jQuery lo interpretaba como HTML. En Fase 11, esa cascada y las dos cascadas de depósitos relacionadas pasaron a crear opciones como nodos DOM, asignando valores con `.val()` y etiquetas con `.text()`; las respuestas atrasadas ya no repueblan selects tras cambiar su selección padre. Se revisaron los seis endpoints de notificaciones: cada uno devuelve conteos enteros construidos con `count()` y `JsonResponse`; sus plantillas actualizan la campana con nodos DOM y asignan mensajes mediante `textContent`, sin `innerHTML`. En el barrido de las 34 plantillas con JavaScript inline se detectó que varios Select2 anulaban el escape de resultados para permitir íconos en placeholders; se retiró ese bypass y se conservaron placeholders de texto. Mensajes flash, errores de login, nombres para PDFs y datos de gráficas se codifican como JSON con `JSON_HEX_TAG`, `JSON_HEX_APOS`, `JSON_HEX_AMP` y `JSON_HEX_QUOT`. También se corrigieron selectores de `allowClear` que consultaban `document` en vez del control. La revisión HTML corrigió el `lang` del documento a español, añadió landmark `<main>`, salto al contenido, encabezado principal accesible y arregló un `<div>` anidado dentro de `<p>`. Los 94 usos de `form_widget` tienen ahora etiqueta asociada para lectores de pantalla; también se etiquetaron controles manuales, se añadió `scope="col"` a 176 encabezados, nombre a columnas de acción y tipo explícito a 24 botones de formulario. Las 12 imágenes tienen `alt` y todos los botones de solo icono tienen nombre accesible. `php bin/console lint:twig templates` pasa para las 126 plantillas; el host PHP 8.5 imprime deprecaciones de dependencias Symfony. Fase 11 se cierra en alcance local con validaciones estáticas y una prueba funcional aislada; no equivale a regresión contra endpoints/datos reales ni a revisión visual/assistiva. Esas verificaciones forman parte de Fase 13. La suite PHP se ejecutó con PHP 7.4.33 en contenedores locales; la validación del servidor real queda para la revisión global. Los cambios de Fase 10 quedaron registrados en el commit `eeeb40d`.

Fase 11 queda cerrada. El siguiente chat puede iniciar **Fase 12 — CSS y JavaScript personalizados** después de releer README y los cuatro documentos locales y verificar `git status`/HEAD. Al final de cada fase se actualizarán este README y los cuatro documentos locales de `docs/`. Antes de cada commit se mostrará el mensaje, contenido y motivo, y se esperará aceptación explícita.


### Continuación de Fase 11 — extracción de notificaciones — 2026-11-07

Se extrajeron los seis sondeos periódicos de notificaciones de sus fragmentos Twig a `public/assets/dist/js/notifications.js`. El layout lo carga después de `parent()`, por lo que jQuery ya está disponible y el DOM de notificaciones ya fue renderizado. Las plantillas exponen URL, tipo e índices/mensajes mediante atributos `data-*`; el asset actualiza conteos con nodos DOM y `textContent`. Se preservan las tres categorías y divisores de Sala, la espera inicial de un segundo y la repetición cada cinco minutos incluso tras error. No se extrajeron scripts de formularios/modales, cuyo ciclo de inicialización AJAX requiere validación de integración específica. `node --check`, lint Twig (126/126) y `git -c core.whitespace=cr-at-eol diff --check` pasan. No se realizó validación específica del sondeo en navegador; sus estados visuales cero/no cero quedan como regresión pendiente para Fase 13.


### Fase 11 — validación funcional de cascadas — 2026-11-07

Se ejecutó en Firefox un arnés HTML temporal que carga el JavaScript vigente de las tres cascadas (investigación, depósito de expediente y depósito de libro), jQuery 2.2.4 y respuestas AJAX simuladas; no usó la base de datos de la aplicación. Pasaron 20 aserciones: carga y selección de clientes/fondos/legajos/expedientes/libros y depósitos; limpieza/deshabilitación al cambiar cliente; limpieza de niveles descendientes y reemplazo de depósitos al cambiar fondo; texto literal de nombres con comillas y etiquetas HTML/`<script>`; y ausencia de nodos inyectados/ejecución de payload. En el arnés `.valid()` se sustituyó por un retorno afirmativo para aislar la lógica de cascada; la validación del formulario queda fuera de alcance. No se ejercitaron endpoints reales, datos reales ni respuestas retrasadas. No hubo cambios de código tras el commit `790b382`; los cambios de esta tanda son documentación.


### Cierre de Fase 11 — 2026-11-07

Fase 11 se cierra en alcance local: se corrigieron las tres cascadas para construir opciones DOM con texto seguro y evitar respuestas obsoletas; se revisaron interpolaciones, dependencias, selectores y errores en plantillas; se quitaron seis usos de `innerHTML` en notificaciones y se centralizaron los sondeos en un asset; además se corrigieron estructura semántica y etiquetas accesibles. Validación final: `php bin/console lint:twig templates --no-debug` (126/126), `node --check public/assets/dist/js/notifications.js`, diff-check y prueba en Firefox con fixture AJAX (20 aserciones sobre carga, selección, limpieza y nombres con comillas/markup, sin interpretación ni ejecución). La prueba aislada stubbeó `.valid()` y no llamó endpoints ni base real.

Quedan para Fase 13 la regresión en formularios/endpoints reales, las respuestas retrasadas y errores de red, inspección visual/assistiva de vistas y notificaciones, y verificación del servidor objetivo. No bloquean el cierre del alcance de refactor estático de Fase 11. Commits de la fase: `464cc9b`, `1598d85`, `f7d3037`, `80fe6fb`, `790b382` y documentación `4ab102f`. Próximo chat: iniciar solo Fase 12; no se inició aquí.

### Fase 12 — avance — 2026-11-08

Fase 12 se completó en alcance local. `custom.js` inicializa plugins opcionales solo si están presentes, inicializa `#sac` una sola vez, publica Toast sin binding léxico global y registra handlers idempotentes; `custom.css` ya no aplica un borde gris contradictorio al Select2 válido en modo oscuro. `node --check` y diff-check pasan. La validación en navegador y sus límites quedan registrados en el cierre siguiente. No iniciar Fase 13 automáticamente.

### Cierre de Fase 12 — 2026-11-08

Fase 12 completada en alcance local. En navegador con fixture temporal y assets/plugins locales se verificaron DataTables, Select2 válido (borde e icono verdes), autofocus de modal y cierre del menú de usuario. Se revisó responsive a 390×844 y 1280×800 sin desbordamiento horizontal y no hubo errores/avisos en consola. Login real cargó sin tabla; no se accedió a páginas autenticadas por falta de credenciales. El fixture se eliminó y no se tocaron datos. `node --check` y diff-check con CRLF tolerado pasan. La regresión integrada y cierre global quedan para Fase 13, que requiere instrucción nueva.

### Fase 13 — regresión y cierre técnico — 2026-11-08

Fase 13 completada en alcance local. En PHP 7.4.33 pasaron unidad (10 pruebas/17 aserciones), integración (8/36), lint Twig (126/126), ciclo up/down/up de tres migraciones y validación de mapping/esquema; también `node --check` de `custom.js` y `notifications.js` y diff-check. Con las ocho cuentas se comprobaron 64 accesos GET entre los ocho roles: acceso propio 200 y denegación ajena 403, sin desvíos. Los seis endpoints de notificación devolvieron JSON de conteos para el rol correspondiente y 403 para otros; los endpoints de cascada JSON se ejercitaron como AJAX.

La regresión detectó que la cascada de legajos lanzaba 500 cuando faltaba el parámetro `fondoid`; ahora devuelve `200 []` ante parámetro ausente, no numérico o fondo inexistente. Se añadió prueba de integración para los tres casos y se confirmaron también en la app local. La interfaz de login real cargó en Firefox; no se completó inspección visual autenticada/assistiva. Quedan para la revisión global la validación en servidor real y los advisories ya documentados de PHP/Symfony fuera de soporte. Cinco deprecaciones indirectas de Doctrine/Twig siguen presentes. El stack de prueba se detuvo sin `-v`.

El código y esta documentación quedaron registrados en el commit `edc41eaad32176803cca975c85761d4e1f7c157d` (`fix: handle invalid fund IDs in investigation cascade`).

## Sincronización pendiente del historial

El 2026-11-08 se reescribieron los mensajes de los 14 commits iniciales hasta el antiguo `origin/master` (`6737e84` → `ad0a784`). Por ello cambiaron también los hashes de todos sus commits descendientes; la rama local `master` conserva el mismo contenido de trabajo y tiene una referencia de respaldo local `codex/backup-before-message-rewrite`. El remoto todavía conserva el historial anterior. **No se ha ejecutado `git push`**. Cuando se autorice la actualización del remoto, verificar primero que `origin/master` siga en `6737e84` y publicar el historial local con `git push --force-with-lease origin master`. Esto reemplaza el historial compartido; no usar un push normal ni eliminar la referencia de respaldo antes de confirmar la sincronización.

### Corrección del mensaje de línea base

En una revisión posterior se detectó que el commit de línea base posterior al antiguo `origin/master` también debía seguir el formato acordado. Su mensaje ahora es `chore: establecer línea base del proyecto antes de la estabilización`. Como consecuencia, cambiaron nuevamente los hashes de ese commit y de todos sus descendientes. La rama de respaldo anterior a esta corrección es `codex/backup-before-baseline-message-fix`; sigue pendiente sincronizar el historial reescrito y no se ha ejecutado `git push`.
