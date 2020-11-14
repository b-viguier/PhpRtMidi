<?php

namespace bviguier\RtMidi;

final class Message
{
    static public function fromIntegers(array $bytes): self
    {
        $instance = new self;
        $instance->bytes = $bytes;

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

    public function toIntegers(): array
    {
        return $this->bytes;
    }

    private function __construct()
    {
    }

    private array $bytes = [];
}
