<?php

namespace bviguier\RtMidi\Internal;

use bviguier\RtMidi\Exception\LibraryException;
use bviguier\RtMidi\Exception\MidiException;

/**
 * @internal Don't rely on this code, internal usage only.
 */
class RtMidiFFI
{
    /**
     * @throws LibraryException
     */
    public function __construct(string $rtMidiLibPath = null)
    {
        $this->ffi = \FFI::cdef($this->headers(), $rtMidiLibPath ?? self::defaultRtMidiLibrary());
    }

    /**
     * @return \FFI\CData<\RtMidiInPtr>
     * @throws MidiException
     */
    public function rtmidi_in_create_default(): \FFI\CData
    {
        return $this->checkDeviceState($this->ffi->rtmidi_in_create_default());
    }

    /**
     * @return \FFI\CData<\RtMidiInPtr>
     * @throws MidiException
     */
    public function rtmidi_in_create(int $api, string $name, int $queueSize): \FFI\CData
    {
        return $this->checkDeviceState($this->ffi->rtmidi_in_create($api, $name, $queueSize));
    }

    /**
     * @param \FFI\CData<\RtMidiInPtr> $device
     */
    public function rtmidi_in_free(\FFI\CData $device): void
    {
        $this->ffi->rtmidi_in_free($device);
    }

    /**
     * @param \FFI\CData<\RtMidiInPtr> $device
     * @throws MidiException
     */
    public function rtmidi_in_ignore_types(\FFI\CData $device, bool $sysex, bool $time, bool $sense): void
    {
        $this->ffi->rtmidi_in_ignore_types($this->resetDeviceState($device), $sysex, $time, $sense);
        $this->checkDeviceState($device);
    }

    /**
     * @param \FFI\CData<\RtMidiInPtr> $device
     * @param \FFI\CData<string> $buffer
     * @param \FFI\CData<int> $size
     * @throws MidiException
     */
    public function rtmidi_in_get_message(\FFI\CData $device, \FFI\CData $buffer, \FFI\CData $size): float
    {
        $time = $this->ffi->rtmidi_in_get_message($this->resetDeviceState($device), $buffer, $size);
        $this->checkDeviceState($device);

        return $time;
    }

    /**
     * @return \FFI\CData<\RtMidiOutPtr>
     * @throws MidiException
     */
    public function rtmidi_out_create_default(): \FFI\CData
    {
        return $this->checkDeviceState($this->ffi->rtmidi_out_create_default());
    }

    /**
     * @return \FFI\CData<\RtMidiOutPtr>
     * @throws MidiException
     */
    public function rtmidi_out_create(int $api, string $name): \FFI\CData
    {
        return $this->checkDeviceState($this->ffi->rtmidi_out_create($api, $name));
    }

    /**
     * @param \FFI\CData<\RtMidiOutPtr> $device
     */
    public function rtmidi_out_free(\FFI\CData $device): void
    {
        $this->ffi->rtmidi_out_free($device);
    }

    /**
     * @param \FFI\CData<\RtMidiOutPtr> $device
     * @param \FFI\CData<string> $buffer
     * @throws MidiException
     */
    public function rtmidi_out_send_message(\FFI\CData $device, \FFI\CData $buffer, int $size): void
    {
        $this->ffi->rtmidi_out_send_message($this->resetDeviceState($device), $buffer, $size);
        $this->checkDeviceState($device);
    }

    /**
     * @param \FFI\CData<int> $buffer
     */
    public function rtmidi_get_compiled_api(\FFI\CData $buffer, int $size): int
    {
        return $this->ffi->rtmidi_get_compiled_api($buffer, $size);
    }

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     * @throws MidiException
     */
    public function rtmidi_get_port_name(\FFI\CData $device, int $port): string
    {
        $name = $this->ffi->rtmidi_get_port_name($this->resetDeviceState($device), $port);
        $this->checkDeviceState($device);

        return $name;
    }

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     * @throws MidiException
     */
    public function rtmidi_get_port_count(\FFI\CData $device): int
    {
        $count = $this->ffi->rtmidi_get_port_count($this->resetDeviceState($device));
        $this->checkDeviceState($device);

        return $count;
    }

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     * @throws MidiException
     */
    public function rtmidi_open_port(\FFI\CData $device, int $port, string $name): void
    {
        $this->ffi->rtmidi_open_port($this->resetDeviceState($device), $port, $name);
        $this->checkDeviceState($device);
    }

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     * @throws MidiException
     */
    public function rtmidi_open_virtual_port(\FFI\CData $device, string $name): void
    {
        $this->ffi->rtmidi_open_virtual_port($this->resetDeviceState($device), $name);
        $this->checkDeviceState($device);
    }

    /**
     * @return \FFI\CData<mixed>
     */
    public function new(string $type): \FFI\CData
    {
        return $this->ffi->new($type);
    }

    /**
     * @throws LibraryException
     */
    public static function defaultRtMidiLibrary(): string
    {
        switch(PHP_OS_FAMILY) {
            case 'Darwin': return 'librtmidi.dylib';
            case 'Linux': return 'librtmidi4.so';
        }

        throw new LibraryException(sprintf('No default RtMidi library configured for your OS family (%s).', PHP_OS_FAMILY));
    }

    private \FFI $ffi;

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     *
     * @return \FFI\CData<T>
     */
    private function resetDeviceState(\FFI\CData $device): \FFI\CData
    {
        $device[0]->ok = true;

        return $device;
    }

    /**
     * @template T of \RtMidiPtr
     * @param \FFI\CData<T> $device
     *
     * @return \FFI\CData<T>
     * @throws MidiException
     */
    private function checkDeviceState(\FFI\CData $device): \FFI\CData
    {
        if ($device[0]->ok ) {
            return $device;
        }

        throw new MidiException(sprintf(
            'Midi Error: %s',
            \FFI::string($device[0]->msg)
        ));
    }

    private function headers(): string
    {
        return <<<C_HEADER
struct RtMidiWrapper {void* ptr; void* data; bool  ok; const char* msg;};

typedef struct RtMidiWrapper* RtMidiPtr;
typedef struct RtMidiWrapper* RtMidiInPtr;
typedef struct RtMidiWrapper* RtMidiOutPtr;

enum RtMidiApi {RTMIDI_API_UNSPECIFIED, RTMIDI_API_MACOSX_CORE, RTMIDI_API_LINUX_ALSA,  RTMIDI_API_UNIX_JACK, RTMIDI_API_WINDOWS_MM, RTMIDI_API_RTMIDI_DUMMY, RTMIDI_API_NUM};

int rtmidi_get_compiled_api (enum RtMidiApi *apis, unsigned int apis_size);
const char *rtmidi_api_name(enum RtMidiApi api);
const char *rtmidi_api_display_name(enum RtMidiApi api);

void rtmidi_open_port (RtMidiPtr device, unsigned int portNumber, const char *portName);
void rtmidi_open_virtual_port (RtMidiPtr device, const char *portName);
void rtmidi_close_port (RtMidiPtr device);
unsigned int rtmidi_get_port_count (RtMidiPtr device);
const char* rtmidi_get_port_name (RtMidiPtr device, unsigned int portNumber);

RtMidiInPtr rtmidi_in_create_default (void);
RtMidiInPtr rtmidi_in_create (enum RtMidiApi api, const char *clientName, unsigned int queueSizeLimit);
void rtmidi_in_free (RtMidiInPtr device);
void rtmidi_in_ignore_types (RtMidiInPtr device, bool midiSysex, bool midiTime, bool midiSense);
double rtmidi_in_get_message (RtMidiInPtr device, unsigned char *message, size_t *size);

RtMidiOutPtr rtmidi_out_create_default (void);
RtMidiOutPtr rtmidi_out_create (enum RtMidiApi api, const char *clientName);
void rtmidi_out_free (RtMidiOutPtr device);
int rtmidi_out_send_message (RtMidiOutPtr device, const unsigned char *message, int length);
C_HEADER;
    }
}
