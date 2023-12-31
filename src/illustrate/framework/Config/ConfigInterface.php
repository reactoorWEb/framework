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

namespace illustrate\Config;

interface ConfigInterface
{
    /**
     * @param string $key
     * @param $value
     * @return bool
     */
    public function write(string $key, $value) : bool;

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function read(string $key, $default = null);

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key) : bool;
    
}
