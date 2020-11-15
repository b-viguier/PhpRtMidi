<?php

namespace bviguier\RtMidi;

final class Input
{
    /**
     * @param \FFI\CData<\RtMidiInPtr> $input
     */
    public function __construct(string $name, \FFI $ffi, \FFI\CData $input)
    {
        $this->name = $name;
        $this->ffi = $ffi;
        $this->input = $input;
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_in_free($this->input);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ignoreTypes(): void
    {
        // TODO
    }

    public function pullMessage(): ?Message
    {
        $buffer = $this->ffi->new("unsigned char[64]");
        /** @var \FFI\CData<int> $maxSize */
        $maxSize = $this->ffi->new('size_t');
        $maxSize->cdata = 64;
        $this->ffi->rtmidi_in_get_message($this->input, $buffer, \FFI::addr($maxSize));
        if($maxSize->cdata === 0) {
            return null;
        }
        $messageData = [];
        for($i=0; $i<$maxSize->cdata; ++$i) {
            $messageData[] = $buffer[$i];
        }

        return Message::fromIntegers($messageData);
    }

    private string $name;
    private \FFI $ffi;
    /** @var \FFI\CData<\RtMidiInPtr> */
    private \FFI\CData $input;
}
