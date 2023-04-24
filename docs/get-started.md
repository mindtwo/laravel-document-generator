# Get Started

## Introduction

> Simply generate documents from your models

This package adds a simple, external dependencyless way of
generating a document and output it as a pdf inside your laravel
projects.

## Features

- Add documents to a model type 📃
- Simple system for you to structure documents through blocks 📌
- Retrieve data for placholders inside your blocks dynamically 📡
- Output documents as pdf 🖨️
- Store generated documents in database to recreate them even if
the models data changed 💾
- Comes with blade template support and database support out-of-the box 📦
- Define custom layouts and templates 💅

## Installation

To install the package via composer simply run

```
composer require mindtwo/document-generator
```

inside your projects root directory.

After the installation was successful you need to publish the packages migrations and config file. To achieve that simply run

```
php artisan vendor:publish migrations
php artisan vendor:publish documents
```

To finish the installation simply run

```
php artisan migrate
```
