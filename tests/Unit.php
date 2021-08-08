<?php

use Resohead\Filesize\FileSize;

it('it uses bytes as default input', function () {
    expect((new FileSize))
        ->from(1000)->asInteger()->toBe(1000)
        ->fromBytes(1000)->asInteger()->toBe(1000);
});

it('it returns bytes by default', function () {
    expect((new FileSize))
        ->fromKilobytes(1.5)->asInteger()->toBe(1500)
        ->fromKibibytes(1.5)->asInteger()->toBe(1536);
});

it('sets the base when outputting to formats', function(){
    expect((new FileSize)->fromKilobytes(1.5))
        ->byteUnitIndex(FileSize::BYTE)->toBe(0)
        ->byteUnitArray(FileSize::BYTE)->toBe(FileSize::BINARY_BYTE_UNITS)
        ->byteUnitIndex(FileSize::KILOBYTE)->toBe(1)
        ->byteUnitArray(FileSize::KILOBYTE)->toBe(FileSize::DECIMAL_BYTE_UNITS)
        ->byteUnitIndex(FileSize::KIBIBYTE)->toBe(1)
        ->byteUnitArray(FileSize::KIBIBYTE)->toBe(FileSize::BINARY_BYTE_UNITS);
});

it('can set the byte standard as string', function () {
    expect((new FileSize)->fromBytes(1610612736))
        ->toGibibytes()->asString()->toBe('1.50 GiB')
        ->toGigabytes()->asString()->toBe('1.61 GB');
});

it('can return the same unit', function(){

    expect((new FileSize)->fromKilobytes(1.60))
        ->asNumber()->toBe(1600.0);

    expect((new FileSize)->fromGigabytes(1.60))
        ->asNumber()->toBe(1600000000.0)
        ->toSame()->round(2)->asNumber()->toBe(1.60);

    expect((new FileSize)->fromGigabytes(1.61))
        ->asNumber()->toBe(1610000000.0)
        ->toGigabytes()->round(2)->asNumber()->toBe(1.61)
        ->toSame()->round(2)->asNumber()->toBe(1.61);
});

it('can round numbers', function(){
    expect((new FileSize)->fromBytes(1610000000))
        ->toMebibytes()->asNumber(2)->toBe(1535.42);
});

it('does something with base standard', function(){
    expect((new FileSize)->fromBytes(1610612736))
        ->toGigabytes()->asNumber()->toBe(1.61)
        ->inBinary()->asNumber()->toBe(1.50);

    expect((new FileSize)->fromGigabytes(1.61))
        ->toGigabytes()->inBinary()->asNumber()->toBe(1.50)
        ->toSame()->asNumber()->toBe(1.50)
        ->toSame()->inDecimal()->asNumber()->toBe(1.61)
        ->toSame()->inBinary()->asNumber()->toBe(1.50);
});

it('can set a default format for humans', function ($bytes, $decimal) {
    expect((new FileSize)->fromBytes($bytes))
        ->inDecimal()->forHumans()->toBe($decimal);
})->with([
    [1, '1 B'],
    [1000, '1 KB'],
    [2000, '2 KB'],
    [15000, '15 KB'],
    [1000000, '1.0 MB'],
    [1000000000, '1.00 GB'],
    [1500000000, '1.50 GB']
]);

it('can set binary format for humans', function ($bytes, $decimal) {
    expect((new FileSize)->fromBytes($bytes))
        ->inBinary()->forHumans()->toBe($decimal);
})->with([
    [1, '1 B'],
    [1000, '1 KiB'],
    [2000, '2 KiB'],
    [15000, '15 KiB'],
    [1000000, '1.0 MiB'],
    [1000000000, '0.93 GiB'],
    [1500000000, '1.40 GiB']
]);

it('can set binary format for humans 2', function ($bytes, $decimal) {
    expect((new FileSize)->fromBytes($bytes))
        ->inBinary()->round(1)->forHumans()->toBe($decimal);
})->with([
    [1, '1.0 B'],
    [1000, '1.0 KiB'],
    [2000, '2.0 KiB'],
    [15000, '14.6 KiB'],
    [1000000, '1.0 MiB'],
    [1000000000, '0.9 GiB'],
    [1500000000, '1.4 GiB']
]);

it('returns that same byte standard by default for humans', function () {
    expect((new FileSize)->fromGibibytes(1.5))
        ->forHumans()->toBe('1.50 GiB');
});

it('can set the byte standard for humans', function () {
    expect((new FileSize)->fromKilobytes(1610000))
        ->toGigabytes()->asNumber()->toBe(1.61)
        ->toGibibytes()->asNumber()->toBe(1.5)
        ->toGigabytes()->forHumans()->toBe('1.61 GB')
        ->toGibibytes()->forHumans()->toBe('1.50 GiB');
});

it('can format for humans', function(){
    expect((new FileSize)->fromBytes(1610612736))
        ->toGibibytes()->round(2)->asNumber()->toBe(1.50)
        ->toGibibytes()->forHumans()->toBe('1.50 GiB')
        ->toGibibytes()->round(1)->forHumans()->toBe('1.5 GiB')
        ->toGibibytes()->round(2)->forHumans()->toBe('1.50 GiB')
        ->toGigabytes()->round(2)->asNumber()->toBe(1.61);
});

it('can format as a string', function () {
    expect((new FileSize)->fromBytes(1024))
        ->asString()->toBe('1,024 B')
        ->withThousandSeparator()->asString()->toBe('1,024 B')
        ->withThousandSeparator('.')->asString()->toBe('1.024 B')
        ->withoutThousandSeparator()->asString()->toBe('1024 B')
        ->withDecimalSeparator(',')->asString()->toBe('1024 B')
        ->withThousandSeparator('.')->withDecimalSeparator(',')->asString()->toBe('1.024 B');
});

it('can round numbers formatted as string', function () {
    expect((new FileSize)->fromKilobytes(1.5)->toKilobytes())
        ->asString()->toBe('2 KB')
        ->round(0)->asString()->toBe('2 KB')
        ->round(1)->asString()->toBe('1.5 KB')
        ->round(2)->asString()->toBe('1.50 KB')
        ->round(2)->withDecimalSeparator(',')->asString()->toBe('1,50 KB');

    expect((new FileSize)->fromKibibytes(1.5)->toKilobytes())
        ->asString()->toBe('2 KB')
        ->round(0)->asString()->toBe('2 KB')
        ->round(1)->asString()->toBe('1.5 KB')
        ->round(2)->asString()->toBe('1.54 KB')
        ->round(2)->withDecimalSeparator(',')->asString()->toBe('1,54 KB');
});

it('it uses the latest byte standard for calculations', function(){
    expect((new FileSize)->fromKilobytes(1.5))
        ->toKibibytes()->asNumber(2)->toBe(1.46)
        ->toKibibytes()->inDecimal()->asNumber(2)->toBe(1.5)
        ->toKibibytes()->inBinary()->asNumber(2)->toBe(1.46)
        ->toKilobytes()->inBinary()->asNumber(2)->toBe(1.46);
});

it('can parse file sizes', function ($input, $output) {
    expect(FileSize::parse($input))
        ->toBytes()->asNumber()->toBe($output);
})->with([
    ['1', 1.0],
    ['12', 12.0],
    ['12 B', 12.0],
    ['12 B', 12.0],
    ['12.0 B', 12.0],
    ['12.0B', 12.0],
    ['12.0b', 12.0],
    ['12.0 b', 12.0],
    ['1 kb', 1000.0],
    ['1 kB', 1000.0],
    ['1 kib', 1024.0],
    ['1 KiB', 1024.0],
    ['1 KIB', 1024.0],
    ['1MB', 1000000.0],
    ['1 MB', 1000000.0],
    ['1.0 MB', 1000000.0],
    ['1.0MB', 1000000.0],
    ['1.0mb', 1000000.0],
    ['1.0 mb', 1000000.0],
    ['1.0 mib', 1048576.0],
    ['1.0 MIB', 1048576.0],
    ['1.0 MiB', 1048576.0]
]);

it('can parse large file sizes', function () {
    expect(FileSize::parse('1073741824 GB'))
        ->toGib()->asInteger()->toBe(1000000000)
        ->toGb()->asInteger()->toBe(1073741824);
});

test('it can convert from bytes to kb', function () {
    expect((new FileSize)->fromBytes(1_000))
        ->toKb()->asInteger()->toEqual(1);
});

test('it can convert from bytes to kib', function () {
    expect((new FileSize)->fromBytes(1_024))
        ->toKiB()->asNumber()->toEqual(1.0)
        ->toKb()->round(3)->asNumber()->toEqual(1.024);
});

test('it can convert from bytes to mb', function () {
    expect((new FileSize)->fromBytes(1_000_000))
        ->toMb()->asInteger()->toEqual(1);
});

test('it can convert from bytes to mib', function () {
    expect((new FileSize)->fromBytes(1_048_576))
        ->toMiB()->asNumber()->toEqual(1.0);
});

test('it can convert from bytes to gb', function () {
    expect((new FileSize)->fromBytes(1_000_000_000))
        ->toGb()->asInteger()->toEqual(1)
        ->toGib()->round(2)->asNumber(0.93);
});

test('it can convert from bytes to gib', function () {
    expect((new FileSize)->fromBytes(1_073_741_824))
        ->toMebibytes()->asNumber()->toEqual(1024)
        ->toKibibytes()->asNumber()->toEqual(1_048_576)
        ->toGiB()->round(2)->asNumber()->toEqual(1.00)
        ->toGb()->round(2)->asNumber()->toEqual(1.07);
});

it('converts kilobytes', function(){
    expect(new Filesize)
        ->fromKilobytes(1)->toKibibytes()->round(3)->asNumber()->toBe(0.977)
        ->fromKilobytes(1)->toBytes()->asInteger()->toBe(1000)
        ->fromKibibytes(1)->toBytes()->asInteger()->toBe(1024)
        ->fromBytes(1024)->toKilobytes()->round(3)->asNumber()->toBe(1.024)
        ->fromBytes(1000)->toKilobytes()->round(3)->asNumber()->toBe(1.000)
        ->fromBytes(1024)->toKibibytes()->round(3)->asNumber()->toBe(1.000)
        ->fromBytes(1000)->toKibibytes()->round(3)->asNumber()->toBe(0.977);
});

it('converts megabytes', function(){
    expect(new Filesize)
        ->fromMegabytes(1)->toMebibytes()->round(2)->asNumber()->toBe(0.95)
        ->fromMegabytes(1)->toKilobytes()->asInteger()->toBe(1000)
        ->fromMegabytes(1)->toBytes()->asInteger()->toBe(1_000_000)
        ->fromMebibytes(1)->toKibibytes()->asInteger()->toBe(1024)
        ->fromMebibytes(1)->toKilobytes()->round(2)->asNumber()->toBe(1048.58)
        ->fromMebibytes(1)->toBytes()->asInteger()->toBe(1_048_576);
});
