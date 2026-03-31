# Deploy na Hostinger (Laravel)

## 1) Ambiente de produção (`.env`)
Defina no servidor:

```env
APP_ENV=production
APP_DEBUG=false
```

## 2) Evitar Vite dev server em produção
Se existir, apague o arquivo:

```bash
rm -f public/hot
```

Confirme também que existe:

```bash
public/build/manifest.json
```

## 3) Dependências e cache
Na pasta do projeto, execute:

```bash
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4) Verificação rápida
- Abra `/login` e confirme que não há caracteres quebrados.
- No código-fonte da página, não pode aparecer `http://[::1]:5173`.
- Verifique `storage/logs/laravel.log` após primeiro acesso.
