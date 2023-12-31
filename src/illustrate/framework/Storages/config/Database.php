<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2014-2015 Marius Sarca
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

namespace illustrate\Config\Storage;

use PDOException;
use illustrate\Config\ArrayHelper;
use illustrate\Database\Connection;
use illustrate\Config\StorageInterface;
use illustrate\Database\Database as OpisDatabase;

class Database implements StorageInterface
{
    /** @var \Opis\Database\Database Database. */
    protected $db;

    /** @var string Cache table. */
    protected $table;

    /** @var array Column map. */
    protected $columns;

    /** @var array Config cache. */
    protected $cache = array();

    /**
     * Constructor.
     *
     * Database storage requires a table with two columns: name and data.
     * It is recommended that name column to be unique and data column blob.
     * You can change columns name by passing the third parrameter.
     *
     * @param   \Opis\Database\Connection   $connection Database connection
     * @param   string                      $table      Table's name
     * @param   array                       $columns    Columns mapping
     */
    public function __construct(Connection $connection, $table, array $columns = array())
    {
        $this->db = new OpisDatabase($connection);
        $this->table = $table;
        $this->columns = $columns + array(
            'name' => 'name',
            'data' => 'data',
        );
    }

    /**
     * Check if an item was cached
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    protected function checkCache($name)
    {
        if (!isset($this->cache[$name])) {
            try {
                $config = $this->db
                    ->from($this->table)
                    ->where($this->columns['name'])->eq($name)
                    ->select()
                    ->fetchAssoc()
                    ->first();
                if (!$config) {
                    return false;
                }

                $this->cache[$name] = new ArrayHelper(unserialize($config[$this->columns['data']]));
            } catch (PDOException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update a record
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    protected function updateRecord($name)
    {
        try {
            return (bool) $this->db
                    ->update($this->table)
                    ->where($this->columns['name'])->eq($name)
                    ->set(array(
                        $this->columns['data'] => serialize($this->cache[$name]->toArray()),
            ));
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Insert a new record
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    protected function insertRecord($name)
    {
        try {
            return (bool) $this->db
                    ->insert(array(
                        $this->columns['name'] => $name,
                        $this->columns['data'] => serialize($this->cache[$name]->toArray()),
                    ))
                    ->into($this->table);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a record
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    protected function deleteRecord($name)
    {
        try {
            return (bool) $this->db
                    ->from($this->table)
                    ->where($this->columns['name'])->eq($name)
                    ->delete();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Write config data
     * 
     * @param   string  $name
     * @param   mixed   $value
     * 
     * @return  boolean
     */
    public function write($name, $value)
    {
        $path = explode('.', $name);
        $key = array_shift($path);

        $exists = $this->checkCache($key);

        if ($path) {
            if (!$exists) {
                $this->cache[$key] = new ArrayHelper();
            }
            $this->cache[$key]->set($path, $value);
        } else {
            $this->cache[$key] = new ArrayHelper($value);
        }

        return $exists ? $this->updateRecord($key) : $this->insertRecord($key);
    }

    /**
     * Read config data
     * 
     * @param   string      $name
     * @param   mixed|null  $default    (optional)
     * 
     * @return  mixed
     */
    public function read($name, $default = null)
    {
        $path = explode('.', $name);
        $key = array_shift($path);

        if ($this->checkCache($key)) {
            return $path ? $this->cache[$key]->get($path, $default) : $this->cache[$key]->toArray();
        }

        return $default;
    }

    /**
     * Check if the config exists
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    public function has($name)
    {
        $path = explode('.', $name);
        $key = array_shift($path);

        if ($this->checkCache($key)) {
            return $path ? $this->cache[$key]->has($path) : true;
        }

        return false;
    }

    /**
     * Delete a config
     * 
     * @param   string  $name
     * 
     * @return  boolean
     */
    public function delete($name)
    {
        $path = explode('.', $name);
        $key = array_shift($path);

        if ($path) {
            if ($this->checkCache($key) && $this->cache[$key]->delete($path)) {
                return $this->updateRecord($key);
            }
            return false;
        }

        unset($this->cache[$key]);

        return $this->deleteRecord($key);
    }
}
