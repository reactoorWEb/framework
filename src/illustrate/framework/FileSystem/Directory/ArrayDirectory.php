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
use illustrate\FileSystem\IProtocolInfo;
use illustrate\FileSystem\Traits\DirectoryFullPathTrait;

final class ArrayDirectory implements IDirectory, IProtocolInfo
{
    use DirectoryFullPathTrait;

    /** @var string */
    private $path;

    /** @var \Opis\FileSystem\File\IFileInfo[] */
    private $items;

    /**
     * ArrayDirectory constructor.
     * @param string $path
     * @param \Opis\FileSystem\File\IFileInfo[]|array $items
     */
    public function __construct(string $path, array $items)
    {
        $this->path = trim($path, ' /');
        $this->items = $items;
        reset($this->items);
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
        if ($this->items === null) {
            return null;
        }

        $next = current($this->items);
        next($this->items);

        return $next instanceof IFileInfo ? $next : null;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): bool
    {
        if ($this->items === null) {
            return false;
        }

        reset($this->items);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->items = null;
    }
}