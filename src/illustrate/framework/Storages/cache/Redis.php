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

use Predis\Client;
use illustrate\Cache\StorageInterface;

class Redis implements StorageInterface
{
    /** @var    \Predis\Clinet */
    protected $redis;

    /** @var    string */
    protected $prefix;

    /**
     * Constructor
     * 
     * @param   \Predis\Client  $redis  Redis client
     * @param   string          $prefix Cache key prefix
     */
    public function __construct(Client $redis, $prefix = '')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->redis = null;
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
        $this->redis->set($this->prefix . $key, is_numeric($value) ? $value : serialize($value));
        if ($ttl != 0) {
            $this->redis->expire($this->prefix . $key, $ttl);
        }
        return true;
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
        $data = $this->redis->get($this->prefix . $key);
        return $data === null ? false : (is_numeric($data) ? $data : unserialize($data));
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
        return (bool) $this->redis->exists($this->prefix . $key);
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
        return (bool) $this->redis->del($this->prefix . $key);
    }

    /**
     * Clears the user cache.
     *
     * @return  boolean
     */
    public function clear()
    {
        return (bool) $this->redis->flushdb();
    }
}
