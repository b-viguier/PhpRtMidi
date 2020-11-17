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
        $instance = new self;
        $instance->bytes = array_merge([$firstByte], $tailBytes);
        foreach ($instance->bytes as $byte) {
            if ($byte < 0 || $byte > 255) {
                throw new \OutOfBoundsException('Byte value must be between 0 and 127.');
            }
        }
        if (count($instance->bytes) > self::MAX_LENGTH) {
            throw new \LogicException('Message size cannot exceed '.self::MAX_LENGTH.' bytes.');
        }

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
        for ($i = 0; $i < $length; ++$i) {
            $instance->bytes[] = ord($bytes[$i]);
        }

        return $instance;
    }

    public function size(): int
    {
        return count($this->bytes);
    }

    public function byte(int $index): int
    {
        if ($index < 0 || $index > count($this->bytes)) {
            throw new \OutOfRangeException('Out of range byte offset.');
        }

        return $this->bytes[$index];
    }

    /**
     * @return array<int>
     */
    public function toIntegers(): array
    {
        return $this->bytes;
    }

    public function toBinString(): string
    {
        $str = '';
        foreach ($this->bytes as $byte) {
            $str .= chr($byte);
        }

        return $str;
    }

    private function __construct()
    {
    }

    /** @var array<int> */
    private array $bytes = [];
}
