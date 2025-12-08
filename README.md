# Tech-Store (Laravel)

A modern e-commerce platform for IT products, built with Laravel. Features include:

- Product, category, cart, order, payment, and wishlist management
- Sales reports dashboard with live analytics, charts, and filters
- Export sales reports to Excel and PDF 
- QR code payment integration (Bakong, KHQR)
- Social login (Google, Facebook, etc.)
- User authentication and roles
- Queue and notification system
- Modern testing with Pest

---

## Composer Packages

**Production:**
- `laravel/framework`: Laravel core
- `maatwebsite/excel`: Excel export for reports
- `barryvdh/laravel-dompdf`: PDF export for reports
- `laravel/socialite`: Social login integration
- `laravel/tinker`: REPL for Laravel
- `chamroeuntam/bakong-khqr-image`, `khqr-gateway/bakong-khqr-php`, `endroid/qr-code`: QR code and payment gateway support

**Development:**
- `fakerphp/faker`: Fake data for testing
- `laravel/breeze`: Simple authentication scaffolding
- `laravel/pail`, `laravel/pint`, `laravel/sail`: Dev tools (linting, Docker, etc.)
- `mockery/mockery`: Mocking for tests
- `nunomaduro/collision`: Error handling
- `pestphp/pest`, `pestphp/pest-plugin-laravel`: Modern testing framework

---

## Commands

- `php artisan queue:work`: Run the queue worker
- `php artisan queue:listen`: Listen for queue jobs
- `php artisan queue:failed`: View failed jobs
- `php artisan queue:retry`: Retry failed jobs
- `php artisan queue:flush`: Flush the queue
- `php artisan queue:clear`: Clear the queue
- `php artisan queue:info`: Get queue information
- `php artisan queue:status`: Get queue status
- `php artisan queue:restart`: Restart the queue
- `php artisan queue:terminate`: Terminate the queue
