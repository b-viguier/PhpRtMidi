<?php

namespace bviguier\RtMidi\Internal;

use bviguier\RtMidi\Exception\MidiException;
use bviguier\RtMidi;

/**
 * @internal Don't rely on this code, internal usage only.
 */
final class Input implements RtMidi\Input
{
    /**
     * @param \FFI\CData<\RtMidiInPtr> $input
     */
    public function __construct(string $name, RtMidiFFI $ffi, \FFI\CData $input)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->input = $input;
        $this->msgBuffer = $this->ffi->new('unsigned char['.RtMidi\Message::MAX_LENGTH.']');
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
    public function pullMessage(): ?RtMidi\Message
    {
        $this->msgSize->cdata = RtMidi\Message::MAX_LENGTH;
        $this->ffi->rtmidi_in_get_message($this->input, $this->msgBuffer, $this->msgSizePtr);
        if($this->msgSize->cdata === 0) {
            return null;
        }

        return RtMidi\Message::fromBinString(\FFI::string($this->msgBuffer, $this->msgSize->cdata));
    }

    private string $name;
    private RtMidiFFI $ffi;
    /** @var \FFI\CData<\RtMidiInPtr> */
    private \FFI\CData $input;
    /** @var \FFI\CData<string> */
    private \FFI\CData $msgBuffer;
    /** @var \FFI\CData<int> */
    private \FFI\CData $msgSize;
    /** @var \FFI\CData<int> */
    private \FFI\CData $msgSizePtr;
}
