<?php

namespace bviguier\RtMidi;

use bviguier\RtMidi\Exception\MidiException;

class Output
{
    /**
     * @param \FFI\CData<\RtMidiOutPtr> $output
     */
    public function __construct(string $name, Internal\RtMidiFFI $ffi, \FFI\CData $output)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->output = $output;
        $this->msgBuffer = $this->ffi->new('unsigned char['.Message::MAX_LENGTH.']');
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
    public function send(Message $message): void
    {
        \FFI::memcpy($this->msgBuffer, $message->toBinString(), $size = $message->size());
        $this->ffi->rtmidi_out_send_message($this->output, $this->msgBuffer, $size);
    }

    private string $name;
    private Internal\RtMidiFFI $ffi;
    /** @var \FFI\CData<\RtMidiOutPtr> */
    private \FFI\CData $output;
    /** @var \FFI\CData<string> */
    private \FFI\CData $msgBuffer;
}
