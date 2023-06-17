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

namespace illustrate\FileSystem\Traits;

trait ProtocolTrait
{
    /** @var string */
    protected $protocol = '';

    /**
     * @param string $path
     * @return string
     */
    protected function parseProtocolFromPath(string $path): string
    {
        if (strpos($path, ':') === false) {
            return '';
        }

        return strstr($path, ':', true);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function setCurrentProtocolFromPath(string $path): string
    {
        return $this->protocol = $this->parseProtocolFromPath($path);
    }

    /**
     * @return string
     */
    protected function getCurrentProtocol(): string
    {
        return $this->protocol;
    }
}