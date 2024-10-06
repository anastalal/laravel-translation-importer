# Laravel Translation Importer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anastalal/laravel-translation-importer.svg?style=flat-square)](https://packagist.org/packages/anastalal/laravel-translation-importer)
[![Total Downloads](https://img.shields.io/packagist/dt/anastalal/laravel-translation-importer.svg?style=flat-square)](https://packagist.org/packages/anastalal/laravel-translation-importer)

## Introduction

The **Laravel Translation Importer** package is a simple and powerful solution to manage your application's translation keys efficiently. It scans your project for translation functions (`__()` and `trans()`) and automatically updates the translation files located in the `resources/lang` directory.

This package simplifies the process of maintaining translations, especially when new keys are introduced during development. It helps you avoid missing translation keys by automating the synchronization between your project and language files.

## Features

- **Automatic scanning**: The package scans your project for translation keys inside `__()` and `trans()` functions.
- **File generation**: It generates and updates translation files in `resources/lang`.
- **CLI Commands**: Use artisan commands to sync missing keys or update existing ones.
- **Supports Multiple Languages**: The package is compatible with multi-language projects and works seamlessly across different locales.

## Installation

You can install the package via composer:

```bash
composer require anastalal/laravel-translation-importer
```

After installing, you need to publish the configuration file using:

```bash
php artisan vendor:publish --provider="Anastalal\LaravelTranslationImporter\TranslationImporterServiceProvider"
```
## Usage

### Sync Missing Translation Keys
The following command scans your project files for any missing translation keys and adds them to the respective language files in the resources/lang folder:

```bash
php artisan translation-importer:sync-missing-translation-keys
```

## Sync Translations
To ensure that all translations are up-to-date, use the sync command. This will compare existing translations and update them as needed:

```bash
php artisan translation-importer:sync-translations
```
## Configuration
Once you’ve published the configuration, you can customize the package’s behavior by modifying the config file located at:
```bash
config/translation-importer.php
```

## Testing
To ensure the package works as expected, you can run the following tests:
```bash
composer test
```

## Contributing
Contributions are welcome!
## License
The Laravel Translation Importer is open-sourced software licensed under the [MIT license](LICENSE.md).
## Acknowledgments
- [joedixon/laravel-translation](https://github.com/joedixon/laravel-translation)



