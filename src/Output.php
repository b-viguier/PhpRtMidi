<?php

namespace bviguier\RtMidi;

use bviguier\RtMidi\Exception\MidiException;

interface Output
{
    public function name(): string;

    /**
     * @throws MidiException
     */
    public function send(Message $message): void;
}
