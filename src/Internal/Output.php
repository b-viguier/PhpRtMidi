<?php

namespace bviguier\RtMidi\Internal;

use bviguier\RtMidi\Exception\MidiException;
use bviguier\RtMidi;

/**
 * @internal Don't rely on this code, internal usage only.
 */
class Output implements RtMidi\Output
{
    /**
     * @param \FFI\CData<\RtMidiOutPtr> $output
     */
    public function __construct(string $name, RtMidiFFI $ffi, \FFI\CData $output)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->output = $output;
        $this->msgBuffer = $this->ffi->new('unsigned char['.RtMidi\Message::MAX_LENGTH.']');
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_out_free($this->output);
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @throws MidiException
     */
    public function send(RtMidi\Message $message): void
    {
        \FFI::memcpy($this->msgBuffer, $message->toBinString(), $size = $message->size());
        $this->ffi->rtmidi_out_send_message($this->output, $this->msgBuffer, $size);
    }

    private string $name;
    private RtMidiFFI $ffi;
    /** @var \FFI\CData<\RtMidiOutPtr> */
    private \FFI\CData $output;
    /** @var \FFI\CData<string> */
    private \FFI\CData $msgBuffer;
}
