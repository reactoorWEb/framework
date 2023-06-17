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
use illustrate\FileSystem\Handler\CachedHandler;
use illustrate\FileSystem\IProtocolInfo;
use illustrate\FileSystem\Traits\DirectoryFullPathTrait;

final class CachedDirectory implements IDirectory
{
    use DirectoryFullPathTrait;

    /** @var \Opis\FileSystem\Directory\IDirectory */
    private $directory;
    /** @var CachedHandler */
    private $handler;
    /** @var string */
    private $path;

    /**
     * CachedDirectory constructor.
     * @param IDirectory $directory
     * @param CachedHandler $handler
     */
    public function __construct(IDirectory $directory, CachedHandler $handler)
    {
        $this->directory = $directory;
        $this->handler = $handler;
        $this->path = $directory->path();

        if ($directory instanceof IProtocolInfo) {
            $this->protocol = $directory->protocol();
        }
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function doNext(): ?IFileInfo
    {
        if ($this->directory === null) {
            return null;
        }

        if ($info = $this->directory->next()) {
            $this->handler->updateCache($info);
        }

        return $info;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): bool
    {
        if ($this->directory === null) {
            return false;
        }

        return $this->directory->rewind();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->directory !== null) {
            $this->directory->close();
            $this->directory = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        $this->close();
        $this->handler = null;
    }
}