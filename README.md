# Project Setup Documentation

## 1. Install Composer

To install Composer, follow the instructions on the [official Composer website](https://getcomposer.org/download/).

## 2. Update Composer

To update Composer to the latest version, run:

composer self-update

## 3. Connect to MySQL Database

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assignment
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password


## 4. Migrate and Seed the Database
# This command will run all outstanding migrations to update your database schema.
php artisan migrate

# This command will populate your database with seed data.
php artisan db:seed


## 5. Start the Laravel Development Server
## This command will start the Laravel development server.
php artisan serve   


```bash
