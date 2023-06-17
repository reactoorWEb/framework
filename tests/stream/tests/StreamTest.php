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

namespace illustrate\Stream\Test;

use illustrate\Stream\DataStream;
use illustrate\Stream\IStream;
use illustrate\Stream\PHPDataStream;
use illustrate\Stream\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /**
     * @dataProvider streamProvider
     */
    public function testReadable(callable $factory)
    {
        $data = 'this is data';
        /** @var IStream $stream */
        $stream = $factory($data, 'r');

        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isSeekable());

        $this->assertFalse($stream->isWritable());
        $this->assertFalse($stream->isClosed());
        $this->assertFalse($stream->isEOF());

        $this->assertEquals(0, $stream->tell());
        $this->assertEquals(strlen($data), $stream->size());


        $this->assertEquals('this', $stream->read(4));
        $this->assertEquals(4, $stream->tell());
        $this->assertEquals(' ', $stream->read(1));
        $this->assertEquals('is data', $stream->readToEnd());
        $this->assertTrue($stream->isEOF());

        $this->assertNull($stream->read());

        $stream->close();
        $this->assertTrue($stream->isClosed());
    }

    /**
     * @dataProvider streamProvider
     */
    public function testReadLine(callable $factory)
    {
        $lines = [
            'a',
            'b',
            'c',
        ];

        /** @var IStream $stream */
        $stream = $factory(implode("\n", $lines), 'r');

        $list = [];
        while (($l = $stream->readLine()) !== null) {
            $list[] = $l;
        }

        $this->assertEquals($lines, $list);
    }

    /**
     * @dataProvider streamProvider
     */
    public function testWritable(callable $factory)
    {
        /** @var IStream $stream */
        $stream = $factory('', 'w');

        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->isSeekable());

        $this->assertFalse($stream->isClosed());

        $this->assertEquals(4, $stream->write('this'));
        $this->assertEquals(1, $stream->write('#'));

        $this->assertTrue($stream->seek(-1, Stream::SEEK_CUR));
        $this->assertEquals(4, $stream->write(' is '));
        $this->assertEquals(4, $stream->write('data'));

        $this->assertEquals('this is data', $stream);
    }

    /**
     * @dataProvider streamProvider
     */
    public function testSeek(callable $factory)
    {
        $data = 'this is data';
        /** @var IStream $stream */
        $stream = $factory($data, 'r+');

        $this->assertTrue($stream->isSeekable());

        $stream->seek(5, Stream::SEEK_SET);

        $this->assertEquals('is', $stream->read(2));

        $stream->seek(1, Stream::SEEK_CUR);

        $this->assertEquals('data', $stream->read(4));

        $stream->rewind();

        $this->assertEquals('this', $stream->read(4));

        $pos = $stream->tell();

        $this->assertEquals('this is data', $stream);

        $this->assertEquals($pos, $stream->tell());

        $this->assertEquals(strlen($data), $stream->size());
    }


    public function streamProvider(): array
    {
        $list[] = [
            function ($data, $mode = 'r') {
                return new Stream('data://text/plain,' . $data, $mode);
            },
        ];

        $list[] = [
            function ($data, $mode = 'r') {
                return new PHPDataStream($data, $mode);
            },
        ];

        $list[] = [
            function ($data, $mode = 'r') {
                return new DataStream($data, $mode);
            },
        ];

        return $list;
    }
}