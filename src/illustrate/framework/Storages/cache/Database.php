<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

namespace illustrate\Cache\Storage;

use PDOException;
use illustrate\Database\Connection;
use illustrate\Cache\StorageInterface;
use illustrate\Database\Database as OpisDatabase;

class Database implements StorageInterface
{
    /** @var    \Opis\Database\Database Database. */
    protected $db;

    /** @var    string  Cache table. */
    protected $table;

    /** @var    string  Table prefix. */
    protected $prefix;

    /** @var    array   Column map. */
    protected $columns;

    /**
     * Constructor
     * 
     * @param   Connection  $connection Database connection
     * @param   string      $table      Table's name
     * @param   string      $prefix     Cache key prefix
     * @param   array       $columns    Table's columns
     */
    public function __construct(Connection $connection, $table, $prefix = '', $columns = null)
    {
        $this->db = new OpisDatabase($connection);
        $this->table = $table;
        $prefix = trim($prefix);
        $this->prefix = $prefix === '' ? '' : $prefix . '.';

        if ($columns === null || !is_array($columns)) {
            $columns = array();
        }

        $columns += array(
            'key' => 'key',
            'data' => 'data',
            'ttl' => 'ttl',
        );

        $this->columns = $columns;
    }

    /**
     * Store variable in the cache.
     *
     * @param   string   $key    Cache key
     * @param   mixed    $value  The variable to store
     * @param   int      $ttl    (optional) Time to live
     * 
     * @return  boolean
     */
    public function write($key, $value, $ttl = 0)
    {
        $ttl = ((int) $ttl <= 0) ? 0 : ((int) $ttl + time());

        try {
            $this->delete($key);

            return $this->db
                    ->insert(array(
                        $this->columns['key'] => $this->prefix . $key,
                        $this->columns['data'] => serialize($value),
                        $this->columns['ttl'] => $ttl,
                    ))
                    ->into($this->table);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Fetch variable from the cache.
     *
     * @param   string  $key  Cache key
     * 
     * @return  mixed
     */
    public function read($key)
    {
        try {
            $cache = $this->db->from($this->table)
                ->where($this->columns['key'])->eq($this->prefix . $key)
                ->select()
                ->fetchAssoc()
                ->first();
        } catch (PDOException $e) {
            return false;
        }

        if ($cache !== false) {
            $expire = (int) $cache[$this->columns['ttl']];

            if ($expire === 0 || time() < $expire) {
                return unserialize($cache[$this->columns['data']]);
            }

            $this->delete($key);
        }

        return false;
    }

    /**
     * Returns TRUE if the cache key exists and FALSE if not.
     * 
     * @param   string   $key  Cache key
     * 
     * @return  boolean
     */
    public function has($key)
    {
        try {
            $ttlColumn = $this->columns['ttl'];

            return (bool) $this->db->from($this->table)
                    ->where($this->columns['key'])->eq($this->prefix . $key)
                    ->andWhere(function ($group) use ($ttlColumn) {
                        $group->where($ttlColumn)->eq(0)
                        ->orWhere($ttlColumn)->gt(time());
                    })
                    ->count();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a variable from the cache.
     *
     * @param   string   $key  Cache key
     * 
     * @return  boolean
     */
    public function delete($key)
    {
        try {
            return (bool) $this->db->from($this->table)
                    ->where($this->columns['key'])->eq($this->prefix . $key)
                    ->delete();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Clears the user cache.
     *
     * @return  boolean
     */
    public function clear()
    {
        try {
            $this->db->from($this->table)->delete();
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }
}
