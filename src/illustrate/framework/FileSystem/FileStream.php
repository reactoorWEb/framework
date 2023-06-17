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

namespace illustrate\FileSystem;

use illustrate\Stream\Stream;
use illustrate\FileSystem\File\Stat;

class FileStream extends Stream
{
    /** @var callable */
    protected $saveHandler = null;

    /** @var null|array */
    protected $stat = null;

    /**
     * FileStream constructor.
     * @param $stream
     * @param string $mode
     * @param null|Stat $stat
     * @param callable|null $saveHandler
     * @param null|string $data
     */
    public function __construct($stream, string $mode, ?Stat $stat = null, ?callable $saveHandler = null, ?string $data = null)
    {
        parent::__construct($stream, $mode);

        $this->saveHandler = $saveHandler;

        $this->stat = $stat ? $stat->toArray() : null;

        if ($data !== null) {
            $this->write($data);
            $this->rewind();
        }
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        if (!$this->saveHandler) {
            return parent::flush();
        }

        if (!parent::flush()) {
            return false;
        }

        $pos = $this->tell();

        if ($pos === null) {
            return false;
        }

        if (!$this->rewind()) {
            return false;
        }

        $ok = (bool)($this->saveHandler)($this);

        $this->seek($pos);

        return $ok;
    }

    /**
     * @inheritDoc
     */
    public function stat(): ?array
    {
        if ($this->stat) {
            return $this->stat;
        }

        $stat = parent::stat();

        if ($stat && !$stat['mode']) {
            $stat['mode'] = $stat[0] = 0x8000 | 0777;
        }

        return $stat;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        parent::close();
        $this->stat = null;
        $this->saveHandler = null;
    }
}