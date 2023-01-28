# robusta-bus-booking
The goal of this project is to implement a building a fleet-management system

- **table of contents :**
  

- [requirements](#requirements)
- [installation guide](#installation-guide)

# requirements:
- laravel 9 
- php 8.0
- mysql 8
- composer v2

# installation guide
- navigate to the project folder after cloning and run the following command in the terminal `cp .env.example .env`
  

- update the following keys in the .env to match your environment
  - `APP_URL`

- generate the application encryption key with `php artisan key:generate`
  

- install the project backend dependencies with `composer install`
  

- run the migration and seed the admin data with `php artisan migrate --seed`
