# Money Tracker API (Laravel)

Backend-only API for managing users, wallets, and transactions.

## Requirements
- PHP 8.2+ (tested with 8.3)
- Composer
- A database (SQLite/MySQL/Postgres)

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, then run:
```bash
php artisan migrate
```

## API Endpoints
Base URL: `/api`

### Users
- `POST /api/users`
  - Body: `name` (string, required), `email` (string, required)
- `GET /api/users/{user}`
  - Returns user profile with wallets, each wallet balance, and total balance

### Wallets
- `POST /api/users/{user}/wallets`
  - Body: `name` (string, required), `description` (string, optional)
- `GET /api/wallets/{wallet}`
  - Returns wallet details, wallet balance, and all transactions

### Transactions
- `POST /api/wallets/{wallet}/transactions`
  - Body: `type` (`income|expense`, required), `amount` (positive number, required), `description` (string, optional)

## Balance Rules
- Income adds to balance
- Expense subtracts from balance

## Notes
- No authentication is required per assessment instructions.
