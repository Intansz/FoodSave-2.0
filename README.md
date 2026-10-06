# FoodSave

FoodSave is a web-based surplus food marketplace that connects student consumers with local food businesses offering surplus food at affordable prices.

The platform aims to reduce food waste while helping partner businesses sell surplus products.

## Features

### Consumer

- Browse and explore available surplus food products.
- View product details and partner information.
- Place orders and track order status.
- View notifications and order information.

### Merchant

- Manage product listings and availability.
- Manage incoming orders.
- View sales and transaction information.

### Administrator

- Manage and review merchant accounts.
- Monitor platform activity.
- Review service-fee settlements.

## Technology Stack

- **Backend:** Laravel 12
- **Frontend:** Blade, JavaScript, and Vite
- **Database:** MySQL
- **Styling:** Project frontend assets
- **Development environment:** PHP, Composer, Node.js, and npm

## Requirements

Install the following before setting up the project:

- PHP and required Laravel PHP extensions
- Composer
- Node.js and npm
- MySQL
- A local web server environment, such as XAMPP

## Installation

1. Clone or download the repository.

2. Open a terminal in the project directory.

3. Install PHP dependencies:

   ```bash
   composer install
   ```

4. Install frontend dependencies:

   ```bash
   npm install
   ```

5. Create the local environment file:

   ```bash
   cp .env.example .env
   ```

   On Windows PowerShell, use:

   ```powershell
   Copy-Item .env.example .env
   ```

6. Create a MySQL database named `foodsave`, then configure the database credentials in `.env`.

7. Generate the application key:

   ```bash
   php artisan key:generate
   ```

8. Run database migrations:

   ```bash
   php artisan migrate
   ```

9. Create the public storage link:

   ```bash
   php artisan storage:link
   ```

10. Build the frontend assets:

    ```bash
    npm run build
    ```

## Running Locally

Start the Laravel development server:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser.

For frontend development with Vite, run this in a separate terminal:

```bash
npm run dev
```

## Testing

Run the automated test suite:

```bash
php artisan test
```

## Configuration and Security

- Keep `.env` private. Never commit database passwords, application keys, or other secrets.
- `.env.example` provides a template for local configuration.
- Configure production environment variables, database credentials, HTTPS, and debugging settings before deployment.
- Demo data should only be seeded in a local development environment.

## Business Model

FoodSave charges partner businesses a service fee on eligible transactions. Product payments are made directly by consumers to partner businesses; FoodSave does not hold the product transaction funds. Service fees are recorded for periodic settlement.

## Project Status

FoodSave is under development. Features and setup instructions may change as the project evolves.
