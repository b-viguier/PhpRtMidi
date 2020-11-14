<?php

namespace bviguier\RtMidi;

class Output
{
    public function __construct(string $name, \FFI $ffi, \FFI\CData $output)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->output = $output;
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_out_free($this->output);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function send(Message $message): void
    {
        $size = $message->size();
        $buffer = $this->ffi->new("unsigned char[$size]");
        $i = 0;
        foreach ($message->toIntegers() as $byte) {
            $buffer[$i++] = $byte;
        }

        $this->ffi->rtmidi_out_send_message($this->output, $buffer, $size);
    }

    private string $name;
    private \FFI $ffi;
    private \FFI\CData $output;
}
