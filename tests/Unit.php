<?php

use Resohead\Filesize\FileSize;

it('loads file sizes from units')
    ->expect((new FileSize)->fromKilobytes(1))
    ->toKilobytes()->asNumber()->toBe(1.0)
    ->toKilobytes()->asInteger()->toBe(1)
    ->toBytes()->asNumber()->toBe(1_024.0)
    ->toBytes()->asInteger()->toBe(1_024);

it('can format for humans', function ($bytes, $expected) {
    expect((new FileSize)->fromBytes($bytes))
        ->forHumans()->toBe($expected);
})->with([
    [1, '1 B'],
    [1_024, '1 KB'],
    [1_048_576, '1 MB']
]);

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
        ->round(2)->asString()->toBe('1.54 KB')
        ->round(2)->withDecimalSeparator(',')->asString()->toBe('1,54 KB');
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
    ['1 kb', 1024.0],
    ['1MB', 1_048_576.0],
    ['1 MB', 1_048_576.0],
    ['1.0 MB', 1_048_576.0],
    ['1.0MB', 1_048_576.0],
    ['1.0mb', 1_048_576.0],
    ['1.0 mb', 1_048_576.0]
]);

it('can parse large file sizes', function () {
    expect(FileSize::parse('1073741824 GB'))
        ->toGib()->asNumber()->toBe(1_073_741_824.0)
        ->toGb()->asNumber()->toBe(1_152_921_504.61);
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
    expect((new FileSize)->fromBytes(1_000_000_000))->toGb()->asInteger()->toEqual(1);
});

test('it can convert from bytes to gib', function () {
    expect((new FileSize)->fromBytes(1_073_741_824))
        ->toGiB()->asNumber()->toEqual(1.00)
        ->toGb()->asNumber()->toEqual(1.07);
});

it('can convert to kilobytes (KB,kb) and Kibibytes (KiB)', function () {

    $filesize = (new FileSize)->fromBytes(1024);

    expect($filesize)
        ->toKilobytes()->round(2)->asNumber()->toEqual(1.02)
        ->toKilobytes()->round(0)->asNumber()->toEqual(1)
        ->toKilobytes()->inDecimal()->round(2)->asNumber()->toEqual(1.02)
        ->toKilobytes()->inDecimal()->round(0)->asNumber()->toEqual(1)
        ->toKilobytes()->inBinary()->round(2)->asNumber()->toEqual(1.00)
        ->toKilobytes()->inBinary()->round(0)->asNumber()->toEqual(1)
        ->toKb()->round(2)->asNumber()->toEqual(1.02);

    expect($filesize)
        ->toKibibytes()->round(2)->asNumber()->toEqual(1.00)
        ->toKibibytes()->round(0)->asNumber()->toEqual(1)
        ->toKibibytes()->inDecimal()->round(2)->asNumber()->toEqual(1.02)
        ->toKibibytes()->inDecimal()->round(0)->asNumber()->toEqual(1)
        ->toKib()->round(2)->asNumber()->toEqual(1.00);
});
