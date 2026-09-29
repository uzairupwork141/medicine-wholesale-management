# Wholesale Medical Management — Clean Schema Update

## What changed

1. Customers no longer have `credit_limit` or `opening_balance`.
2. Medicine master no longer has `mrp`, `default_sale_price`, or `reorder_level`.
3. Batch pricing remains batch-level: `purchase_price`, `sale_price`, and `mrp` stay on `batches` because price/expiry/stock are batch-specific.
4. Sales can be fully paid, partially paid, or completely unpaid. There is no customer credit-limit check.
5. Invoice discount is a percentage (0–100) and the database stores that percentage in `sales.invoice_discount`.
6. Admin can filter sales by payment status.
7. Admin has a Customer Pending Payments page that aggregates outstanding balances across all sales.
8. Customer payments are recorded in `customer_payments` and allocated to the oldest pending/partial sales first through `payment_allocations`.
9. A single payment can automatically clear any number of sales. Example: if oldest dues are Rs. 2,000, Rs. 2,000, Rs. 1,000 and the customer pays Rs. 3,000, the first sale becomes Paid and the second becomes Partial with Rs. 1,000 due.
10. Payment allocation is transactional and uses row locks, so a concurrent payment cannot allocate the same outstanding balance twice.
11. A sale with recorded payment allocations cannot be edited or deleted, preserving payment history and stock consistency.
12. Sales posting locks selected batches and changes stock in the same database transaction as the sale.
13. Admin-only routes are protected server-side; hiding menu items is not used as the security boundary.
14. Login only succeeds for active users and login attempts are rate-limited.
15. The last active administrator cannot be removed/deactivated, and an administrator cannot remove/deactivate their own account.

## Fresh migration setup

This package intentionally contains a new clean migration set. It is intended for a fresh database.

**Back up any existing database before proceeding.**

1. Create/select the `wholesale_medical` database.
2. Do not keep old migration files in `database/migrations`.
3. Copy this project's migration files into `database/migrations`.
4. Configure `.env` locally from `.env.example`; do not copy a real `.env` from another machine.
5. Run:

```powershell
php artisan optimize:clear
php artisan migrate:fresh --seed
```

`migrate:fresh` drops the existing application tables before recreating them. It is intentionally used here because obsolete columns must disappear from the schema.

## Seeded accounts

Admin:
- Email: `admin@example.com`
- Password: `password`

Seller:
- Email: `seller@example.com`
- Password: `password`

Change these passwords before using the application outside local development.

## Seeded payment scenario

`ABC Medical Store` has five historical sales. Three pending sales have a combined outstanding balance of Rs. 5,000. This is included so the automatic multi-sale payment allocation can be tested immediately.

## Important

No real `.env` file is included in this package. Never commit production credentials.
