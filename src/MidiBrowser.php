<?php

namespace bviguier\RtMidi;

use bviguier\RtMidi\Exception\LibraryException;
use bviguier\RtMidi\Exception\MidiException;

final class MidiBrowser
{
    /**
     * @throws LibraryException
     * @throws MidiException
     */
    public function __construct(string $rtMidiLibPath = null)
    {
        try {
            $this->ffi = new Internal\RtMidiFFI($rtMidiLibPath);
            $this->defaultInput = $this->ffi->rtmidi_in_create_default();
            $this->defaultOutput = $this->ffi->rtmidi_out_create_default();
        } catch(\FFI\Exception $exception) {
            throw new LibraryException('Cannot load RtMidi library', 0, $exception);
        }
    }

    public function __destruct()
    {
        $this->ffi->rtmidi_in_free($this->defaultInput);
        $this->ffi->rtmidi_out_free($this->defaultOutput);
    }

    /**
     * @return array<int>
     */
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

    /**
     * @return array<string>
     * @throws MidiException
     */
    public function availableInputs(): array
    {
        $count = $this->ffi->rtmidi_get_port_count($this->defaultInput);
        $inputs = [];
        for($i=0;$i<$count;++$i) {
            $inputs[] = $this->ffi->rtmidi_get_port_name($this->defaultInput, $i);
        }

        return $inputs;
    }

    /**
     * @throws MidiException
     */
    public function openInput(string $name, int $queueSize = 64, int $api = Api::UNSPECIFIED): Input
    {
        if ($queueSize <= 1) {
            throw new \OutOfRangeException('Input queue size must be greater than 1.');
        }

        $port = -1;
        $count = $this->ffi->rtmidi_get_port_count($this->defaultInput);
        for($i=0;$i<$count;++$i) {
            if( $name === (string) $this->ffi->rtmidi_get_port_name($this->defaultInput, $i)) {
                $port = $i;
                break;
            }
        }

        if($port < 0) {
            throw new MidiException("Unknown input [$name]");
        }

        $input = $this->ffi->rtmidi_in_create($api, $name, $queueSize);
        $this->ffi->rtmidi_open_port($input, $port, "[RtMidi] $name");

        return new Input($name, $this->ffi, $input);
    }

    /**
     * @throws MidiException
     */
    public function openVirtualInput(string $name, int $queueSize = 64, int $api = Api::UNSPECIFIED): Input
    {
        if ($queueSize <= 1) {
            throw new \OutOfRangeException('Input queue size must be greater than 1.');
        }

        $input = $this->ffi->rtmidi_in_create($api, $name, $queueSize);
        $this->ffi->rtmidi_open_virtual_port($input, $name);

        return new Input($name, $this->ffi, $input);
    }

    /**
     * @return array<string>
     * @throws MidiException
     */
    public function availableOutputs(): array
    {
        $count = $this->ffi->rtmidi_get_port_count($this->defaultOutput);
        $outputs = [];
        for($i=0;$i<$count;++$i) {
            $outputs[] = (string) $this->ffi->rtmidi_get_port_name($this->defaultOutput, $i);
        }

        return $outputs;
    }

    /**
     * @throws MidiException
     */
    public function openOutput(string $name, int $api = Api::UNSPECIFIED): Output
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
            throw new MidiException("Unknown output [$name]");
        }

        $output = $this->ffi->rtmidi_out_create($api, $name);
        $this->ffi->rtmidi_open_port($output, $port, "[RtMidi] $name");

        return new Output($name, $this->ffi, $output);
    }

    /**
     * @throws MidiException
     */
    public function openVirtualOutput(string $name, int $api = Api::UNSPECIFIED): Output
    {
        $output = $this->ffi->rtmidi_out_create($api, $name);
        $this->ffi->rtmidi_open_virtual_port($output, $name);

        return new Output($name, $this->ffi, $output);
    }

    private Internal\RtMidiFFI $ffi;
    /** @var \FFI\CData<\RtMidiInPtr>  */
    private \FFI\CData $defaultInput;
    /** @var \FFI\CData<\RtMidiOutPtr>  */
    private \FFI\CData $defaultOutput;
}
