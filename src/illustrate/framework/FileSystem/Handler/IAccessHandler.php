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

use illustrate\FileSystem\File\IFileInfo;

interface IAccessHandler
{
    /**
     * @param string $path
     * @param int $time
     * @param int|null $atime
     * @return null|IFileInfo
     */
    public function touch(string $path, int $time, ?int $atime = null): ?IFileInfo;

    /**
     * @param string $path
     * @param int $mode
     * @return null|IFileInfo
     */
    public function chmod(string $path, int $mode): ?IFileInfo;

    /**
     * @param string $path
     * @param string $owner
     * @return null|IFileInfo
     */
    public function chown(string $path, string $owner): ?IFileInfo;

    /**
     * @param string $path
     * @param string $group
     * @return null|IFileInfo
     */
    public function chgrp(string $path, string $group): ?IFileInfo;
}