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

namespace Opis\FileSystem\Test;

use Opis\FileSystem\Handler\CachedHandler;
use Opis\FileSystem\Handler\IFileSystemHandler;
use Opis\FileSystem\Handler\LocalFileHandler;

class LocalHandlerTest extends AbstractHandler
{
    use FilesTrait;

    public static function handler(): IFileSystemHandler
    {
        return new LocalFileHandler(self::copyFiles(__DIR__ . '/files/local', __DIR__ . '/files'));
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass()
    {
        /** @var LocalFileHandler $h */
        $h = static::$handler;
        if (!$h) {
            return;
        }

        if ($h instanceof CachedHandler) {
            $h = $h->handler();
        }

        if ($h instanceof LocalFileHandler) {
            self::deleteFiles($h->root(), true);
            return;
        }

        $dir = $h->dir('/');

        while ($item = $dir->next()) {
            if ($item->stat()->isDir()) {
                $h->rmdir($item->path(), true);
            } else {
                $h->unlink($item->path());
            }
        }
    }
}