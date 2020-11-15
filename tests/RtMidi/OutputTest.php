<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Message;
use bviguier\RtMidi\MidiBrowser;
use bviguier\RtMidi\Exception\MidiException;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    public function testOpeningUnknownOutputFails(): void
    {
        $browser = new MidiBrowser();

        $this->expectException(MidiException::class);
        $this->expectExceptionMessage('Unknown output [Unknown Output]');
        $browser->openOutput('Unknown Output');
    }

    public function testSendingMessage(): void
    {
        $browser = new MidiBrowser();
        $virtualInput = $browser->openVirtualInput('Test Output');

        $output = $browser->openOutput('Test Output');
        $this->assertSame('Test Output', $output->name());
        $this->assertNull($virtualInput->pullMessage());

        $msgSent = Message::fromIntegers([128, 60, 0]);
        $output->send($msgSent);
        usleep(100);
        $msgReceived = $virtualInput->pullMessage();

        $this->assertNotNull($msgReceived);
        assert(!is_null($msgReceived));
        $this->assertInstanceOf(Message::class, $msgReceived);
        $this->assertSame($msgSent->toIntegers(), $msgReceived->toIntegers());

        $this->assertNull($virtualInput->pullMessage());
    }

    public function testSendingToBrokenOutputSucceed(): void
    {
        $browser = new MidiBrowser();
        $virtualInput = $browser->openVirtualInput('Test Output');
        $output = $browser->openOutput('Test Output');
        unset($virtualInput);
        usleep(100);

        $output->send(Message::fromIntegers([128, 60, 0]));
        $this->assertTrue(true); // No exception should be thrown
    }
}
