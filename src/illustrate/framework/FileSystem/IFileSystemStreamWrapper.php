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

namespace illustrate\FileSystem;

use illustrate\Stream\IStreamWrapper;

interface IFileSystemStreamWrapper extends IStreamWrapper
{
    /**
     * @return bool
     */
    public function dir_closedir(): bool;

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function dir_opendir(string $path, int $options): bool;

    /**
     * Next filename or null
     * @return string|null
     */
    public function dir_readdir(): ?string;

    /**
     * @return bool
     */
    public function dir_rewinddir(): bool;

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function mkdir(string $path, int $mode, int $options): bool;

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function rmdir(string $path, int $options): bool;

    /**
     * @param string $path
     * @return bool
     */
    public function unlink(string $path): bool;

    /**
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function rename(string $from, string $to): bool;

    /**
     * @param string $from
     * @param string $to
     * @param bool $overwrite
     * @return bool
     */
    public function copy(string $from, string $to, bool $overwrite = true): bool;

    /**
     * @param string $path
     * @param int $flags
     * @return array|null
     */
    public function url_stat(string $path, int $flags): ?array;

    /**
     * @param string $path
     * @param int $option
     * @param mixed $value
     * @return bool
     */
    public function stream_metadata(string $path, int $option, $value = null): bool;

    /**
     * @param int $option
     * @param int $arg1
     * @param int|null $arg2
     * @return bool
     */
    public function stream_set_option(int $option, int $arg1, ?int $arg2 = null): bool;

    /**
     * @param string $protocol
     * @param IFileSystemHandlerManager $manager
     * @return bool
     */
    public static function register(string $protocol, IFileSystemHandlerManager $manager): bool;

    /**
     * @param string $protocol
     * @return bool
     */
    public static function unregister(string $protocol): bool;

    /**
     * @param string $protocol
     * @return bool
     */
    public static function isRegistered(string $protocol): bool;

    /**
     * @param string $protocol
     * @return IFileSystemHandlerManager|null
     */
    public static function manager(string $protocol): ?IFileSystemHandlerManager;
}