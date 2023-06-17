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

namespace illustrate\Stream\Scanner;

use finfo;
use illustrate\Stream\IStream;

class MimeScanner
{
    /** @var finfo */
    protected $info;

    public function __construct()
    {
        $this->info = new finfo(FILEINFO_MIME_TYPE | FILEINFO_PRESERVE_ATIME);
    }

    /**
     * @param IStream $stream
     * @param int|null $max_chars
     * @return string|null
     */
    public function mime(IStream $stream, ?int $max_chars = null): ?string
    {
        if (!$stream->isReadable()) {
            return null;
        }

        if ($max_chars > 0) {
            $str = $stream->read($max_chars);
        } else {
            $str = (string) $stream;
        }

        $mime = null;
        if ($str !== null) {
            $mime = $this->info->buffer($str) ?: null;
            unset($str);
        }

        if ($mime === null || $mime === 'application/x-empty') {
            $mime = $stream->metadata('mediatype');
            if (!is_string($mime)) {
                $mime = null;
            }
        }

        return $mime;
    }
}