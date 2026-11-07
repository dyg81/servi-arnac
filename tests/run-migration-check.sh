#!/bin/sh
set -eu

case "${DATABASE_URL:-}" in
    */main_test\?*|*/main_test) ;;
    *) echo "DATABASE_URL debe apuntar a la base aislada main_test." >&2; exit 2 ;;
esac

php tests/prepare_migration_schema.php
php bin/console doctrine:migrations:migrate --no-interaction --env=test --no-debug
php bin/console doctrine:migrations:execute \
    DoctrineMigrations\\Version20220707022505 \
    DoctrineMigrations\\Version20220625213101 \
    DoctrineMigrations\\Version20220625210730 \
    --down --no-interaction --env=test --no-debug
php bin/console doctrine:migrations:migrate --no-interaction --env=test --no-debug
php bin/console doctrine:schema:update --force --env=test --no-debug
php bin/console doctrine:schema:validate --env=test --no-debug
