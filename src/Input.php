<?php

namespace bviguier\RtMidi;

use bviguier\RtMidi\Exception\MidiException;

interface Input
{
    public const ALLOW_NONE = 0;
    public const ALLOW_SYSEX = 1;
    public const ALLOW_TIME = 2;
    public const ALLOW_SENSE = 4;
    public const ALLOW_ALL = self::ALLOW_SYSEX | self::ALLOW_TIME | self::ALLOW_SENSE;

    public function name(): string;

    /**
     * By default Sysex, timing and active sensing messages are ignored.
     * @param int $allowMask Combination of self::ALLOW_*
     * @throws MidiException
     */
    public function allow(int $allowMask): void;

    /**
     * @throws MidiException
     */
    public function pullMessage(): ?Message;
}
