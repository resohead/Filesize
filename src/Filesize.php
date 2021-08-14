<?php

namespace Resohead\Filesize;

use Exception;

class Filesize
{
    protected $bytes;
    protected $precision = null;
    protected string $decimalSeparator = '.';
    protected string $thousandSeparator = ',';
    protected ?string $unit = null;
    protected string $from = self::BYTE;
    protected int $base = 10;

    const BYTE = 'B';
    const KILOBYTE = 'kB';
    const MEGABYTE = 'MB';
    const GIGABYTE = 'GB';
    const TERABYTE = 'TB';
    const PETABYTE = 'PB';
    const EXABYTE = 'EB';
    const ZETTABYTE = 'ZB';
    const YOTTABYTES = 'YB';

    const KIBIBYTE = 'KiB';
    const MEBIBYTE = 'MiB';
    const GIBIBYTE = 'GiB';
    const TEBIBYTE = 'TiB';
    const PEBIBYTE = 'PiB';
    const EXBIBYTE = 'EiB';
    const ZEBIBYTE = 'ZiB';
    const YOBIBYTE = 'YiB';

    const DECIMAL_BYTE_UNITS = [
        self::BYTE,
        self::KILOBYTE,
        self::MEGABYTE,
        self::GIGABYTE,
        self::TERABYTE,
        self::PETABYTE,
        self::EXABYTE,
        self::ZETTABYTE,
        self::YOTTABYTES,
    ];

    const BINARY_BYTE_UNITS = [
        self::BYTE,
        self::KIBIBYTE,
        self::MEBIBYTE,
        self::GIBIBYTE,
        self::TEBIBYTE,
        self::PEBIBYTE,
        self::EXBIBYTE,
        self::ZEBIBYTE,
        self::YOBIBYTE,
    ];

    const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];

    const BYTE_BINARY = 1024;
    const BYTE_DECIMAL = 1000;

    public function __toString()
    {
        return (string) $this->forHumans();
    }

    public function getDefaultUnit()
    {
        return $this->unit ?? $this->from;
    }

    /**
     * Convert bytes to human readable format based on size.
     *
     * @return string Human readable bytes
     */
    public function forHumans(): string
    {
        $bytes = $this->bytes;
        $array = self::DECIMAL_BYTE_UNITS;
        $byteStandard = self::BYTE_DECIMAL;

        if($this->isBinary()){
            $array = self::BINARY_BYTE_UNITS;
            $byteStandard = self::BYTE_BINARY;
        }

        for ($i = 0; ($bytes / $byteStandard) >= 0.9 && $i < count($array)-1; $i++) {
            $bytes /= $byteStandard;
        }
        return number_format($bytes, $this->precision ??= self::BYTE_PRECISION[$i]) . ' '. $array[$i];
    }

    /**
     * Convert bytes to fixed human readable format
     *
     * @return string Human readable bytes
     */
    public function asString(): string
    {
        return number_format($this->convert(), $this->getPrecision(), $this->decimalSeparator, $this->thousandSeparator) . " " . $this->getDefaultUnit();
    }

    protected function getPrecision(): string
    {
        return $this->precision ??= self::BYTE_PRECISION[$this->byteUnitIndex($this->unit)];
    }

    public function asNumber($precision = null): int|float
    {
        return round($this->convert(), $precision ?? $this->getPrecision());
    }

    public function asInteger(): int
    {
        return (int) round($this->convert());
    }

    public function round($precision = null): self
    {
        $this->precision = $precision;
        return $this;
    }

    public function getByteStandard(): int
    {
        return $this->isDecimal() ? self::BYTE_DECIMAL : self::BYTE_BINARY;
    }

    public function getByteStandardFromUnit($unit): int
    {
        if($unit === self::BYTE){
            return $this->getByteStandard();
        }

        if(array_search($unit, self::BINARY_BYTE_UNITS)){
            return self::BYTE_BINARY;
        }

        return self::BYTE_DECIMAL;
    }

    protected function getBaseFromUnit($unit)
    {
        return $this->isBinaryByte($unit)
            ? 2
            : 10;
    }

    protected function isBinaryByte($unit)
    {
        return in_array($unit, self::BINARY_BYTE_UNITS);
    }

    public function byteUnitArray($unit)
    {
        return $this->isBinaryByte($unit)
            ? self::BINARY_BYTE_UNITS
            : self::DECIMAL_BYTE_UNITS;
    }

    public function byteUnitIndex($unit)
    {
        return array_search($unit, $this->byteUnitArray($unit));
    }

    public function multiplier($unitsTo)
    {
        return $this->getByteStandardFromUnit($unitsTo) ** $this->byteUnitIndex($unitsTo);
    }

    protected function convert(): int|float
    {
        return $this->bytes / $this->multiplier($this->unit);
    }

    protected function convertToBytes(int|float $size = 0, string $unit = self::BYTE)
    {
        return $size * $this->multiplier($unit);
    }

    public function base(int $base): self
    {
        if($base !== 2 && $base !== 10){
            throw new Exception('Invalid base value. Must be 2 or 10');
        }
        $this->base = $base;
        return $this;
    }

    public function inDecimal(): self
    {
        // map the current output index with the decimal index
        $this->unit = self::DECIMAL_BYTE_UNITS[$this->byteUnitIndex($this->getDefaultUnit())];
        return $this->base(10);
    }

    public function inBinary(): self
    {
        // map the current output index with the binary index
        $this->unit = self::BINARY_BYTE_UNITS[$this->byteUnitIndex($this->getDefaultUnit())];
        return $this->base(2);
    }

    protected function isDecimal()
    {
        return $this->base === 10;
    }

    protected function isBinary()
    {
        return $this->base === 2;
    }

    public function from(int|float $size = 0, string $unit = self::BYTE): self
    {
        $this->bytes = $this->convertToBytes($size, $unit);
        $this->from = $unit;
        // $this->unit = $unit;
        return $this;
    }

    public function fromBytes($size = 0): self
    {
        return $this->from($size, self::BYTE);
    }

    public function fromKilobytes($size = 0): self
    {
        return $this->from($size, self::KILOBYTE);
    }

    public function fromKibibytes($size = 0): self
    {
        return $this->from($size, self::KIBIBYTE);
    }

    public function fromMegabytes($size = 0): self
    {
        return $this->from($size, self::MEGABYTE);
    }

    public function fromMebibytes($size = 0): self
    {
        return $this->from($size, self::MEBIBYTE);
    }

    public function fromGigabytes($size = 0): self
    {
        return $this->from($size, self::GIGABYTE);
    }

    public function fromGibibytes($size = 0): self
    {
        return $this->from($size, self::GIBIBYTE);
    }

    public function fromTerabytes($size = 0): self
    {
        return $this->from($size, self::TERABYTE);
    }

    public function fromPetabytes($size = 0): self
    {
        return $this->from($size, self::PETABYTE);
    }

    public function fromExabytes($size = 0): self
    {
        return $this->from($size, self::EXABYTE);
    }

    public function fromZettabytes($size = 0): self
    {
        return $this->from($size, self::ZETTABYTE);
    }

    public function fromYottabytes($size = 0): self
    {
        return $this->from($size, self::YOTTABYTES);
    }

    public static function parse($input)
    {
        preg_match('/(?P<size>-?[0-9\.]+)\s*(?P<unit>[A-z]+b|[bB])?$/i', trim($input), $matches, PREG_OFFSET_CAPTURE);

        $matchedUnit = strtoupper($matches['unit'][0] ?? 'B');
        $decimalBytes = array_search($matchedUnit, array_map('strtoupper', self::DECIMAL_BYTE_UNITS));
        $binaryBytes = array_search($matchedUnit, array_map('strtoupper', self::BINARY_BYTE_UNITS));

        $unit = null;
        if($decimalBytes !== false){
            $unit = self::DECIMAL_BYTE_UNITS[$decimalBytes];
        } elseif($binaryBytes !== false) {
            $unit = self::BINARY_BYTE_UNITS[$binaryBytes];
        }

        if (!isset($matches['size']) || !isset($unit)) {
            throw new Exception("Could not parse \"{$input}\"");
        }

        return (new self)->from((float) $matches['size'][0], $unit);
    }

    public function to($unit): self
    {
        $this->unit = $unit;
        $this->base(
            $this->getBaseFromUnit($unit)
        );
        return $this;
    }

    public function toSame()
    {
        $this->unit = $this->getDefaultUnit();
        return $this;
    }

    public function toBytes(): self
    {
        return $this->to(self::BYTE);
    }

    public function toB(): self
    {
        return $this->toBytes();
    }

    public function toKilobytes(): self
    {
        return $this->to(self::KILOBYTE);
    }

    public function toKibibytes(): self
    {
        return $this->to(self::KIBIBYTE);
    }

    public function toKb(): self
    {
        return $this->toKilobytes();
    }

    public function toKiB(): self
    {
        return $this->toKibibytes();
    }

    public function toMegabytes(): self
    {
        return $this->to(self::MEGABYTE);
    }

    public function toMebibytes(): self
    {
        return $this->to(self::MEBIBYTE);
    }

    public function toMb(): self
    {
        return $this->toMegabytes();
    }

    public function toMib(): self
    {
        return $this->toMebibytes();
    }

    public function toGigabytes(): self
    {
        return $this->to(self::GIGABYTE);
    }

    public function toGibibytes(): self
    {
        return $this->to(self::GIBIBYTE);
    }

    public function toGb(): self
    {
        return $this->toGigabytes();
    }

    public function toGib(): self
    {
        return $this->toGibibytes();
    }

    public function toTerabytes(): self
    {
        return $this->to(self::TERABYTE);
    }

    public function toTb(): self
    {
        return $this->toTerabytes();
    }

    public function toTebibytes(): self
    {
        return $this->to(self::TEBIBYTE);
    }

    public function toTib(): self
    {
        return $this->toTebibytes();
    }

    public function toPetabytes(): self
    {
        return $this->to(self::PETABYTE);
    }

    public function toPebibytes(): self
    {
        return $this->to(self::PEBIBYTE);
    }

    public function toPb(): self
    {
        return $this->toPetabytes();
    }

    public function toPib(): self
    {
        return $this->toPebibytes();
    }

    public function toExabytes(): self
    {
        return $this->to(self::EXABYTE);
    }

    public function toExbibyte(): self
    {
        return $this->to(self::EXBIBYTE);
    }

    public function toEb(): self
    {
        return $this->toExabytes();
    }

    public function toEib(): self
    {
        return $this->toExbibyte();
    }

    public function toZettabytes(): self
    {
        return $this->to(self::ZETTABYTE);
    }

    public function toZebibytes(): self
    {
        return $this->to(self::ZEBIBYTE);
    }

    public function toZb(): self
    {
        return $this->toZettabytes();
    }

    public function toZib(): self
    {
        return $this->toZebibytes();
    }

    public function toYottabytes(): self
    {
        return $this->to(self::YOTTABYTES);
    }

    public function toYobibytes(): self
    {
        return $this->to(self::YOBIBYTE);
    }

    public function toYb(): self
    {
        return $this->toYottabytes();
    }

    public function toYib(): self
    {
        return $this->toYobibytes();
    }

    public function withDecimalSeparator(string $separator): self
    {
        $this->decimalSeparator = $separator;
        return $this;
    }

    public function withThousandSeparator(string $separator = ','): self
    {
        $this->thousandSeparator = $separator;
        return $this;
    }

    public function withoutThousandSeparator(): self
    {
        $this->thousandSeparator = '';
        return $this;
    }
}
