<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Exception\LibraryException;
use bviguier\RtMidi\MidiBrowser;
use bviguier\RtMidi\Internal;
use PHPUnit\Framework\TestCase;

class MidiBrowserTest extends TestCase
{
    public function testLoadSystemRtMidiLibSucceed(): void
    {
        $browser = new MidiBrowser();
        $this->assertNotNull($browser);
        unset($browser);
    }

    public function testLoadExplicitRtMidiLibSucceed(): void
    {
        $lib = Internal\RtMidiFFI::defaultRtMidiLibrary();
        $browser = new MidiBrowser($lib);
        $this->assertNotNull($browser);
        unset($browser);
    }

    public function testLoadWrongRtMidiLibFails(): void
    {
        $this->expectException(LibraryException::class);
        $this->expectExceptionMessage("Cannot load RtMidi library");
        new MidiBrowser('InvalidPath');
    }

    public function testAtLeastOneApiExists(): void
    {
        $apis = (new MidiBrowser())->availableAPIs();
        $this->assertIsArray($apis);
        $this->assertGreaterThanOrEqual(1, count($apis));
    }

    public function testListingAvailableInputs(): void
    {
        $browser = new MidiBrowser();
        $existingInputs = $browser->availableInputs();
        $this->assertIsArray($existingInputs);

        // Create a virtual output, to see a new outside input
        $virtualOutput = $browser->openVirtualOutput('This is a test input');

        $newInputs = $browser->availableInputs();
        $this->assertIsArray($existingInputs);
        $this->assertSame(1 + count($existingInputs), count($newInputs));
        $diff = array_diff($newInputs, $existingInputs);
        $this->assertSame('This is a test input', reset($diff));

        unset($virtualOutput);
        $this->assertSame($existingInputs, $browser->availableInputs());
    }

    public function testListingAvailableOutputs(): void
    {
        $browser = new MidiBrowser();
        $existingOutputs = $browser->availableOutputs();
        $this->assertIsArray($existingOutputs);

        // Create a virtual output, to see a new outside input
        $virtualIntput = $browser->openVirtualInput('This is a test output');

        $newOutputs = $browser->availableOutputs();
        $this->assertIsArray($existingOutputs);
        $this->assertSame(1 + count($existingOutputs), count($newOutputs));
        $diff = array_diff($newOutputs, $existingOutputs);
        $this->assertSame('This is a test output', reset($diff));

        unset($virtualIntput);
        $this->assertSame($existingOutputs, $browser->availableOutputs());
    }

    public function testInvalidQueueSizeFailsWhenOpeningInput(): void
    {
        $browser = new MidiBrowser();
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('Input queue size must be greater than 1.');

        $browser->openInput('name', 1);
    }

    public function testInvalidQueueSizeFailsWhenOpeningVirtualInput(): void
    {
        $browser = new MidiBrowser();
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('Input queue size must be greater than 1.');

        $browser->openVirtualInput('name', 1);
    }
}
