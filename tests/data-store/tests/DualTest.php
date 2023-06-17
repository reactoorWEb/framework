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

use Opis\DataStore\Drivers\Dual;
use Opis\DataStore\Drivers\Memory;
use Opis\DataStore\IDataStore;
use PHPUnit\Framework\TestCase;

class DualTest extends TestCase
{
    /** @var IDataStore */
    protected $store;

    /** @var IDataStore */
    protected $primary;

    /** @var IDataStore */
    protected $secondary;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->primary = new Memory([
            'foo' => [
                'bar' => 'QUX'
            ]
        ]);
        $this->secondary = new Memory([
            'foo' => [
                'bar' => 'BAR',
                'baz' => 'BAZ',
            ],
            'bar' => [
                'foo' => 'FOO',
            ],
            'baz' => [
                'foo.bar' => 'FOOBAR',
                'foo.baz' => 'FOOBAZ',
            ]
        ]);

        $this->store = new Dual($this->primary, $this->secondary, true);
    }

    public function testReadSync()
    {
        $this->assertEquals('QUX', $this->store->read('foo.bar'));
        $this->assertEquals('QUX', $this->store->read(['foo', 'bar']));

        $this->assertFalse($this->primary->has('foo.baz'));
        $this->assertTrue($this->secondary->has('foo.baz'));
        $this->assertEquals('BAZ', $this->store->read('foo.baz'));
        $this->assertTrue($this->primary->has('foo.baz'));
    }


    public function testWriteSync()
    {
        $this->assertFalse($this->primary->has('foo.qux'));
        $this->assertFalse($this->secondary->has('foo.qux'));

        $this->store->write('foo.qux', 'QUX');

        $this->assertTrue($this->store->has('foo.qux'));
        $this->assertEquals('QUX', $this->primary->read('foo.qux'));
        $this->assertEquals('QUX', $this->secondary->read('foo.qux'));
    }

    public function testDeleteSync()
    {
        $this->assertTrue($this->primary->has('foo'));
        $this->assertTrue($this->secondary->has('foo'));

        $this->assertTrue($this->store->delete('foo'));

        $this->assertFalse($this->store->has('foo'));
        $this->assertFalse($this->primary->has('foo'));
        $this->assertFalse($this->secondary->has('foo'));
    }
}