# ICVB — Boletim mensal

Sistema web da Igreja Congregacional Vale da Benção. A Fase 0 entrega o esqueleto **Laravel 13 + PHP 8.4 + PostgreSQL 17 + Redis**, já no Docker, para qualquer dev subir o mesmo ambiente.

Diretrizes completas: [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md).

O dashboard estático original ficou em `resources/legacy-bulletin/` e será ligado aos dados na Fase 1.

## Requisitos

- Docker e Docker Compose
- Não é necessário ter PHP, Composer, Postgres ou Redis na máquina

## Como iniciar

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Se o `.env` já existir, pule o `cp`. O `key:generate` só precisa rodar uma vez.

## URLs locais

| Serviço | URL |
| --- | --- |
| Site | http://localhost:8000 |
| Healthcheck | http://localhost:8000/up |
| Mailpit (e-mail de dev) | http://localhost:8025 |
| PostgreSQL | `localhost:15432` (user/senha/db: `icvb` / `secret` / `icvb`) |
| Redis | `localhost:16379` |

Bancos Postgres no mesmo servidor:

| Ambiente | Database | Quem usa |
| --- | --- | --- |
| Desenvolvimento | `icvb` | site, fila, scheduler (`APP_ENV=local`) |
| Teste | `icvb_test` | só `php artisan test` (`APP_ENV=testing`) |

Timezone da aplicação: **America/Fortaleza**.

Dentro da rede Docker, a aplicação usa `postgres:5432` e `redis:6379`. As portas `15432` e `16379` são só para ferramentas no host (DBeaver, TablePlus, etc.). Se colidirem, altere `FORWARD_DB_PORT` e `FORWARD_REDIS_PORT` no `.env`.

## Comandos úteis

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan test
docker compose exec app php artisan tinker
docker compose logs -f nginx app queue
docker compose down
```

Toda fase deve manter `php artisan test` verde. Demandas novas entram com teste (ver [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md) § 4.4).

Os testes rodam com `APP_ENV=testing` no banco **`icvb_test`** (Postgres separado). O banco de desenvolvimento `icvb` não é apagado. Cache, sessão e fila de teste usam `array`/`sync`, não o Redis da aplicação.

Branch e commit de cada fase seguem Conventional Commits; a IA só sugere nome e mensagem — o responsável cria a branch e commita (ver [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md) § 10).

Dados do Postgres ficam no volume Docker `postgres_data`. `docker compose down -v` apaga o banco.

## Acesso administrativo

O painel `/admin` entra na **Fase 2** (Filament). Quando existir:

```bash
docker compose exec app php artisan make:filament-user
```

Papéis previstos: `admin` (tudo) e `editor` (boletins). Auditoria de alterações também é Fase 2.

Até lá, o critério de saúde do stack é `GET /up` retornar HTTP 200.

## Produção (resumo)

Detalhamento completo na Fase 3. Para um VPS com Docker:

- Use o mesmo Compose, **sem Mailpit**
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` com HTTPS
- Segredos (`APP_KEY`, senha do Postgres, Redis) fora do Git
- Mantenha `queue` e `scheduler` ligados
- Backup: `pg_dump` periódico do PostgreSQL 17
- Não publique as portas 15432/16379 na internet

## Stack desta fase

- Laravel 13 / PHP 8.4-FPM
- Nginx
- PostgreSQL 17
- Redis 7 (cache, sessão e fila)
- Mailpit (somente desenvolvimento)

Nada nesta stack é pago.
