<?php

namespace bviguier\RtMidi;

final class MidiBrowser
{
    public function __construct(string $rtMidiLibPath = null)
    {
        $this->ffi = \FFI::cdef($this->headers(), $rtMidiLibPath ?? 'librtmidi.dylib');
        $this->defaultInput = $this->ffi->rtmidi_in_create_default();
        $this->defaultOutput = $this->ffi->rtmidi_out_create_default();
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_in_free($this->defaultInput);
        $this->ffi->rtmidi_out_free($this->defaultOutput);
    }

    public function availableAPIs(): array
    {
        $buffer = $this->ffi->new('enum RtMidiApi[6]');
        $nbApis = $this->ffi->rtmidi_get_compiled_api($buffer, 6);
        $apiList = [];
        for($i=0; $i<$nbApis; ++$i) {
            $apiList[] = $buffer[$i];
        }

        return $apiList;
    }

    public function availableInputs(): array
    {
        $count = $this->ffi->rtmidi_get_port_count($this->defaultInput);
        $inputs = [];
        for($i=0;$i<$count;++$i) {
            $inputs[] = (string) $this->ffi->rtmidi_get_port_name($this->defaultInput, $i);
        }

        return $inputs;
    }

    public function openInput(string $name, int $queueSize = 64, int $api = API::UNSPECIFIED): Input
    {
        $port = -1;
        $count = $this->ffi->rtmidi_get_port_count($this->defaultInput);
        for($i=0;$i<$count;++$i) {
            if( $name === (string) $this->ffi->rtmidi_get_port_name($this->defaultInput, $i)) {
                $port = $i;
                break;
            }
        }

        if($port < 0) {
            throw new MidiError("Unknown input [$name]");
        }

        $input = $this->ffi->rtmidi_in_create($api, $name, $queueSize);
        $this->ffi->rtmidi_open_port($input, $port, "[RtMidi] $name");

        return new Input($name, $this->ffi, $input);
    }

    public function openVirtualInput(string $name, int $queueSize = 64, int $api = API::UNSPECIFIED): Input
    {
        $input = $this->ffi->rtmidi_in_create($api, $name, $queueSize);
        $this->ffi->rtmidi_open_virtual_port($input, $name);

        return new Input($name, $this->ffi, $input);
    }

    public function availableOutputs(): array
    {
        $count = $this->ffi->rtmidi_get_port_count($this->defaultOutput);
        $outputs = [];
        for($i=0;$i<$count;++$i) {
            $outputs[] = (string) $this->ffi->rtmidi_get_port_name($this->defaultOutput, $i);
        }

        return $outputs;
    }

    public function openOutput(string $name, int $api = API::UNSPECIFIED): Output
    {
        $port = -1;
        $count = $this->ffi->rtmidi_get_port_count($this->defaultOutput);
        for($i=0;$i<$count;++$i) {
            if( $name === (string) $this->ffi->rtmidi_get_port_name($this->defaultOutput, $i)) {
                $port = $i;
                break;
            }
        }

        if($port < 0) {
            throw new MidiError("Unknown output [$name]");
        }

        $output = $this->ffi->rtmidi_out_create($api, $name);
        $this->ffi->rtmidi_open_port($output, $port, "[RtMidi] $name");

        return new Output($name, $this->ffi, $output);
    }

    public function openVirtualOutput(string $name, int $api = API::UNSPECIFIED): Output
    {
        $output = $this->ffi->rtmidi_out_create($api, $name);
        $this->ffi->rtmidi_open_virtual_port($output, $name);

        return new Output($name, $this->ffi, $output);
    }

    private \FFI $ffi;
    private \FFI\CData $defaultInput;
    private \FFI\CData $defaultOutput;

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
enum RtMidiApi rtmidi_in_get_current_api (RtMidiPtr device);
void rtmidi_in_ignore_types (RtMidiInPtr device, bool midiSysex, bool midiTime, bool midiSense);
double rtmidi_in_get_message (RtMidiInPtr device, unsigned char *message, size_t *size);

RtMidiOutPtr rtmidi_out_create_default (void);
RtMidiOutPtr rtmidi_out_create (enum RtMidiApi api, const char *clientName);
void rtmidi_out_free (RtMidiOutPtr device);
enum RtMidiApi rtmidi_out_get_current_api (RtMidiPtr device);
int rtmidi_out_send_message (RtMidiOutPtr device, const unsigned char *message, int length);
C_HEADER;


    }
}
