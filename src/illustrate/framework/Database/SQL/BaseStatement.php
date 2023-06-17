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

namespace illustrate\Database\SQL;

use Closure;

class BaseStatement extends WhereStatement
{

    /**
     * @param   string|string[] $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement
     */
    public function join($table, Closure $closure)
    {
        $this->sql->addJoinClause('INNER', $table, $closure);
        return $this;
    }

    /**
     * @param   string|string[] $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement
     */
    public function leftJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('LEFT', $table, $closure);
        return $this;
    }

    /**
     * @param   string|string[] $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement
     */
    public function rightJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('RIGHT', $table, $closure);
        return $this;
    }

    /**
     * @param   string|string[] $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement
     */
    public function fullJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('FULL', $table, $closure);
        return $this;
    }

    /**
     * @param   string|string[] $table
     *
     * @return  Delete|Select|BaseStatement
     */
    public function crossJoin($table)
    {
        $this->sql->addJoinClause('CROSS', $table, null);
        return $this;
    }
}