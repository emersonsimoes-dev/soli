#!/bin/bash
set -euo pipefail

# Roda só na primeira inicialização do volume Postgres.
# Banco exclusivo do PHPUnit — equivalente ao RAILS_ENV=test / database.yml test.
TEST_DB="${POSTGRES_DB}_test"

if ! psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -tc "SELECT 1 FROM pg_database WHERE datname = '${TEST_DB}'" | grep -q 1; then
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" -c "CREATE DATABASE ${TEST_DB}"
fi
