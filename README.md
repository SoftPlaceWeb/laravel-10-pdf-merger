# Laravel PDF Merger based TCPDF
[![Latest Stable Version](https://poser.pugx.org/softplaceweb/laravel-10-pdf-merger/v/stable)](https://packagist.org/packages/softplaceweb/laravel-10-pdf-merger) [![Total Downloads](https://poser.pugx.org/softplaceweb/laravel-10-pdf-merger/downloads)](https://packagist.org/packages/softplaceweb/laravel-10-pdf-merger) [![Latest Unstable Version](https://poser.pugx.org/softplaceweb/laravel-10-pdf-merger/v/unstable)](https://packagist.org/packages/softplaceweb/laravel-10-pdf-merger) [![License](https://poser.pugx.org/softplaceweb/laravel-10-pdf-merger/license)](https://packagist.org/packages/softplaceweb/laravel-10-pdf-merger)

A simple [Laravel](http://www.laravel.com) service provider with some basic configuration for including the [TCPDF library](http://www.tcpdf.org/) to allow you to merge PDF's in your Laravel application.

Compatibility from 1.3 to 1.7 versions, if attempt to merge version greater than 1.4 it convert through Ghosthscript.

The final result is a merged pdf file v 1.7  

## Requirements

- PHP 8.0+
- Ghostscript (gs command on Linux)

## Installation

The Laravel PDF Merger service provider can be installed via [composer](http://getcomposer.org) by requiring the `softplaceweb/laravel-10-pdf-merger` package in your project's `composer.json`.

```
composer require softplaceweb/laravel-10-pdf-merger
```

for lumen, you should add the following lines:

```php
$app->register(Softplaceweb\PdfMerger\PdfMergerServiceProvider::class);
class_alias(Softplaceweb\PdfMerger\Facades\TCPDF::class, 'PDF');
```

That's it! You're good to go.

Here is a little example:

```php
use Softplaceweb\PdfMerger\Facades\PdfMerger;

PdfMerger::addPDF('path/to/pdf1.pdf', 1)
->addPDF('path/to/pdf2.pdf', 'all')
->merge()
->save('new_file_name.pdf', 'browser');
```

or sending pdf's as array ...

```php
use Softplaceweb\PdfMerger\Facades\PdfMerger;

PdfMerger::addPDF([
    [
        'filePath' => 'path/to/pdf1.pdf',
        'pages'    => 1,
    ],
    [
        'filePath' => 'path/to/pdf2.pdf',
    ],
])
->merge()
->save('new_file_name.pdf', 'browser');
```

You can extend functionality for this class and for a list of all available function take a look at the [TCPDF Documentation](https://tcpdf.org/docs/srcdoc/TCPDF/)

## Configuration

Laravel Pdf Merger comes with some basic configuration.
If you want to override the defaults, you can publish the config, like so:

    php artisan vendor:publish --provider="Softplaceweb\PdfMerger\PdfMergerServiceProvider"

Now access `config/pdf-merger.php` to customize.

 * use_original_header is to used the original `Header()` from TCPDF.
    * Please note that `PdfMerger::setHeaderCallback(function($pdf){})` overrides this settings.
 * use_original_footer is to used the original `Footer()` from TCPDF.
    * Please note that `PdfMerger::setFooterCallback(function($pdf){})` overrides this settings.

## Credits

 * [oriceon/laravel-pdf-merger](https://github.com/oriceon/laravel-pdf-merger)
 * [DALTCORE/lara-pdf-merger](https://github.com/DALTCORE/lara-pdf-merger)
 * [elibyy/tcpdf-laravel](https://github.com/elibyy/tcpdf-laravel)
 * [anton-am/pdf-version-converter](https://github.com/Anton-Am/pdf-version-converter)
