# Soli — boletim e gestão para igrejas

**Soli** (*Soli Deo Gloria* — glória somente a Deus) é o **projeto Soli para igrejas**: boletim, comunhão e gestão, com a identidade da congregação (nome do templo, logo, ministérios) controlada no painel.

A Fase 1 entrega o boletim público do **mês vigente** (timezone `America/Fortaleza`). A Fase 2 entrega o painel `/admin`. A Fase 3 entrega a API pública `/api/v1` e as notas de produção.

Diretrizes completas: [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md).

O dashboard estático original ficou em `resources/legacy-bulletin/`.

## Requisitos

- Docker e Docker Compose
- Não é necessário ter PHP, Composer, Postgres ou Redis na máquina

## Como iniciar

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Se o `.env` já existir, pule o `cp`. O `key:generate` só precisa rodar uma vez.

## URLs locais

| Serviço | URL |
| --- | --- |
| Site | http://localhost:8000 |
| Admin | http://localhost:8000/admin |
| API (mês vigente) | http://localhost:8000/api/v1/bulletins/current |
| API (mês específico) | http://localhost:8000/api/v1/bulletins/2026/8 |
| Healthcheck | http://localhost:8000/up |
| Mailpit (e-mail de dev) | http://localhost:8025 |
| PostgreSQL | `localhost:15432` (user/senha/db: `soli` / `secret` / `soli`) |
| Redis | `localhost:16379` |

Bancos Postgres no mesmo servidor:

| Ambiente | Database | Quem usa |
| --- | --- | --- |
| Desenvolvimento | `soli` | site, fila, scheduler (`APP_ENV=local`) |
| Teste | `soli_test` | só `php artisan test` (`APP_ENV=testing`) |

Timezone da aplicação: **America/Fortaleza**.

Dentro da rede Docker, a aplicação usa `postgres:5432` e `redis:6379`. As portas `15432` e `16379` são só para ferramentas no host (DBeaver, TablePlus, etc.). Se colidirem, altere `FORWARD_DB_PORT` e `FORWARD_REDIS_PORT` no `.env`.

## Comandos úteis

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose exec app php artisan tinker
docker compose logs -f nginx app queue
docker compose down
```

Toda fase deve manter `php artisan test` verde. Demandas novas entram com teste (ver [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md) § 4.4).

Os testes rodam com `APP_ENV=testing` no banco **`soli_test`** (Postgres separado). O banco de desenvolvimento `soli` não é apagado. Cache, sessão e fila de teste usam `array`/`sync`, não o Redis da aplicação.

Branch e commit de cada fase seguem Conventional Commits, com o **slug do título da issue** (ex.: issue #2 → `feat/2-fase-1-boletim-home`). A IA só sugere; o responsável cria a branch e commita (ver [docs/PLANEJAMENTO.md](docs/PLANEJAMENTO.md) § 10).

Dados do Postgres ficam no volume Docker `postgres_data`. `docker compose down -v` apaga o banco.

## Acesso administrativo

O painel Filament fica em `/admin`. Depois do seed (papéis `admin` e `editor`), crie o primeiro usuário:

```bash
docker compose exec app php artisan make:filament-user
```

O primeiro usuário recebe o papel `admin` automaticamente. Os demais são criados no próprio painel, em **Usuários**.

Papéis:

- `admin` — boletins, congregação, usuários e auditoria
- `editor` — boletins, congregação e consulta da auditoria (sem gerir usuários)

A auditoria registra criações, alterações, exclusões e publicações, com usuário, campos alterados, IP e user-agent. Os logs não podem ser editados nem apagados pela interface.

Uploads da logo da congregação usam o disco `public`. O `storage:link` precisa existir para a logo aparecer no site.

## API v1

Os GETs de boletim **publicado** são públicos (sem token). O Sanctum está instalado para o app autenticar depois; ainda não há rotas que exijam `Authorization`.

| Método | Rota | Uso |
| --- | --- | --- |
| `GET` | `/api/v1/bulletins/current` | Boletim publicado do mês vigente em `America/Fortaleza` |
| `GET` | `/api/v1/bulletins/{year}/{month}` | Boletim publicado de um mês específico (histórico) |

Resposta `200` em JSON (`data`): congregação (nome, PIX, logo), tema, programação, eventos, escalas, culto infantil, EBD, aniversariantes e KPIs derivados. Rascunho, mês inexistente ou mês inválido (fora de 1–12) devolvem `404`.

Exemplo:

```bash
curl -s http://localhost:8000/api/v1/bulletins/current
curl -s http://localhost:8000/api/v1/bulletins/2026/8
```

## Produção

Use o mesmo Docker Compose da Fase 0, **sem Mailpit**. Suba só o que a aplicação precisa:

```bash
docker compose up -d app nginx postgres redis queue scheduler
```

Não inicie `mailpit`. Não publique `15432` (Postgres) nem `16379` (Redis) na internet; no servidor, remova os `ports:` desses serviços ou deixe-os só na rede Docker.

### HTTPS, URL e ambiente

- Coloque um proxy reverso (Caddy, Traefik ou Nginx no host) na frente do `nginx` do Compose, com certificado TLS.
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seu-dominio` (sem barra no final)
- `APP_PORT` só precisa ser acessível pelo proxy, não pela internet aberta

### Segredos

Fora do Git. No servidor, o `.env` (ou secrets do orquestrador) deve ter valores únicos:

- `APP_KEY` (`php artisan key:generate` uma vez, no servidor)
- `DB_PASSWORD` forte para o PostgreSQL 17
- `REDIS_PASSWORD` se o Redis não estiver isolado só na rede Docker

Nunca commite `.env`, dumps com senha, ou tokens Sanctum.

### Workers e scheduler

Os serviços `queue` (`php artisan queue:work`) e `scheduler` (`php artisan schedule:work`) precisam estar **sempre ligados** em produção. O Compose já os define com `restart: unless-stopped`. Sem eles, filas e tarefas agendadas não rodam.

Depois de cada deploy:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

Healthcheck HTTP: `GET /up` deve responder 200.

### Backup

PostgreSQL 17, periódico, fora do volume Docker:

```bash
docker compose exec postgres pg_dump -U soli -d soli -Fc > soli-$(date +%Y%m%d).dump
```

Guarde o arquivo em outro disco ou bucket. Teste o restore de vez em quando (`pg_restore`). `docker compose down -v` apaga o volume `soli_postgres` — não use `-v` em produção.

### E-mail

Mailpit é só desenvolvimento. Em produção configure `MAIL_*` para um SMTP próprio (ou relay da VPS). Nada nesta stack é pago por obrigação do produto.

### Deploy sugerido

VPS + Docker Compose (self-hosted). Sem amarrar a nuvem paga. HTTPS no proxy, backup com `pg_dump`, workers ligados, secrets fora do Git.

## Stack desta fase

- Laravel 13 / PHP 8.4-FPM
- Filament 5 (`/admin`)
- Laravel Sanctum (tokens, pronto para o app)
- Nginx
- PostgreSQL 17
- Redis 7 (cache, sessão e fila)
- Mailpit (somente desenvolvimento)
- Spatie Permission (`admin`, `editor`) e Activitylog

Nada nesta stack é pago.
