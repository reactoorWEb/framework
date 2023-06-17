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

namespace illustrate\DataStore\Drivers;

use illustrate\DataStore\{
    IDataStore, PathTrait
};

class Dual implements IDataStore
{
    use PathTrait;

    /** @var IDataStore Primary storage */
    protected $primary;

    /** @var IDataStore Secondary storage */
    protected $secondary;

    /** @var bool Auto-sync storages */
    protected $autoSync;

    /**
     * DualConfig constructor.
     * @param IDataStore $primary
     * @param IDataStore $secondary
     * @param bool $auto_sync
     */
    public function __construct(IDataStore $primary, IDataStore $secondary, bool $auto_sync = true)
    {
        $this->primary = $primary;
        $this->secondary = $secondary;
        $this->autoSync = $auto_sync;
    }

    /**
     * @inheritDoc
     */
    public function read($path, $default = null)
    {
        $path = $this->normalizePath($path);
        if (empty($path)) {
            return $default;
        }

        $val = $this->primary->read($path, $this);

        if ($val === $this) {
            $val = $this->secondary->read($path, $this);
            if ($val === $this) {
                return $default;
            }
            if ($this->autoSync) {
                $this->primary->write($path, $val);
            }
        }

        return $val;
    }

    /**
     * @inheritDoc
     */
    public function write($path, $value): bool
    {
        $path = $this->normalizePath($path);
        if (empty($path)) {
            return false;
        }

        if ($this->primary->write($path, $value) || $this->autoSync) {
            return $this->secondary->write($path, $value);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($path): bool
    {
        $path = $this->normalizePath($path);
        if (empty($path)) {
            return false;
        }

        $p = $this->primary->delete($path);
        $s = $this->secondary->delete($path);

        return $p && $s;
    }

    /**
     * @inheritDoc
     */
    public function has($path): bool
    {
        $path = $this->normalizePath($path);
        if (empty($path)) {
            return false;
        }
        return $this->primary->has($path) || $this->secondary->has($path);
    }
}