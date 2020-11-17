<?php

namespace bviguier\tests\RtMidi;

use bviguier\RtMidi\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreateMessageFromInteger(): void
    {
        $data = array_fill(0, Message::MAX_LENGTH, 128);

        $msg = Message::fromIntegers(...$data);

        $this->assertSame(count($data), $msg->size());
        $this->assertSame($data, $msg->toIntegers());
        $this->assertSame(join('', array_map('chr', $data)), $msg->toBinString());
    }

    public function testCreateMessageFromBinString(): void
    {
        $binData = str_pad('', Message::MAX_LENGTH, '1');
        $data = array_map('ord', str_split($binData));

        $msg = Message::fromBinString($binData);

        $this->assertSame(strlen($binData), $msg->size());
        $this->assertSame($binData, $msg->toBinString());
        $this->assertSame($data, $msg->toIntegers());
    }

    public function testCannotCreateEmptyMessage(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Message cannot be empty.');

        Message::fromBinString('');
    }

    public function testCannotCreateTooLongMessageFromIntegers(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Message size cannot exceed '.Message::MAX_LENGTH.' bytes.');

        Message::fromIntegers(...array_fill(0, Message::MAX_LENGTH + 1, 0));
    }

    public function testCannotCreateTooLongMessageFromBinString(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Message size cannot exceed '.Message::MAX_LENGTH.' bytes.');

        Message::fromBinString(str_pad('', Message::MAX_LENGTH + 1, '0'));
    }

    /**
     * @dataProvider invalidByteProvider
     */
    public function testMessageIntegersInvalidValues(int $byte): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('Byte value must be between 0 and 127.');

        Message::fromIntegers($byte);
    }

    /**
     * @return iterable<array<int>>
     */
    public function invalidByteProvider(): iterable
    {
        yield [-1];
        yield [256];
    }

    public function testAccessBytes(): void
    {
        $data = [128, 60, 0];
        $msg = Message::fromIntegers(...$data);

        foreach ($data as $index => $byte) {
            $this->assertSame($byte, $msg->byte($index), "Byte $index");
        }
    }

    /**
     * @dataProvider invalidIndexProvider
     */
    public function testInvalidIndex(int $index): void
    {
        $msg = Message::fromIntegers(128, 60, 0);
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('Out of range byte offset.');

        $msg->byte($index);
    }

    /**
     * @return iterable<array<int>>
     */
    public function invalidIndexProvider(): iterable
    {
        yield [-1];
        yield [5];
    }
}
