# Docker / Render instructions for Backend

Este arquivo descreve como construir e executar a API Laravel localmente com Docker e como proceder para deploy em Render usando o `Dockerfile` incluído.

Local (docker-compose)

1. Copie o `.env.example` para `.env` e ajuste variáveis (DB_HOST=db, DB_USERNAME, etc.).
2. Build e subir containers:

```bash
cd Backend
docker-compose build
docker-compose up -d

# (apenas uma vez) gerar key e rodar migrations
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

3. A API estará disponível em `http://localhost:8000`.

Notas sobre Render

- No painel do Render crie um novo Web Service do tipo Docker (Dockerfile).
- Configure as environment variables no dashboard do Render (APP_KEY, APP_ENV, APP_DEBUG=false, DB_* se usar um serviço de banco de dados externo, etc.).
- Render define a porta via a variável de ambiente `PORT`; o `Dockerfile` usa a variável `PORT` e roda `php artisan serve --port=${PORT}`.

Recomendações (produção):
- O `php artisan serve` não é uma solução de alto desempenho para produção. Para produção, prefira uma imagem com Nginx+PHP-FPM ou um setup com containers separados (Nginx + php-fpm) e um processo supervisor.
- Proteja o `.env` e configure secrets no painel do Render.
