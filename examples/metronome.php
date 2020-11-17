#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

function select(string $title, array $list): string
{
    do {
        echo "\n===============\n";
        $list = array_values($list);
        foreach ($list as $key => $value) {
            echo "[$key] $value\n";
        }
        echo "$title\n?> ";
        $choice = fgetc(\STDIN);
    } while (!isset($list[$choice]));

    return $list[$choice];
}

$browser = new \bviguier\RtMidi\MidiBrowser();

$output = $browser->openOutput(select("Select a MIDI output", $browser->availableOutputs()));
$noteOn = \bviguier\RtMidi\Message::fromIntegers(0x90, 0x3C, 0x50);
$noteOff = \bviguier\RtMidi\Message::fromIntegers(0x80, 0x3C, 0x00);

echo "Metronome enabled on channel 1, use Ctr-C to exit…\n";
while (true) {
    $output->send($noteOn);
    usleep(500_000);
    $output->send($noteOff);
    usleep(500_000);
}
