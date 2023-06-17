<?php
/* ============================================================================
 * Copyright 2018 Zindex Software
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

namespace illustrate\Stream;

final class PHPMemoryStream extends Stream
{
    /**
     * PHPMemoryStream constructor.
     * @param string|null $data
     * @param int|null $max Maximum amount of bytes to store in memory before using a temporary file
     * @param string|null $mode
     */
    public function __construct(?string $data = null, ?int $max = null, ?string $mode = null)
    {
        parent::__construct($max > 0 ? ('php://temp/maxmemory:' . $max) : 'php://memory', $mode ?? 'rb+');

        if ($data !== null && $data !== '') {
            if ($this->write($data) > 0) {
                $this->rewind();
            }
        }
    }
}