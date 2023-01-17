# PHP Local FileSystem (Local disk implementation of dnj/filesystem)
 
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Total Downloads][ico-downloads]][link-downloads]
 
## Introduction 

This is a simple implemenetation of [DNJ\FileSystem][repo-dnj-filesystem] for local disk based file system.
* Latest versions of PHP and PHPUnit and PHPCsFixer
* Best practices applied:
  * [`README.md`][link-readme] (badges included)
  * [`LICENSE`][link-license]
  * [`composer.json`][link-composer-json]
  * [`phpunit.xml`][link-phpunit]
  * [`.gitignore`][link-gitignore]
  * [`.php-cs-fixer.php`][link-phpcsfixer]
* Some useful resources to start coding

## How To Use
This is an implementation of local disk filesystem that allows you to work  with local files.

### File basic usage:

Read file:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('/etc/hosts');
$content = $file->read();
echo $content; // prints: 127.0.0.1       localhost 
```

Write file:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('~/adele-hello.txt');

$lyrics = <<<EOF
Hello, it's me
I was wondering if after all these years you'd like to meet
To go over everything
They say that time's supposed to heal ya, but I ain't done much healing
EOF;

try {
    $file->write($lyrics);
} catch (IOException $e) {
    // in case failed for any reason, like permission denied
}
```

Rename file:
```php
<?php
use dnj\Filesystem\Local\File;


$file = new File('~/adele-hello.txt');
$lyrics = <<<EOF
I heard that you're settled down
That you found a girl and you're married now
I heard that your dreams came true
Guess she gave you things, I didn't give to you
Old friend, why are you so shy?
EOF;

try {
    $file->write($lyrics);
} catch (IOException $e) {
    // in case failed for any reason, like permission denied, ...
}


try {
    $file->rename('adele-someone-like-you.txt');

    // or you can completly move this file to new dest:
    $destFile = new File('/tmp/adele-someone-like-you.txt');
    $file->move($destFile);
} catch (IOException $e) {
    // in case failed for any reason, like permission denied, ...
}
```

Get size of file:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('/etc/hosts');
echo $file->size(); // prints the size of file in bytes
```

Existence of file and touch it (create it) if not exists:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('~/dnj-does-file-exists.txt');
echo 'file ' . ($file->exists() ? 'exists.' : 'not exists!');

if ($file->exists()) {
    echo 'File exists.' . PHP_EOL;
} else {
    echo 'File does not exists!' . PHP_EOL;
    // touch file
    $file->touch();
}
```

Delete file:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('~/test-dnj-delete-file.txt');
$file->write('Hello World!');
$file->delete();
```
Append to file:
```php
<?php
use dnj\Filesystem\Local\File;

$file = new File('~/hello-world.txt');
$file->write('Hello');
echo $file->read(); // prints: Hello

$file->append(' World!');
echo $file->read(); // prints: Hello World!
```

### Directory basic usage:
Check directory existence:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('~/dnj/');
$dir->exists(); // return bool
```

Make directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('~/dnj/');
$dir->make();
// or:
$recursive = true;
$dir->make($recursive, 0700); // make recursivley with permission 0700
```

Get files of directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('/tmp/');
if ($dir->exists()) {
    // $directory->file() returns Generator of dnj\Filesystem\Local\File;
    $recursive = false;
    foreach ($directory->files($recursive) as $file) {
        echo $file->getPath() . PHP_EOL;
    }
}
```

Get directories of directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$parent = new Directory('/tmp/');
if ($dir->exists()) {
    // $directory->directories() returns Generator of dnj\Filesystem\Local\Directory;
    $recursive = false;
    foreach ($parent->directories($recursive) as $directory) {
        echo $directory->getPath() . PHP_EOL;
    }
}
```

Get directories of directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('/tmp/');
if ($dir->exists()) {
    // $directory->file() returns Generator of dnj\Filesystem\Local\File;
    $recursive = false;
    foreach ($directory->files($recursive) as $file) {
        echo $file->getPath() . PHP_EOL;
    }
}
```

Get items (files and directories) of directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('/tmp/');
if ($dir->exists()) {
    // $directory->file() returns Generator of dnj\Filesystem\Local\File|dnj\Filesystem\Local\Directory;
    $recursive = false;
    foreach ($directory->items($recursive) as $node) {
        echo $node->getPath() . PHP_EOL;
    }
}
```

Get size of a directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$dir = new Directory('/tmp/');
$recursively = true;
echo $dir->size($recursively); // prints the size of directory and all of it's contents
```

Get file from directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$parent = new Directory('/tmp/');
$file = $parent->file('test-file.txt');
$file->write('https://dnj.co.ir');

echo $file->getPath(); // prints: /tmp/test-file.txt
```

Get directory from directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$parent = new Directory('/tmp/');
$directory = $parent->file('test-directory');
$directory->make();

echo $directory->getPath(); // prints: /tmp/test-directory
```

Delete directory:
```php
<?php
use dnj\Filesystem\Local\Directory;

$parent = new Directory('/tmp/');
$directory = $parent->file('test-directory');
$directory->make();

echo $directory->getPath(); // prints: /tmp/test-directory

$directory->delete(); // delete directory and all of it's content
```

## About
We'll try to maintain this project as simple as possible, but Pull Requests are welcomed!

## License

The MIT License (MIT). Please see [License File][link-license] for more information.

[ico-version]: https://img.shields.io/packagist/v/dnj/local-filesystem.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dnj/local-filesystem.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dnj/local-filesystem
[link-license]: https://github.com/dnj/local-filesystem/blob/master/LICENSE
[link-downloads]: https://packagist.org/packages/dnj/local-filesystem
[link-readme]: https://github.com/dnj/local-filesystem/blob/master/README.md
[link-composer-json]: https://github.com/dnj/local-filesystem/blob/master/composer.json
[link-phpunit]: https://github.com/dnj/local-filesystem/blob/master/phpunit.xml
[link-gitignore]: https://github.com/dnj/local-filesystem/blob/master/.gitignore
[link-phpcsfixer]: https://github.com/dnj/local-filesystem/blob/master/.php-cs-fixer.php
[link-author]: https://github.com/dnj

[repo-dnj-filesystem]: https://github.com/dnj/filesystem
[repo-dnj-s3-filesystem]: https://github.com/dnj/s3-filesystem
[repo-dnj-local-filesystem]: https://github.com/dnj/local-filesystem
[repo-dnj-tmp-filesystem]: https://github.com/dnj/tmp-filesystem
