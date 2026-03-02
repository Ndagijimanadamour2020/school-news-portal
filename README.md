# School News Portal

A modern, secure news portal for schools to share updates, announcements, and engage with the community.

## Features

- Public news listing with categories, search, and pagination
- Article detail page with comments
- Announcements with file attachments (PDF, Word, Excel)
- Admin panel to manage news, categories, comments, users, and announcements
- Secure authentication with password hashing and CSRF protection
- Responsive UI using Bootstrap 5
- File upload validation
- Prepared statements against SQL injection
- XSS protection via output escaping

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- XAMPP/WAMP/MAMP or any local server

## Installation

1. **Clone or download** the project into your web root (e.g., `C:\xampp\htdocs\school-news-portal`).

2. **Create a database** named `school_news` (or import the provided `school_news.sql`).

3. **Configure database connection** in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'school_news');
   ```

4. **Run Database Update for Monetization:**
   Open your browser and visit: `http://localhost/school-news-portal/update_monetization.php`. This will add the necessary tables and columns for premium content and payments.

5. **Set up Flutterwave Keys:**
   - Sign up for a [Flutterwave](https://flutterwave.com) account and get your **Public Key** and **Secret Key**.
   - Open `initiate_payment.php` and `payment_callback.php`.
   - Replace the placeholder keys with your actual keys.
   ```php
   // initiate_payment.php
   $p_key = "YOUR_PUBLIC_KEY";
   
   // payment_callback.php
   $s_key = "YOUR_SECRET_KEY";
   ```

## Admin Access

The default admin login credentials are:
- **Username:** `admin`
- **Password:** `admin123` (Note: Ensure you change this in production!)