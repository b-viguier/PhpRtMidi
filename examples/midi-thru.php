#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

function select(string $title, string ...$list): string
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

$input = $browser->openInput(select("Select a MIDI input", ...$browser->availableInputs()));
$output = $browser->openOutput(select("Select a MIDI output", ...$browser->availableOutputs()));

echo "Midi thru enabled, use Ctr-C to exit…\n";
$msgCount = 0;
while (true) {
    if ($msg = $input->pullMessage()) {
        $mem = memory_get_usage();
        ++$msgCount;
        echo "\r$msgCount messages transferred (Memory $mem) ";
        $output->send($msg);
    }
    usleep(100);
}
