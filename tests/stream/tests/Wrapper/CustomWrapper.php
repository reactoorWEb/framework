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


namespace illustrate\Stream\Test\Wrapper;

use illustrate\Stream\{Content, IContent, Wrapper\AbstractContentStreamWrapper};

class CustomWrapper extends AbstractContentStreamWrapper
{
    /**
     * @inheritDoc
     */
    protected function content(string $path): ?IContent
    {
        $path = explode('://', $path, 2);

        if (count($path) !== 2 || $path[0] !== static::protocol()) {
            return null;
        }

        return new Content($path[1]);
    }

    /**
     * @inheritDoc
     */
    public static function protocol(): string
    {
        return 'custom';
    }
}