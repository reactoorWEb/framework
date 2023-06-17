<?php
/* ============================================================================
 * Copyright 2019 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace illustrate\FileSystem\Directory;

use illustrate\FileSystem\File\IFileInfo;
use illustrate\FileSystem\Handler\IFileSystemHandler;
use illustrate\FileSystem\Traits\DirectoryFullPathTrait;

class LocalDirectory implements IDirectory
{
    use DirectoryFullPathTrait;

    /** @var resource|null|bool */
    protected $dir = false;
    /** @var \Opis\FileSystem\Handler\IFileSystemHandler */
    protected $fs;
    /** @var string */
    protected $path;
    /** @var string */
    protected $root;

    /**
     * LocalDirectory constructor.
     * @param IFileSystemHandler $handler
     * @param string $path
     * @param string $root
     */
    public function __construct(IFileSystemHandler $handler, string $path, string $root = '')
    {
        $this->fs = $handler;
        $this->path = $path;
        $this->root = $root;
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return '/' . trim($this->path, '/');
    }

    /**
     * @inheritDoc
     */
    public function doNext(): ?IFileInfo
    {
        if ($this->dir === false) {
            $this->dir = @opendir($this->root . $this->path);

            if (!$this->dir) {
                $this->dir = null;
                return null;
            }
        }

        do {
            $next = @readdir($this->dir);
            if ($next === false) {
                return null;
            }
            if ($next !== '.' && $next !== '..') {
                break;
            }
        } while (true);

        if ($this->path !== '' && $this->path !== '/') {
            $next = rtrim($this->path, '/') . '/' . $next;
        }

        return $this->fs->info($next);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): bool
    {
        if ($this->dir) {
            return (bool)@rewinddir($this->dir);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->dir) {
            @closedir($this->dir);
            $this->dir = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        $this->close();
    }
}