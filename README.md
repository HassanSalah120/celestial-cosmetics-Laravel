# Celestial Cosmetics

![Celestial Cosmetics Banner](public/images/logo/logo.png)

Celestial Cosmetics is a modern, feature-rich e-commerce platform for selling cosmetic products, built with the Laravel framework. It provides a complete solution for managing products, orders, customers, and more.

## Features

- **Product Management**: Easily add, edit, and manage products and categories.
- **Order Processing**: A seamless checkout experience with order and payment tracking.
- **Customer Accounts**: Users can create accounts, manage their profiles, and view order history.
- **Admin Dashboard**: A powerful admin panel for managing the entire store.
- **Responsive Design**: A beautiful and responsive design that works on all devices.
- **Multi-language Support**: Easily translate your store into multiple languages.

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/celestial-cosmetics.git
   cd celestial-cosmetics
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Set up your environment file:**
   - Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```
   - Configure your database and other services in the `.env` file.

4. **Run database migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Build your assets:**
   ```bash
   npm run dev
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

## License

This project is open-source and available under the [MIT License](LICENSE).
