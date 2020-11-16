<?php

namespace bviguier\RtMidi;

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

    /**
     * By default Sysex, timing and active sensing messages are ignored.
     * @param int $allowMask Combination of self::ALLOW_*
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
