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

namespace illustrate\FileSystem\Handler;

use illustrate\Stream\IStream;
use illustrate\FileSystem\Directory\IDirectory;
use illustrate\FileSystem\File\{IFileInfo, Stat};

interface IFileSystemHandler
{
    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return null|IFileInfo
     */
    public function mkdir(string $path, int $mode = 0777, bool $recursive = true): ?IFileInfo;

    /**
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    public function rmdir(string $path, bool $recursive = true): bool;

    /**
     * @param string $path
     * @return bool
     */
    public function unlink(string $path): bool;

    /**
     * @param string $from
     * @param string $to
     * @return null|IFileInfo
     */
    public function rename(string $from, string $to): ?IFileInfo;

    /**
     * @param string $from
     * @param string $to
     * @param bool $overwrite
     * @return null|IFileInfo
     */
    public function copy(string $from, string $to, bool $overwrite = true): ?IFileInfo;

    /**
     * @param string $path
     * @param bool $resolve_links
     * @return Stat|null
     */
    public function stat(string $path, bool $resolve_links = true): ?Stat;

    /**
     * @param string $path
     * @param IStream $stream
     * @param int $mode
     * @return null|IFileInfo
     */
    public function write(string $path, IStream $stream, int $mode = 0777): ?IFileInfo;

    /**
     * @param string $path
     * @param string $mode
     * @return IStream|null
     */
    public function file(string $path, string $mode = 'rb'): ?IStream;

    /**
     * @param string $path
     * @return IDirectory|null
     */
    public function dir(string $path): ?IDirectory;

    /**
     * @param string $path
     * @return IFileInfo|null
     */
    public function info(string $path): ?IFileInfo;
}