<?php

namespace Resohead\Filesize;

use Exception;

class FileSize
{
    protected $bytes;
    protected $precision = null;
    protected string $decimalSeparator = '.';
    protected string $thousandSeparator = ',';
    protected string $unit = 'B';
    protected int $base = 2;

    const BYTE = 'B';
    const KILOBYTE = 'KB';
    const MEGABYTE = 'MB';
    const GIGABYTE = 'GB';
    const TERABYTE = 'TB';
    const PETABYTE = 'PB';
    const EXABYTE = 'EB';
    const ZETTABYTE = 'ZB';
    const YOTTABYTES = 'YB';

    const BYTE_UNITS = [
        self::BYTE,
        self::KILOBYTE,
        self::MEGABYTE,
        self::GIGABYTE,
        self::TERABYTE,
        self::PETABYTE,
        self::EXABYTE,
        self::ZETTABYTE,
        self::YOTTABYTES
    ];

    const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];

    const BYTE_BINARY = 1024; //KiB/KB (base = 2)
    const BYTE_DECIMAL = 1000; //kB (base = 10)

    public function __toString()
    {
        return (string) $this->forHumans();
    }

    /**
     * Convert bytes to human readable format based on size.
     *
     * @return string Human readable bytes
     */
    public function forHumans(): string
    {
        $bytes = $this->bytes;

        for ($i = 0; ($bytes / $this->getByteStandard()) >= 0.9 && $i < count(self::BYTE_UNITS)-1; $i++) {
            $bytes /= $this->getByteStandard();
        }
        return round($bytes, $this->precision ??= self::BYTE_PRECISION[$i]) . ' ' . self::BYTE_UNITS[$i];
    }

    /**
     * Convert bytes to fixed human readable format
     *
     * @return string Human readable bytes
     */
    public function asString(): string
    {
        return number_format($this->convert(), $this->getPrecision(), $this->decimalSeparator, $this->thousandSeparator) . " " . $this->unit;
    }

    protected function getPrecision(): string
    {
        return $this->precision ??= self::BYTE_PRECISION[$this->byteUnitIndex($this->unit)];
    }

    public function asNumber(): int|float
    {
        return round($this->convert(), $this->getPrecision());
    }

    public function asInteger(): int
    {
        return (int) round($this->convert());
    }

    protected function convert(): int|float
    {
        return $this->bytes / $this->multiplier($this->unit);
    }

    public function round($precision = null): self
    {
        $this->precision = $precision;
        return $this;
    }

    public function fromBytes($bytes = 0): self
    {
        $this->bytes = $bytes;
        return $this;
    }

    public function from(int|float $size = 0, string $unit = self::BYTE): self
    {
        $this->bytes = $size * $this->multiplier($unit);
        return $this;
    }

    public function fromKilobytes($kilobytes = 0): self
    {
        return $this->from($kilobytes, self::KILOBYTE);
    }

    public function fromMegabytes($megabytes = 0): self
    {
        return $this->from($megabytes, self::MEGABYTE);
    }

    public function fromGigabytes($gigabytes = 0): self
    {
        return $this->from($gigabytes, self::GIGABYTE);
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
        return $this->base(10);
    }

    public function inBinary(): self
    {
        return $this->base(2);
    }

    protected function getByteStandard(): int
    {
        return $this->base === 2 ? self::BYTE_BINARY : self::BYTE_DECIMAL;
    }

    public static function parse($input)
    {
        preg_match('/(?P<size>-?[0-9\.]+)\s*(?P<unit>[A-z]+b|[bB])?$/i', trim($input), $matches, PREG_OFFSET_CAPTURE);

        if (!isset($matches['size'])) {
            throw new Exception("Could not parse \"{$input}\"");
        }

        return (new self)->from((float) $matches['size'][0], strtoupper($matches['unit'][0] ?? 'B'));
    }

    protected function byteUnitIndex($unit)
    {
        return array_search($unit, self::BYTE_UNITS);
    }

    protected function multiplier($unitsTo)
    {
        return $this->getByteStandard() ** $this->byteUnitIndex($unitsTo);
    }

    public function to($unit): self
    {
        $this->unit = $unit;
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
        return $this->inDecimal()->to(self::KILOBYTE);
    }

    public function toKb(): self
    {
        return $this->toKilobytes();
    }

    public function toKibibytes(): self
    {
        return $this->toKilobytes()->inBinary();
    }

    public function toKib(): self
    {
        return $this->toKibibytes();
    }

    public function toMegabytes(): self
    {
        return $this->inDecimal()->to(self::MEGABYTE);
    }

    public function toMb(): self
    {
        return $this->toMegabytes();
    }

    public function toMib(): self
    {
        return $this->toMegabytes()->inBinary();
    }

    public function toGigabytes(): self
    {
        return $this->inDecimal()->to(self::GIGABYTE);
    }

    public function toGb(): self
    {
        return $this->toGigabytes();
    }

    public function toGib(): self
    {
        return $this->toGigabytes()->inBinary();
    }

    public function toTerabytes(): self
    {
        return $this->to(self::TERABYTE);
    }

    public function toTb(): self
    {
        return $this->toTerabytes();
    }

    public function toPetabytes(): self
    {
        return $this->to(self::PETABYTE);
    }

    public function toPb(): self
    {
        return $this->toPetabytes();
    }

    public function toExabytes(): self
    {
        return $this->to(self::EXABYTE);
    }

    public function toEb(): self
    {
        return $this->toExabytes();
    }

    public function toZettabytes(): self
    {
        return $this->to(self::ZETTABYTE);
    }

    public function toZb(): self
    {
        return $this->toZettabytes();
    }

    public function toYottabytes(): self
    {
        return $this->to(self::YOTTABYTES);
    }

    public function toYb(): self
    {
        return $this->toYottabytes();
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
