
# Iussing Asset System

This is project is developed to assist the process of issuing assets to staff and faculty at AUPP.

## Features

- Manage Users and assets
- Manage the issuance and returns of assets
- Print out and email a list of assets issued to a particular Users
- Review recents activities

## Requirements

- Php 8.2 or higher
- Composer
- Mysql
- Node.js

## Installation

1. Clone this repository:
```
git clone https://github.com/MOKMonyratanak/AUPPInventory.git
cd AUPPInventory
```

2. Create a `.env` file in the project root. Copy the sample from `.env.example` and adjust it according to your specific setup.

3. Install dependencies
```
composer install
npm install
```

4. Generate an encryption key
```
php artisan key:generate
```

5. Run migration to create database
```
php artisan migrate
```

6. For local development, run:
```
npm run dev
php artisan serve
```

7. For production, run:
```
npm run build
```

8. Login with the following default admin account:
```
Email: admin@gmail.com
Password: 00000000
```

9. Create your required company and position in the setting.

10. Change the default email, password, and make adjustment to the admin profile accordingly.

## Known Issues
- Webpage reload with each click.

## Acknowledgements

This project uses the following open-source libraries and frameworks:

- [Laravel Framework](https://laravel.com/): A powerful and elegant PHP framework that powers this application.
- [Bootstrap](https://getbootstrap.com/): A popular CSS framework for building responsive and modern web designs.
- [FontAwesome](https://fontawesome.com/): A toolkit for vector icons and social logos.
- [jQuery](https://jquery.com/): A fast, small, and feature-rich JavaScript library.
- [Select2](https://select2.org/): A jQuery-based replacement for select boxes.

Special thanks to the Laravel community for their continuous support and updates.
