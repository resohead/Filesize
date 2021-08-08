# Filesize

[![Latest Version on Packagist](https://img.shields.io/packagist/v/resohead/filesize.svg?style=flat-square)](https://packagist.org/packages/resohead/filesize)
[![Total Downloads](https://img.shields.io/packagist/dt/resohead/filesize.svg?style=flat-square)](https://packagist.org/packages/resohead/filesize)

PHP fluent API extension for converting file sizes - like Carbon but for file sizes.

## Installation

You can install the package via composer:

```bash
composer require resohead/filesize
```

## Usage

### Basic

```php
(new Filesize)->fromGigabytes(5000)
    ->toTerabytes()
    ->round(2)
    ->asNumber(); // 4.88

(new Filesize)->fromGigabytes(1)
    ->toMegabytes()
    ->round(3)
    ->withThousandSeparator('.') // defaults to ","
    ->withDecimalSeparator(',') // defaults to "."
    ->asString(); // "1.073,742 MB"
```

### For Humans
This will automatically convert the given size to the most useful human format.

```php
(new Filesize)->fromBytes(1073741824)->forHumans(); // "1 GB"

(new Filesize)->fromBytes(1073741824)->round(2)->forHumans(); // "1.07 GB"

(new Filesize)->fromGigabytes(0.5)->forHumans(); // "512 MB"

(new Filesize)->fromMegabytes(56156113)->forHumans(); // "53.55 TB"
```

### Parse input from string
``` php
Filesize::parse('1 KB')->toBytes()->asInteger(); // 1024
Filesize::parse('1 KB')->toKilobytes()->asString(); // "1 KB"
Filesize::parse('1 KB')->round(3)->toMb()->asString(); // "0.001 MB"
```

### Testing

``` bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
