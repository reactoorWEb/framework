<?php
/* ===========================================================================
 * Copyright 2013-2016 The Reactoor\Phoenix Project
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

namespace illustrate\Config\Drivers;

use illustrate\Config\ConfigInterface;

class DualStore implements ConfigInterface
{

    /** @var ConfigInterface Primary storage */
    protected $primary;

    /** @var ConfigInterface Secondary storage */
    protected $secondary;

    /** @var bool Auto-sync storages */
    protected $autoSync;

    /**
     * DualConfig constructor.
     * @param ConfigInterface $primary
     * @param ConfigInterface $secondary
     * @param bool $autosync
     */
    public function __construct(ConfigInterface $primary, ConfigInterface $secondary, bool $autosync = true)
    {
        $this->primary = $primary;
        $this->secondary = $secondary;
        $this->autoSync = $autosync;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $name, $value) : bool
    {
        if($this->primary->write($name, $value)){
            return $this->secondary->write($name, $value);
        }
        
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $name, $default = null)
    {
        $val = $this->primary->read($name, $this);

        if ($val === $this) {
            $val = $this->secondary->read($name, $default);

            if ($this->autoSync) {
                $this->primary->write($name, $val);
            }
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name) : bool
    {
        return $this->primary->has($name) || $this->secondary->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $name) : bool
    {
        $p = $this->primary->delete($name);
        $s = $this->secondary->delete($name);
        return $p && $s;
    }
}
