#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

$browser = new \bviguier\RtMidi\MidiBrowser();
$apiNames = array_flip((new \ReflectionClass(\bviguier\RtMidi\Api::class))->getConstants());
$apis = $browser->availableAPIs();

echo "Here drivers compiled for your system in RtMidi:\n";
foreach ($apis as $api) {
    echo "[$api] {$apiNames[$api]}\n";
}
