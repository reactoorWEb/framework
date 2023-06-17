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

class DeleteTest extends BaseClass
{
    public function testDeleteAll()
    {
        $expected = 'DELETE FROM "users"';
        $actual = $this->db->from("users")->delete();
        $this->assertEquals($expected, $actual);
    }

    public function testDeleteWhereCondition()
    {
        $expected = 'DELETE FROM "users" WHERE "age" < 18';
        $actual = $this->db->from("users")
            ->where('age')->lt(18)
            ->delete();
        $this->assertEquals($expected, $actual);
    }

    public function testDeleteWhereExpression()
    {
        $expected = 'DELETE FROM "users" WHERE LEN("name") < 18';
        $actual = $this->db->from("users")
            ->where(function (Expression $expr) {
                $expr->len("name");
            }, true)
            ->lt(18)
            ->delete();
        $this->assertEquals($expected, $actual);
    }
}