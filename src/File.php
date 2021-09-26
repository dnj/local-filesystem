<?php

namespace dnj\Filesystem\Local;

use dnj\Filesystem\Contracts\IDirectory;
use dnj\Filesystem\Contracts\IFile;
use dnj\Filesystem\Exceptions\IOException;
use dnj\Filesystem\File as FileAbstract;

class File extends FileAbstract
{
    /**
     * Write a string to a file.
     */
    public function write(string $data): void
    {
        $wrote = @file_put_contents($this->getPath(), $data, FILE_APPEND);
        if (false === $wrote or $wrote < strlen($data)) {
            throw IOException::fromLastError($this);
        }
    }

    /**
     * Reads entire or partial file into a string.
     */
    public function read(?int $length = null): string
    {
        if (null === $length) {
            $contents = @file_get_contents($this->getPath());
        } else {
            $file = new \SplFileObject($this->getPath(), 'r');
            $contents = $file->fread($length);
        }
        if (false === $contents) {
            throw IOException::fromLastError($this);
        }

        return $contents;
    }

    /**
     * Gets file size.
     */
    public function size(): int
    {
        $size = @filesize($this->getPath());
        if (false === $size) {
            throw IOException::fromLastError($this);
        }

        return $size;
    }

    /**
     * Change location of file.
     */
    public function move(IFile $dest): void
    {
        if ($dest instanceof self) {
            $result = rename($this->getPath(), $dest->getPath());
            if (!$result) {
                throw IOException::fromLastError($this);
            }
        } else {
            $this->copyTo($dest);
            $this->delete();
        }
    }

    /**
     * Renames the file.
     */
    public function rename(string $newName): void
    {
        if (!@rename($this->getPath(), $this->directory.'/'.$newName)) {
            throw IOException::fromLastError($this);
        }
        $this->basename = $newName;
    }

    /**
     * Deletes the file.
     */
    public function delete(): void
    {
        if (!$this->exists()) {
            return;
        }
        $result = @unlink($this->getPath());
        if (false === $result) {
            throw IOException::fromLastError($this);
        }
    }

    /**
     * Calculates the md5 hash of the file.
     */
    public function md5(bool $raw = false): string
    {
        $hash = @md5_file($this->getPath(), $raw);
        if (false === $hash) {
            throw IOException::fromLastError($this);
        }

        return $hash;
    }

    /**
     * Calculate the sha1 hash of the file.
     */
    public function sha1(bool $raw = false): string
    {
        $hash = @sha1_file($this->getPath(), $raw);
        if (false === $hash) {
            throw IOException::fromLastError($this);
        }

        return $hash;
    }

    /**
     * Copies file.
     */
    public function copyTo(IFile $dest): void
    {
        if (!$dest instanceof self) {
            $dest->copyFrom($this);

            return;
        }
        $result = @copy($this->getPath(), $dest->getPath());
        if (false === $result) {
            throw IOException::fromLastError($this);
        }
    }

    public function getDirectory(): Directory
    {
        return new Directory($this->directory);
    }

    /**
     * tells whether the path does exist and is a regular file.
     */
    public function exists(): bool
    {
        return is_file($this->getPath());
    }

    /**
     * Returns canonicalized absolute pathname.
     */
    public function getRealPath(): string
    {
        $result = @realpath($this->getPath());
        if (!$result) {
            throw IOException::fromLastError($this);
        }

        return $result;
    }

    /**
     * Sets access and modification time of file.
     */
    public function touch(?int $modifiedTime = null, ?int $accessTime = null): void
    {
        if (null === $modifiedTime) {
            $modifiedTime = time();
        }
        if (null === $accessTime) {
            $accessTime = $modifiedTime;
        }
        if (!@touch($this->getPath(), $modifiedTime, $accessTime)) {
            throw IOException::fromLastError($this);
        }
    }

    /**
     * Append content to existing file.
     */
    public function append(string $data): void
    {
        $wrote = @file_put_contents($this->getPath(), $data, FILE_APPEND);
        if (false === $wrote or $wrote < strlen($data)) {
            throw IOException::fromLastError($this);
        }
    }

    /**
     * @throws \InvalidArgumentException if the given directory wasn't a local directory
     */
    public function getRelativePath(IDirectory $base): string
    {
        if (!$base instanceof Directory) {
            throw new \InvalidArgumentException('base is not a local directory');
        }

        return parent::getRelativePath($base);
    }

    /**
     * Generates a storable representation of a local directory.
     */
    public function serialize(): string
    {
        return serialize($this->getPath());
    }

    /**
     * Creates a local directory object from a stored representation.
     */
    public function unserialize($data)
    {
        $path = unserialize($data);
        $this->directory = dirname($path);
        $this->basename = basename($path);
    }
}
