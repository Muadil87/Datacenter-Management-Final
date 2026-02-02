# PFMDEV - Facility & Resource Management System

A comprehensive web application for managing facility resources, reservations, maintenance schedules, and incident tracking built with **Laravel 12** and **Vite**.

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Key Models & Relationships](#key-models--relationships)
- [User Roles & Permissions](#user-roles--permissions)
- [API Routes](#api-routes)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## 🚀 Features

- **Resource Management**: Create, update, and manage facility resources (servers, equipment, etc.)
- **Resource Categories**: Organize resources by categories with detailed specifications
- **Reservation System**: Book and manage resource reservations with conflict detection
- **Maintenance Tracking**: Schedule and monitor maintenance activities for resources
- **Incident Management**: Report, track, and resolve incidents with automatic notifications
- **Logging System**: Comprehensive audit logs for all system activities
- **User Notifications**: Real-time notifications for reservations, incidents, and maintenance
- **Multi-Role System**: Admin, Manager, Internal users, and Guest roles
- **Social Authentication**: OAuth login support (Google, GitHub, etc.) via Laravel Socialite
- **Search & Filtering**: Advanced filtering for resources by specifications
- **Profile Management**: User profiles with customizable notification preferences
- **Dashboard**: Interactive dashboards for different user roles

## 🛠️ Tech Stack

### Backend

- **Laravel 12** - Modern PHP framework
- **PHP 8.2+** - Server-side language
- **Laravel Socialite 5.24** - OAuth authentication
- **Laravel Tinker** - REPL for PHP

### Frontend

- **Vite 7.0+** - Next generation frontend build tool
- **JavaScript (ES Module)** - Client-side logic
- **Axios** - HTTP client for API requests

### Database

- **MySQL/MariaDB** - Relational database

### Development Tools

- **PHPUnit 11.5+** - PHP testing framework
- **Mockery 1.6+** - Mock library for testing
- **Laravel Pint** - PHP code style fixer
- **Laravel Pail** - Real-time log viewer
- **Laravel Sail** - Docker development environment
- **FakerPHP** - Fake data generator for seeders

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2 or higher**
- **Composer** - PHP dependency manager
- **Node.js & npm** - JavaScript package manager
- **MySQL 5.7+** or **MariaDB**
- **Git** - Version control

## 📥 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/pfmdev.git
cd pfmdev
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## ⚙️ Configuration

Edit the `.env` file and configure the following:

```env
APP_NAME="PFMDEV"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pfmdev
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (for notifications)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=

# OAuth Configuration (optional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
```

## 🗄️ Database Setup

### 1. Create Database

```bash
mysql -u root -p
CREATE DATABASE pfmdev;
EXIT;
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Initial Data

```bash
php artisan db:seed
# or seed specific seeders:
php artisan db:seed --class=AdminSeeder
```

## 🏃 Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite development server
npm run dev
```

The application will be available at `http://localhost:8000`

### Production Build

```bash
# Build frontend assets
npm run build

# Optimize application for production
php artisan optimize
```

## 📁 Project Structure

```
PFMDEV-FINAL-VERSION/
├── .git/                 # Git repository data
├── .env                  # Local environment config
├── .env.example          # Environment template
├── .editorconfig         # Editor settings
├── .gitattributes        # Git attributes
├── .gitignore            # Git ignore rules
├── .phpunit.result.cache # PHPUnit cache
├── .venv/                # Local Python venv (if present)
├── app/
│   ├── Console/          # Artisan commands
│   │   └── Commands/
│   ├── Http/
│   │   ├── Controllers/  # Request handlers
│   │   └── Middleware/   # HTTP middleware
│   ├── Models/           # Eloquent models
│   │   ├── Incident.php
│   │   ├── Log.php
│   │   ├── Maintenance.php
│   │   ├── Notification.php
│   │   ├── Reservation.php
│   │   ├── Resource.php
│   │   ├── ResourceCategory.php
│   │   └── User.php
│   ├── Observers/        # Model event listeners
│   └── Providers/        # Service providers
├── artisan               # Artisan CLI
├── bootstrap/            # Framework bootstrap
│   └── cache/
├── composer.json         # PHP dependencies
├── composer.lock         # PHP dependency lockfile
├── config/               # Configuration files
├── database/
│   ├── factories/        # Model factories for testing
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── lang/                 # Language files
│   ├── en/
│   └── fr.json
├── package.json          # Node.js dependencies
├── package-lock.json     # Node.js lockfile
├── phpunit.xml           # PHPUnit configuration
├── public/               # Publicly accessible files
│   ├── css/
│   ├── images/
│   ├── js/
│   └── storage/
├── Rapport/              # Reports/Documentation
├── README.md
├── resources/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript
│   └── views/            # Blade templates
├── routes/
│   ├── console.php       # Console routes
│   └── web.php           # Web routes
├── scripts/              # Utility scripts
├── storage/              # Logs, cache, uploads
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/                # Test suite
├── vendor/               # Composer dependencies
├── video/                # Project videos
└── vite.config.js        # Vite configuration
```

## 🔗 Key Models & Relationships

### Resource Model

- **Relationships**:
    - `category()` - Belongs to ResourceCategory
    - `maintenances()` - Has Many Maintenance records
    - `reservations()` - Has Many Reservation records
    - `incidents()` - Has Many Incident records

- **Attributes**: name, state, cpu_cores, ram_gb, storage_gb, os, bandwidth_mbps, location, description

### Reservation Model

- Link between users and resources with booking dates

### Maintenance Model

- Track maintenance activities and schedules for resources

### Incident Model

- Report and manage issues with resources

### User Model

- Enhanced with roles, OAuth support, and notification preferences

### Notification Model

- Track notifications for users

## 👥 User Roles & Permissions

The system supports the following user roles:

| Role         | Permissions                                        |
| ------------ | -------------------------------------------------- |
| **Admin**    | Full system access, user management, configuration |
| **Manager**  | Resource management, maintenance scheduling        |
| **Internal** | View and reserve resources, report incidents       |
| **Guest**    | Read-only access to resource listings              |

## 🛣️ API Routes

### Public Routes

- `GET /` - Home page
- `GET /resources` - List all resources
- `GET /resources/ajax-filter` - Filter resources
- `GET /rules` - View system rules
- `GET /login` - Login page

### Authenticated Routes

- Resource management endpoints
- Reservation management
- Maintenance tracking
- Incident reporting
- Profile management

## ✅ Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ResourceTest.php

# Run with coverage
php artisan test --coverage
```

## 👨‍💻 Contributors

This project was developed by:

- **El kadi Monssif**
- **El bahlouli Adil**
- **El boualiti Oussama**
- **Chahid Zaid**

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For issues and questions, please open an issue on the GitHub repository.

https://github.com/MonssifElkadi/Projet_devWEB

**Last Updated**: January 2026
