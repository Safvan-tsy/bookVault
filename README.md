# BookVault - Library Management System


## Tech Stack

- **Backend**: php, Laravel 11
- **Frontend**: Blade, Tailwind CSS
- **Database**:MySQL

## Installation

### Prerequisites
- PHP 8 or higher
- Composer
- Node.js & NPM
- MySQL

### Setup Instructions

1. **Clone repository**
   ```bash
   git clone https://github.com/Safvan-tsy/bookVault.git
   cd book-vault
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   
   - Create a MySQL database `book_vault`
   - Update `.env` with your MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=book_vault
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Gmail SMTP setup for email service**
     
     Gmail SMTP Configuration:
     
     Setup Requirements:
     1. Enable 2-Factor Authentication on Gmail account
     2. Generate App Password: Google Account > Security > App passwords
     
     Env Variables:
      MAIL_MAILER=smtp
      MAIL_HOST=smtp.gmail.com
      MAIL_PORT=587
      MAIL_USERNAME= // your_email>
      MAIL_PASSWORD= // app password
      MAIL_ENCRYPTION=tls
      MAIL_FROM_ADDRESS= // your_email>
      MAIL_FROM_NAME="${APP_NAME}"
 

7. **Run Migrations and Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Start the Application**
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

## Sample Credentials

After running the seeders, you can use these sample accounts:

### Admin Account
- **Email**: admin@bookvault.com
- **Password**: password
- **Role**: Administrator

### Member Account
- **Email**: member@bookvault.com
- **Password**: password
- **Role**: Member

## Queue System

The application uses Laravel's queue system for background tasks:

1. **Start the Queue Worker**
   ```bash
   php artisan queue:work
   ```

2. **Schedule Overdue Notifications**
   ```bash
   php artisan schedule:work
   ```

## Testing

Run the feature tests:
```bash
php artisan test
```

The test suite includes:
- Book borrowing and returning functionality

---
