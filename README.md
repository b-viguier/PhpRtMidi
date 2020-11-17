# PhpRtMidi
Php library for realtime MIDI input/output, thanks to [RtMidi](https://github.com/thestk/rtmidi) and [FFI](https://www.php.net/manual/en/book.ffi.php).

## Requirements
* Php `7.4` (for [FFI](https://www.php.net/manual/en/book.ffi.php) support)
* [RtMidi](https://github.com/thestk/rtmidi) library compiled on your system. Refer to your package manager to install it, or compile it by yourself.

## Installation
```bash
composer require bviguier/php-rtmidi
```

## Features
* Send midi messages (including system exclusive)
* Receive midi messages (including system exclusive)
* Create virtual input or output

## Usage
If the [RtMidi](https://github.com/thestk/rtmidi) is not globally available on your system, you have to provide its path.
By default, `PhpRtMidi` try to load the library by its _standard_ name, but the name may depend of your OS or your build.

This library doesn't match exactly original [RtMidi](https://github.com/thestk/rtmidi) interfaces, but try to expose a straigthforward developer experience.
```php
$browser = new \bviguier\RtMidi\MidiBrowser();

$input = $browser->openInput('My Input');
$output = $browser->openOutput('My Output');

echo "Midi thru enabled, use Ctr-C to exit…\n";
while (true) {
    if ($msg = $input->pullMessage()) {
        $output->send($msg);
    }
    usleep(100);
}
```
Check [`examples` directory](https://github.com/b-viguier/PhpRtMidi/tree/main/examples) to have a better overview of its usage.

