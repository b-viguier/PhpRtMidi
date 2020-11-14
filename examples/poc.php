<?php

require __DIR__ . '/../vendor/autoload.php';

$browser = new \bviguier\RtMidi\MidiBrowser();

foreach ($browser->availableAPIs() as $API) {
    echo "API: $API\n";
}

echo PHP_EOL;

$inputs = $browser->availableInputs();
var_dump($inputs);

$input = $browser->openInput('MIDI interne Bus 2');
$output = $browser->openOutput('MIDI interne Bus 1');
echo "Waiting input on {$input->name()}\n";
echo "Sending output on {$output->name()}\n";

$start = time();
$continue = true;
$inCount = 0;
while($continue) {
    while($msg = $input->pullMessage()) {
        ++$inCount;
        echo "[$inCount] " . join('-', $msg->toIntegers()) . PHP_EOL;
    }
    $output->send(\bviguier\RtMidi\Message::fromIntegers([144, 60, 80]));
    usleep(500000);
    $output->send(\bviguier\RtMidi\Message::fromIntegers([128, 60, 0]));
    usleep(500000);

    $continue = (time() - $start) <= 30;
}


