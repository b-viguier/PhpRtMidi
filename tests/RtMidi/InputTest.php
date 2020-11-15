<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Message;
use bviguier\RtMidi\MidiBrowser;
use bviguier\RtMidi\Exception\MidiException;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testOpeningUnknownInputFails(): void
    {
        $browser = new MidiBrowser();

        $this->expectException(MidiException::class);
        $this->expectExceptionMessage('Unknown input [Unknown Input]');
        $browser->openInput('Unknown Input');
    }

    public function testPullingMessage(): void
    {
        $browser = new MidiBrowser();
        $virtualOutput = $browser->openVirtualOutput('Test Input');

        $input = $browser->openInput('Test Input');
        $this->assertSame('Test Input', $input->name());
        $this->assertNull($input->pullMessage());

        $msgSent = Message::fromIntegers([128, 60, 0]);
        $virtualOutput->send($msgSent);
        usleep(100);
        $msgReceived = $input->pullMessage();

        $this->assertNotNull($msgReceived);
        $this->assertInstanceOf(Message::class, $msgReceived);
        $this->assertSame($msgSent->toIntegers(), $msgReceived->toIntegers());

        $this->assertNull($input->pullMessage());
    }

    public function testPullingFromBrokenInputSucceed(): void
    {
        $browser = new MidiBrowser();
        $virtualOutput = $browser->openVirtualOutput('Test Input');
        $input = $browser->openInput('Test Input');
        unset($virtualOutput);
        usleep(100);

        $this->assertNull($input->pullMessage());
    }
}
