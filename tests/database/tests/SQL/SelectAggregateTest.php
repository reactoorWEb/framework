<?php
/* ===========================================================================
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

namespace Opis\Database\Test\SQL;

use Opis\Database\SQL\Expression;

class SelectAggregateTest extends BaseClass
{
    public function testCountNoColumns()
    {
        $expected = 'SELECT COUNT(*) FROM "users"';
        $actual = $this->db->from('users')->count();
        $this->assertEquals($expected, $actual);
    }

    public function testCountOneColumn()
    {
        $expected = 'SELECT COUNT("description") FROM "users"';
        $actual = $this->db->from('users')->count('description');
        $this->assertEquals($expected, $actual);
    }

    public function testCountOneColumnDistinct()
    {
        $expected = 'SELECT COUNT(DISTINCT "description") FROM "users"';
        $actual = $this->db->from('users')->count('description', true);
        $this->assertEquals($expected, $actual);
    }

    public function testLargestValue()
    {
        $expected = 'SELECT MAX("age") FROM "users"';
        $actual = $this->db->from('users')->max('age');
        $this->assertEquals($expected, $actual);
    }

    public function testSmallestValue()
    {
        $expected = 'SELECT MIN("age") FROM "users"';
        $actual = $this->db->from('users')->min('age');
        $this->assertEquals($expected, $actual);
    }

    public function testAverageValue()
    {
        $expected = 'SELECT AVG("age") FROM "users"';
        $actual = $this->db->from('users')->avg('age');
        $this->assertEquals($expected, $actual);
    }

    public function testTotalSum()
    {
        $expected = 'SELECT SUM("age") FROM "users"';
        $actual = $this->db->from('users')->sum('age');
        $this->assertEquals($expected, $actual);
    }

    public function testExpressionAggregate()
    {
        $expected = 'SELECT SUM("friends" - "enemies") FROM "users"';
        $actual = $this->db->from('users')->sum(function (Expression $expr) {
            $expr->column('friends')->{'-'}->column("enemies");
        });
        $this->assertEquals($expected, $actual);
    }

}