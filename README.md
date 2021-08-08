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

### Basics

```php
$filesize = new FileSize;
```

```php
$filesize->from(1200)->toKilobytes()->asNumber(); // 1.0
$filesize->fromBytes(1200)->toKilobytes()->asNumber(2); // 1.2
$filesize->fromBytes(1200)->toKibibytes()->asNumber(2); // 1.17

$filesize->fromKilobytes(1)->toKibibytes()->round(3)->asNumber(); // 0.977
$filesize->fromKilobytes(1)->toBytes()->asInteger(); // 1000

$filesize->fromKibibytes(1)->toBytes()->asInteger(); // 1024

$filesize->fromBytes(1024)->toKilobytes()->round(3)->asNumber(); //1.024
$filesize->fromBytes(1000)->toKilobytes()->round(3)->asNumber(); //1.000

$filesize->fromBytes(1024)->toKibibytes()->round(3)->asNumber(); //1.000
$filesize->fromBytes(1000)->toKibibytes()->round(3)->asNumber(); //0.977

$filesize->fromKilobytes(1.2)->asNumber(); // 1200
$filesize->fromKilobytes(1.2)->toSame()->asNumber(); // 1.2

$filesize->fromGigabytes(5000))
        ->toTerabytes()
        ->asNumber(2); // 5.0

$filesize->fromGibibytes(5000))
        ->toTebibytes()
        ->asNumber(2); // 4.88

$filesize->fromGigabytes(1)
    ->toMegabytes()
    ->round(3)
    ->withThousandSeparator() // optional - default: ","
    ->withDecimalSeparator() // optional - default: "."
    ->asString(); // "1,073.742 MB"
```

### For Humans
This will automatically convert the given size to the most useful human format.

```php

$filesize->fromBytes(1073741824)->round(2)->forHumans(); // "1.07 GB"

$filesize->fromGigabytes(0.5)->forHumans(); // "512 MB"

$filesize->fromBytes(15000)->inBinary()->round(1)->forHumans(); // '14.6 KiB', or
$filesize->fromBytes(15000)->toKibibytes()->round(1)->forHumans(); // '14.6 KiB'

$filesize->fromBytes(15000)->inDecimal()->round(1)->forHumans(); // '15.0 KB', or
$filesize->fromBytes(15000)->toKibibytes()->round(1)->forHumans(); // '15.0 KB'

$filesize->fromBytes(1073741824)->forHumans(); // "1 GB"

$filesize->fromBytes(1073741824)->forHumans(); // "1.00 GiB"
$filesize->fromBytes(1073741824)->toKibtyes()->forHumans(); // "1.00 GiB"
$filesize->fromBytes(1073741824)->toMegabytes()->forHumans(); // "1.07 GB"
$filesize->fromBytes(1073741824)->inDecimal()->forHumans(); // "1.07 GB"

$filesize->fromMegabytes(56156113)->forHumans(); // "53.55 TB"
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
