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

namespace illustrate\FileSystem\Cache;

use ArrayObject;

abstract class AbstractCacheHandler implements ICacheHandler
{
    /** @var null|ArrayObject */
    protected $data = null;

    /** @var bool */
    protected $autoCommit = true;

    /** @var bool */
    protected $needsCommit = false;

    /**
     * @param bool $autoCommit
     */
    public function __construct(bool $autoCommit = true)
    {
        $this->autoCommit = $autoCommit;
    }

    public function __destruct()
    {
        if ($this->autoCommit) {
            $this->commit();
        }

        $this->data = null;
    }

    /**
     * @inheritDoc
     */
    public function load(): ?ArrayObject
    {
        if ($this->data === null) {
            $data = $this->loadData();

            if ($data === null) {
                return null;
            }

            $data = unserialize($data);

            if (!is_array($data)) {
                $this->needsCommit = true;
                $data = [];
            }

            $this->data = new ArrayObject($data);
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function save(ArrayObject $data): bool
    {
        $this->data = $data;
        $this->needsCommit = true;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        if ($this->data === null || !$this->needsCommit) {
            return true;
        }

        if ($this->saveData(serialize($this->data->getArrayCopy()))) {
            $this->needsCommit = false;
            return true;
        }

        return false;
    }

    /**
     * @return null|string
     */
    abstract protected function loadData(): ?string;

    /**
     * @param string $data
     * @return bool
     */
    abstract protected function saveData(string $data): bool;
}