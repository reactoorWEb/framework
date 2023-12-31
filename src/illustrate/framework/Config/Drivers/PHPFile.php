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

class PHPFile extends File
{

    /**
     * PHPFile constructor.
     * @param string $path
     * @param string $prefix
     */
    public function __construct(string $path, string $prefix = '')
    {
        parent::__construct($path, $prefix, 'php');
    }

    /**
     * {@inheritdoc}
     */
    protected function readConfig(string $file)
    {
        return include($file);
    }

    /**
     * {@inheritdoc}
     */
    protected function writeConfig(string $file, $config)
    {
        $config = var_export($config, true);
        $config = str_replace('stdClass::__set_state', '(object)', $config);
        $config = "<?php\n\rreturn " . $config . ';';
        $this->fileWrite($file, $config);
    }
}
