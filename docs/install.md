# Installatione

## Get Started

To get started with the Laravel Document Generator Package, follow the installation and setup instructions provided in the subsequent sections. We'll guide you through the process of integrating the package into your Laravel application and demonstrate how to generate your first document.

Embrace the power of Laravel and Blade to transform your document generation process, making it more efficient, customizable, and maintainable. Let's dive in!

## System Requirements

Before proceeding, ensure that your system meets the following requirements:

- PHP 7.3 or higher.
- Laravel 9.0 or higher.
- Composer for dependency management.

## Installation Steps

### Step 1: Install the Package via Composer

Begin by installing the package through Composer. Run the following command in your terminal in the root directory of your Laravel application:

```bash
composer require mindtwo/document-generator
```

### Step 2: Publish the migration files

The package requires migrations to work. To publish the migration files run:

```bash
php artisan vendor:publish migrations
```

This will create the migration files inside your `database/migrations` folder. After the creation of the migration file run:

```bash
php artisan migrate
```

### Step 3: Publish Configuration File (Optional)

The package includes a configuration file. If you need to modify the default settings, publish the configuration file to your application's config directory:

```bash
php artisan vendor:publish --provider="mindtwo\DocumentGenerator\Providers\DocumentGeneratorProvider"
```

### Step 4: Configure Environment (Optional)

If you proceeded with the default configuration you may want to specify the disk used by the package. Per default the package will use your apps `local` disk.

To change the disk add `DOCUMENT_DISK` to your .env file e.g.:

```
DOCUMENT_DISK=s3
```
