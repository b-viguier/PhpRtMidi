#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';


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

echo "Logging messages received from [{$input->name()}], use Ctr-C to exit…\n";
while (true) {
    if ($msg = $input->pullMessage()) {
        echo '# '.join('-', array_map(
            fn($byte) => 'Ox'.str_pad(dechex($byte), 2, '0', STR_PAD_LEFT),
            $msg->toIntegers()
        )) . PHP_EOL;
    }
    usleep(100);
}
