<?php

namespace dnj\Filesystem\Local;

use dnj\Filesystem\Contracts\IDirectory;
use dnj\Filesystem\Directory as DirectoryAbstract;
use dnj\Filesystem\Exceptions\IOException;

class Directory extends DirectoryAbstract
{
    /**
     * @return \Generator<File>
     */
    public function files(bool $recursively): \Generator
    {
        $root = new \RecursiveDirectoryIterator($this->getPath(), \RecursiveDirectoryIterator::SKIP_DOTS);
        if ($recursively) {
            $scanner = new \RecursiveIteratorIterator($root);
        } else {
            $scanner = $root;
        }
        foreach ($scanner as $item) {
            if ($item->isFile()) {
                yield new File($item->getPathname());
            }
        }
    }

    /**
     * @return \Generator<self>
     */
    public function directories(bool $recursively): \Generator
    {
        $root = new \RecursiveDirectoryIterator($this->getPath(), \RecursiveDirectoryIterator::SKIP_DOTS);
        if ($recursively) {
            $scanner = new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::SELF_FIRST);
        } else {
            $scanner = $root;
        }
        foreach ($scanner as $item) {
            if ($item !== $root and $item->isDir()) {
                yield new self($item->getPathName());
            }
        }
    }

    /**
     * @return \Generator<self|File>
     */
    public function items(bool $recursively): \Generator
    {
        $root = new \RecursiveDirectoryIterator($this->getPath(), \RecursiveDirectoryIterator::SKIP_DOTS);
        if ($recursively) {
            $scanner = new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::SELF_FIRST);
        } else {
            $scanner = $root;
        }
        foreach ($scanner as $item) {
            if ($item !== $root and $item->isDir()) {
                yield new self($item->getPathname());
            } elseif ($item->isFile()) {
                yield new File($item->getPathname());
            }
        }
    }

    public function make(bool $recursively = true, int $mode = 0755): void
    {
        if (!@mkdir($this->getPath(), $mode, $recursively)) {
            throw IOException::fromLastError($this);
        }
    }

    public function size(bool $recursively = true): int
    {
        $size = 0;
        foreach ($this->files($recursively) as $file) {
            $size += $file->size();
        }

        return $size;
    }

    public function move(IDirectory $dest): void
    {
        if (!$dest->exists()) {
            $dest->make(true);
        }
        if (!@rename($this->getPath(), $dest->getPath().'/'.$this->basename)) {
            throw IOException::fromLastError($this);
        }
        $this->directory = $dest->getPath();
    }

    public function file(string $name): File
    {
        return new File($this->getPath().'/'.$name);
    }

    public function directory(string $name): self
    {
        return new self($this->getPath().'/'.$name);
    }

    public function exists(): bool
    {
        return is_dir($this->getPath());
    }

    public function getDirectory(): self
    {
        return new self($this->directory);
    }

    public function rename(string $newName): void
    {
        if (!@rename($this->getPath(), $this->directory.'/'.$newName)) {
            throw IOException::fromLastError($this);
        }
        $this->basename = $newName;
    }

    public function getRealPath(): string
    {
        $result = @realpath($this->getPath());
        if (!$result) {
            throw IOException::fromLastError($this);
        }

        return $result;
    }

    public function delete(): void
    {
        if (!$this->exists()) {
            return;
        }
        parent::delete();
        if (!@rmdir($this->getPath())) {
            throw IOException::fromLastError($this);
        }
    }

    /**
     * @throws \InvalidArgumentException if the given directory wasn't a local directory
     */
    public function getRelativePath(IDirectory $base): string
    {
        if (!$base instanceof self) {
            throw new \InvalidArgumentException('base is not a local directory');
        }

        return parent::getRelativePath($base);
    }

    public function serialize(): string
    {
        return serialize($this->getPath());
    }

    public function unserialize($data)
    {
        $path = unserialize($data);
        $this->directory = dirname($path);
        $this->basename = basename($path);
    }
}
