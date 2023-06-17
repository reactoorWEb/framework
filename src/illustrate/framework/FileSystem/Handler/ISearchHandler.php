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

interface ISearchHandler
{
    /**
     * @param string $path
     * @param string $text
     * @param callable|null $filter
     * @param array|null $options
     * @param int|null $depth
     * @param int|null $limit
     * @return iterable|IFileInfo[]
     */
    public function search(string $path, string $text, ?callable $filter = null, ?array $options = null, ?int $depth = 0, ?int $limit = null): iterable;
}