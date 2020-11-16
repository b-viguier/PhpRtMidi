<?php

namespace bviguier\RtMidi;

final class Message
{
    public const MAX_LENGTH = 1024;

    /**
     * @param array<int> $bytes
     */
    static public function fromIntegers(array $bytes): self
    {
        $instance = new self;
        $instance->bytes = $bytes;

        return $instance;
    }

    static public function fromBinString(string $bytes): self
    {
        $instance = new self;
        for($i=0, $l=strlen($bytes); $i<$l; ++$i) {
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
