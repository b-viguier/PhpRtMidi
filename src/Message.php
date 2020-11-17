<?php

namespace bviguier\RtMidi;

final class Message
{
    public const MAX_LENGTH = 1024;

    /**
     * @@throws \OutOfBoundsException
     */
    static public function fromIntegers(int $firstByte, int ...$tailBytes): self
    {
        $allBytes = array_merge([$firstByte], $tailBytes);
        foreach ($allBytes as $byte) {
            if ($byte < 0 || $byte > 255) {
                throw new \OutOfBoundsException('Byte value must be between 0 and 127.');
            }
        }
        if (count($allBytes) > self::MAX_LENGTH) {
            throw new \LogicException('Message size cannot exceed '.self::MAX_LENGTH.' bytes.');
        }

        $instance = new self;
        $instance->bytes = join('', array_map('chr', $allBytes));

        return $instance;
    }

    /**
     * @throws \LogicException
     */
    static public function fromBinString(string $bytes): self
    {
        $length = strlen($bytes);
        if ($length === 0) {
            throw new \LogicException('Message cannot be empty.');
        }
        if ($length > self::MAX_LENGTH) {
            throw new \LogicException('Message size cannot exceed '.self::MAX_LENGTH.' bytes.');
        }

        $instance = new self;
        $instance->bytes = $bytes;

        return $instance;
    }

    public function size(): int
    {
        return strlen($this->bytes);
    }

    public function byte(int $index): int
    {
        if ($index < 0 || $index > strlen($this->bytes)) {
            throw new \OutOfRangeException('Out of range byte offset.');
        }

        return ord($this->bytes[$index]);
    }

    /**
     * @return array<int>
     */
    public function toIntegers(): array
    {
        return array_map('ord', str_split($this->bytes));
    }

    public function toBinString(): string
    {
        return $this->bytes;
    }

    private function __construct()
    {
    }

    private string $bytes;
}
