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

use illustrate\FileSystem\IProtocolInfo;

trait FullPathTrait
{
    protected $protocol = null;

    /**
     * @inheritDoc
     */
    public function protocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     * @return self
     */
    public function setProtocol(?string $protocol): IProtocolInfo
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fullPath(): string
    {
        return $this->protocol === null ? $this->path() : ($this->protocol . '://' . $this->path());
    }

    /**
     * @inheritdoc
     */
    abstract public function path(): string;
}