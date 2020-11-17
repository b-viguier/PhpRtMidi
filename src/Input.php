<?php

namespace bviguier\RtMidi;

use bviguier\RtMidi\Exception\MidiException;

final class Input
{
    public const ALLOW_NONE = 0;
    public const ALLOW_SYSEX = 1;
    public const ALLOW_TIME = 2;
    public const ALLOW_SENSE = 4;
    public const ALLOW_ALL = self::ALLOW_SYSEX | self::ALLOW_TIME | self::ALLOW_SENSE;

    /**
     * @param \FFI\CData<\RtMidiInPtr> $input
     */
    public function __construct(string $name, Internal\RtMidiFFI $ffi, \FFI\CData $input)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->input = $input;
        $this->msgBuffer = $this->ffi->new('unsigned char['.Message::MAX_LENGTH.']');
        $this->msgSize = $this->ffi->new('size_t');
        $this->msgSizePtr = \FFI::addr($this->msgSize);
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_in_free($this->input);
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * By default Sysex, timing and active sensing messages are ignored.
     * @param int $allowMask Combination of self::ALLOW_*
     * @throws MidiException
     */
    public function allow(int $allowMask): void
    {
        $this->ffi->rtmidi_in_ignore_types(
            $this->input,
            !(bool) ($allowMask & self::ALLOW_SYSEX),
            !(bool) ($allowMask & self::ALLOW_TIME),
            !(bool) ($allowMask & self::ALLOW_SENSE),
        );
    }

    /**
     * @throws MidiException
     */
    public function pullMessage(): ?Message
    {
        $this->msgSize->cdata = Message::MAX_LENGTH;
        $this->ffi->rtmidi_in_get_message($this->input, $this->msgBuffer, $this->msgSizePtr);
        if($this->msgSize->cdata === 0) {
            return null;
        }

        return Message::fromBinString(\FFI::string($this->msgBuffer, $this->msgSize->cdata));
    }

    private string $name;
    private Internal\RtMidiFFI $ffi;
    /** @var \FFI\CData<\RtMidiInPtr> */
    private \FFI\CData $input;
    /** @var \FFI\CData<string> */
    private \FFI\CData $msgBuffer;
    /** @var \FFI\CData<int> */
    private \FFI\CData $msgSize;
    /** @var \FFI\CData<int> */
    private \FFI\CData $msgSizePtr;
}
