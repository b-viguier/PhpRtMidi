<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Api;
use bviguier\RtMidi\Internal\RtMidiFFI;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testApiEnumAreMatching(): void
    {
        $ffi = \FFI::cdef(
            'enum RtMidiApi {RTMIDI_API_UNSPECIFIED, RTMIDI_API_MACOSX_CORE, RTMIDI_API_LINUX_ALSA,  RTMIDI_API_UNIX_JACK, RTMIDI_API_WINDOWS_MM, RTMIDI_API_RTMIDI_DUMMY, RTMIDI_API_NUM};',
            RtMidiFFI::defaultRtMidiLibrary()
        );;

        $this->assertSame($ffi->RTMIDI_API_UNSPECIFIED, Api::UNSPECIFIED);
        $this->assertSame($ffi->RTMIDI_API_MACOSX_CORE, Api::MACOSX_CORE);
        $this->assertSame($ffi->RTMIDI_API_LINUX_ALSA, Api::LINUX_ALSA);
        $this->assertSame($ffi->RTMIDI_API_UNIX_JACK, Api::UNIX_JACK);
        $this->assertSame($ffi->RTMIDI_API_WINDOWS_MM, Api::WINDOWS_MM);
        $this->assertSame($ffi->RTMIDI_API_RTMIDI_DUMMY, Api::RTMIDI_DUMMY);
    }
}
