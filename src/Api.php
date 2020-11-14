<?php

namespace bviguier\RtMidi;

class Api
{
    public const UNSPECIFIED = 0;
    public const MACOSX_CORE = 1;
    public const LINUX_ALSA = 2;
    public const UNIX_JACK = 3;
    public const WINDOWS_MM = 4;
    public const RTMIDI_DUMMY = 5;
}
