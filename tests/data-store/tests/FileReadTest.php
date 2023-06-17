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

namespace Opis\DataStore\Test;

use Opis\DataStore\Drivers\JSONFile;
use Opis\DataStore\IDataStore;
use PHPUnit\Framework\TestCase;

class FileReadTest extends TestCase
{
    /** @var IDataStore */
    protected $store;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->store = new JSONFile(__DIR__ . DIRECTORY_SEPARATOR . 'data', '', true);
    }

    public function testRead()
    {
        $this->assertEquals('BAR', $this->store->read('test.foo.bar'));
        $this->assertEquals(null, $this->store->read('test.foo.bar.baz'));
        $this->assertEquals('BAZ', $this->store->read(['test', 'foo', 'bar.baz']));
    }

    public function xtestReadWithDot()
    {
        $this->assertEquals(null, $this->store->read('test.other.foo.bar'));
        $this->assertEquals('BAZ', $this->store->read(['test.other', 'foo', 'bar.baz']));
    }
}