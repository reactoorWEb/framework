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

namespace illustrate\Stream\Test\Scanner;

use PHPUnit\Framework\TestCase;
use illustrate\Stream\Scanner\MimeScanner;
use illustrate\Stream\{DataStream, PHPDataStream, Stream};

class MimeScannerTest extends TestCase
{
    /** @var MimeScanner */
    protected $scanner;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->scanner = new MimeScanner();
    }

    public function testImage()
    {
        $img = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z/C/HgAGgwJ/lK3Q6wAAAABJRU5ErkJggg==');

        $stream = new PHPDataStream($img, 'rb', 'image/png');
        $this->assertEquals('image/png', $this->scanner->mime($stream));

        $stream = new DataStream($img);
        $this->assertEquals('image/png', $this->scanner->mime($stream));
    }

    public function testText()
    {
        $stream = new DataStream('this is plain text');
        $this->assertEquals('text/plain', $this->scanner->mime($stream));
    }

    public function testHtml()
    {
        $stream = new DataStream('<html><body>This is HTML</body></html>');
        $this->assertEquals('text/html', $this->scanner->mime($stream));
    }

    public function testPhp()
    {
        $stream = new Stream(__FILE__);
        $this->assertEquals('text/x-php', $this->scanner->mime($stream));
    }
}