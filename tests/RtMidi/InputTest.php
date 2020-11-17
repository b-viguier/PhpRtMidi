<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Input;
use bviguier\RtMidi\Message;
use bviguier\RtMidi\TimeMessage;
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

        $msgSent = Message::fromIntegers(128, 60, 0);
        $virtualOutput->send($msgSent);
        usleep(100);
        $msgReceived = $input->pullMessage();

        $this->assertNotNull($msgReceived);
        assert(!is_null($msgReceived));
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

    /**
     * @dataProvider allowMaskProvider
     */
    public function testMessagesAllowing(int $filter): void
    {
        $messages = [
            Input::ALLOW_SYSEX => Message::fromIntegers(0xF0, 0x7E, 0x7F, 0x06, 0x01, 0xF7),
            Input::ALLOW_TIME => Message::fromIntegers(0xF8),
            Input::ALLOW_SENSE => Message::fromIntegers(0xFE),
        ];
        $browser = new MidiBrowser();
        $virtualOutput = $browser->openVirtualOutput('Test Input');
        $input = $browser->openInput('Test Input');

        $input->allow($filter);
        foreach ($messages as $type => $msg) {
            $virtualOutput->send($msg);
            usleep(100);
            $this->assertEquals($filter & $type, $input->pullMessage() !== null, "Filter type [$type]");
        }
    }

    /**
     * @return iterable<array<int>>
     */
    public function allowMaskProvider(): iterable
    {
        yield [Input::ALLOW_NONE];
        yield [Input::ALLOW_SYSEX];
        yield [Input::ALLOW_TIME];
        yield [Input::ALLOW_SENSE];
        yield [Input::ALLOW_SYSEX | Input::ALLOW_TIME];
        yield [Input::ALLOW_SENSE | Input::ALLOW_TIME];
        yield [Input::ALLOW_SENSE | Input::ALLOW_SYSEX];
        yield [Input::ALLOW_ALL];
    }
}
