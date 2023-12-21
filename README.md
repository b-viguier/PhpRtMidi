# PhpRtMidi
Php library for realtime MIDI input/output, thanks to [RtMidi](https://github.com/thestk/rtmidi) and [FFI](https://www.php.net/manual/en/book.ffi.php).

## Requirements
* Php `>=7.4` (for [FFI](https://www.php.net/manual/en/book.ffi.php) support)
* [RtMidi](https://github.com/thestk/rtmidi) *v6* library compiled on your system.

## Installation
```bash
composer require bviguier/php-rtmidi
```

On MacOS, you can install [RtMidi](https://github.com/thestk/rtmidi) globally thanks to [`brew`](https://brew.sh/).
```bash
brew install rtmidi
``` 

:warning: In Linux, package registries often provide `librtmidi.so.4` which is the version `3`!
To compile it manualy:
* Download and extract [http://www.music.mcgill.ca/~gary/rtmidi/release/rtmidi-4.0.0.tar.gz](http://www.music.mcgill.ca/~gary/rtmidi/release/rtmidi-4.0.0.tar.gz)
* `./configure`
* `make && make install`
* Be sure that the library is available in you `LD_LIBRARY_PATH` or to provide the full path to `PhpRtMidi`. 

## Features
* Send midi messages (including system exclusive)
* Receive midi messages (including system exclusive)
* Create virtual input or output

## Usage
If the [RtMidi](https://github.com/thestk/rtmidi) is not globally available on your system, you have to provide its path.
By default, `PhpRtMidi` try to load the library by its _standard_ name, but the name may depend on your OS or your build.
You can also use `LIB_RTMIDI_PATH` environment variable to provide the path to the library.

This library doesn't match exactly original [RtMidi](https://github.com/thestk/rtmidi) interfaces, but try to expose a straightforward developer experience.
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

