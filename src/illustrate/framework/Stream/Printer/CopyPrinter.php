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

namespace illustrate\Stream\Printer;

use InvalidArgumentException;
use illustrate\Stream\IStream;

class CopyPrinter
{
    /** @var IStream */
    protected $stream;

    /**
     * CopyPrinter constructor.
     * @param IStream $stream
     */
    public function __construct(IStream $stream)
    {
        if ($stream->isClosed() || !$stream->isWritable()) {
            throw new InvalidArgumentException('Stream is not writable');
        }

        $this->stream = $stream;
    }

    /**
     * @return IStream
     */
    public function stream(): IStream
    {
        return $this->stream;
    }

    /**
     * @param IStream $source
     * @param int $chunk
     * @return int
     */
    public function copy(IStream $source, int $chunk = 8192): int
    {
        $total = 0;

        while (!$source->isEOF()) {
            $data = $source->read($chunk);
            if ($data === null) {
                return $total;
            }

            $size = $this->stream->write($data);

            if ($size === null) {
                return $total;
            }

            $total += $size;
        }

        return $total;
    }

    /**
     * @param string $data
     * @return int|null
     */
    public function append(string $data): ?int
    {
        return $this->stream->write($data);
    }
}