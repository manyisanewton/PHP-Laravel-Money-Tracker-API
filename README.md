# Money Tracker API

Backend-only Money Tracker API built with Laravel. It supports users, wallets, and transactions with correct balance calculations and clear JSON responses.

## Features
- Create users (no authentication required)
- Create multiple wallets per user
- Add income/expense transactions
- View user profile with per-wallet balances and total balance
- View a wallet with balance and paginated transactions
- Validation, soft deletes, and consistent error responses

## Tech
- Laravel 12
- PHP 8.3
- SQLite/MySQL/Postgres supported

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`, then run:
```bash
php artisan migrate
```

## Run Tests
```bash
php artisan test
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
  - Body: `name` (string, required), `description` (string, optional), `currency` (3-letter code, optional, default `USD`)
- `GET /api/wallets/{wallet}`
  - Returns wallet details, wallet balance, and paginated transactions
  - Query: `per_page` (1-100, default 15)

### Transactions
- `POST /api/wallets/{wallet}/transactions`
  - Body: `type` (`income|expense`, required), `amount` (positive number, required), `description` (string, optional)

## Balance Rules
- Income adds to balance
- Expense subtracts from balance

## Amount Storage
- API accepts decimal amounts (e.g., `12.34`).
- Values are stored in `amount_cents` to avoid floating-point errors.
- Responses include both `amount` and `amount_cents`.

## Error Format
Validation errors return:
```json
{
  "message": "Validation failed.",
  "errors": {
    "field": ["Error message"]
  }
}
```

Missing resources return:
```json
{ "message": "Resource not found." }
```

## Notes
- No authentication by requirement.
- Soft deletes enabled for wallets and transactions.
