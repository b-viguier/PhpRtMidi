#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

$browser = new \bviguier\RtMidi\MidiBrowser();

$virtualInputName = 'My own RtMidi inteface';
$input = $browser->openVirtualInput($virtualInputName);

echo "Logging messages received from [$virtualInputName], use Ctr-C to exit…\n";
while (true) {
    if ($msg = $input->pullMessage()) {
        echo '# '.join('-', array_map(
            fn($byte) => 'Ox'.str_pad(dechex($byte), 2, '0', STR_PAD_LEFT),
            $msg->toIntegers()
        )) . PHP_EOL;
    }
    usleep(100);
}
