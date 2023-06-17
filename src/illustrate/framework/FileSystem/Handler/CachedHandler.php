<?php
/* ============================================================================
 * Copyright 2019 Zindex Software
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

namespace illustrate\FileSystem\Handler;

use ArrayObject;
use illustrate\Stream\IStream;
use illustrate\FileSystem\Context;
use illustrate\FileSystem\File\{Stat, IFileInfo};
use illustrate\FileSystem\Cache\{MemoryCacheHandler, ICacheHandler};
use illustrate\FileSystem\Directory\{ArrayDirectory, CachedDirectory, IDirectory};

class CachedHandler implements IFileSystemHandler, IAccessHandler, ISearchHandler, IContextHandler
{
    /** @var IFileSystemHandler|IAccessHandler|ISearchHandler|IContextHandler */
    protected $handler;
    /** @var ICacheHandler */
    protected $cache;
    /** @var null|ArrayObject|IFileInfo[] */
    protected $data = null;
    /** @var bool */
    protected $lazyDirCache = false;
    /** @var bool */
    protected $ignoreLinks = true;
    /** @var bool */
    protected $isContextHandler = false;
    /** @var bool */
    protected $isAccessHandler = false;
    /** @var bool */
    protected $isSearchHandler = false;

    /**
     * CachedHandler constructor.
     * @param IFileSystemHandler $handler
     * @param null|ICacheHandler $cache
     * @param bool $lazy_dir_cache
     * @param bool $ignore_links
     */
    public function __construct(
        IFileSystemHandler $handler,
        ?ICacheHandler $cache = null,
        bool $lazy_dir_cache = false,
        bool $ignore_links = true
    )
    {
        $this->handler = $handler;
        $this->cache = $cache ?? new MemoryCacheHandler();
        $this->lazyDirCache = $lazy_dir_cache;
        $this->ignoreLinks = $ignore_links;

        $this->isAccessHandler = $handler instanceof IAccessHandler;
        $this->isContextHandler = $handler instanceof IContextHandler;
        $this->isSearchHandler = $handler instanceof ISearchHandler;
    }

    /**
     * @return IFileSystemHandler
     */
    public function handler(): IFileSystemHandler
    {
        return $this->handler;
    }

    /**
     * @return ICacheHandler
     */
    public function cache(): ICacheHandler
    {
        return $this->cache;
    }

    /**
     * Initialize cached data
     */
    protected function initCache(): void
    {
        if ($this->data === null) {
            $this->data = $this->cache->load() ?? new ArrayObject();
        }
    }

    /**
     * @param IFileInfo $item
     * @return bool
     */
    public function updateCache(IFileInfo $item): bool
    {
        $this->initCache();

        $path = trim($item->path(), ' /');

        $this->data[$path] = $item;

        return $this->cache->save($this->data);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function removeCache(string $path): bool
    {
        $this->initCache();

        $path = trim($path, ' /');

        if (isset($this->data[$path])) {
            unset($this->data[$path]);
            return $this->cache->save($this->data);
        }

        return false;
    }

    /**
     * @param null|string $dir
     * @return bool
     */
    public function clearCache(?string $dir = null): bool
    {
        $this->initCache();

        if ($this->data->count() === 0) {
            return true;
        }

        if ($dir === null) {
            $dir = '';
        } else {
            $dir = trim($dir, ' /');
        }

        if ($dir === '') {
            if ($this->data->count() > 0) {
                $this->data = new ArrayObject();
                return $this->cache->save($this->data);
            }
            return false;
        }

        $changed = false;

        // Remove dir info
        if (isset($this->data[$dir])) {
            $changed = true;
            unset($this->data[$dir]);
        }

        $dir .= '/';

        // Search for sub-paths
        foreach ($this->data as $name => $info) {
            if (strpos($name, $dir) === 0) {
                $changed = true;
                unset($this->data[$name]);
            }
        }

        if ($changed) {
            return $this->cache->save($this->data);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function rebuildCache(): bool
    {
        if ($info = $this->handler->info('/')) {
            $data = $this->rebuildCacheData(new ArrayObject(), $info, $this->handler);

            if ($this->cache->save($data)) {
                $this->data = $data;
                $this->cache->commit();
                return true;
            }
        }

        return false;
    }

    /**
     * @param ArrayObject $data
     * @param IFileInfo $file
     * @param IFileSystemHandler $handler
     * @return ArrayObject
     */
    protected function rebuildCacheData(ArrayObject $data, IFileInfo $file, IFileSystemHandler $handler): ArrayObject
    {
        $path = trim($file->path(), ' /');
        $data[$path] = $file;

        if ($file->stat()->isDir() && ($dir = $handler->dir($path))) {
            while ($item = $dir->next()) {
                $this->rebuildCacheData($data, $item, $handler);
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function stat(string $path, bool $resolve_links = true): ?Stat
    {
        if (!$resolve_links && !$this->ignoreLinks) {
            return $this->handler->stat($path, false);
        }

        if ($info = $this->info($path)) {
            return $info->stat();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function dir(string $path): ?IDirectory
    {
        $this->initCache();

        $path = trim($path, ' /');

        if (isset($this->data[$path])) {
            $path .= '/';
            $len = strlen($path);
            $files = [];

            foreach ($this->data as $name => $info) {
                if (strpos($name, $path) === false) {
                    continue;
                }

                $name = substr($name, $len);

                if (strpos($name, '/') === false) {
                    $files[] = $info;
                }
            }

            if ($files) {
                return new ArrayDirectory($path, $files);
            }
        }

        if (($dir = $this->handler->dir($path)) === null) {
            return null;
        }

        if ($this->lazyDirCache) {
            return new CachedDirectory($dir, $this);
        }

        while ($item = $dir->next()) {
            $this->updateCache($item);
        }

        $dir->rewind();

        return $dir;
    }

    /**
     * @inheritDoc
     */
    public function info(string $path): ?IFileInfo
    {
        $this->initCache();

        $path = trim($path, ' /');

        if (isset($this->data[$path])) {
            return $this->data[$path];
        }

        if ($info = $this->handler->info($path)) {
            $this->updateCache($info);
        }

        return $info;
    }

    /**
     * @inheritDoc
     */
    public function mkdir(string $path, int $mode = 0777, bool $recursive = true): ?IFileInfo
    {
        if ($item = $this->handler->mkdir($path, $mode, $recursive)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function rmdir(string $path, bool $recursive = true): bool
    {
        if ($this->handler->rmdir($path, $recursive)) {
            $this->clearCache($path);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function unlink(string $path): bool
    {
        if ($this->handler->unlink($path)) {
            $this->removeCache($path);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function touch(string $path, int $time, ?int $atime = null): ?IFileInfo
    {
        if (!$this->isAccessHandler) {
            return null;
        }

        if ($item = $this->handler->touch($path, $time, $atime)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function chmod(string $path, int $mode): ?IFileInfo
    {
        if (!$this->isAccessHandler) {
            return null;
        }

        if ($item = $this->handler->chmod($path, $mode)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function chown(string $path, string $owner): ?IFileInfo
    {
        if (!$this->isAccessHandler) {
            return null;
        }

        if ($item = $this->handler->chown($path, $owner)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function chgrp(string $path, string $group): ?IFileInfo
    {
        if (!$this->isAccessHandler) {
            return null;
        }

        if ($item = $this->handler->chgrp($path, $group)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function rename(string $from, string $to): ?IFileInfo
    {
        if ($item = $this->handler->rename($from, $to)) {
            $this->clearCache($from);
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function copy(string $from, string $to, bool $overwrite = true): ?IFileInfo
    {
        if ($item = $this->handler->copy($from, $to, $overwrite)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, IStream $stream, int $mode = 0777): ?IFileInfo
    {
        if ($item = $this->handler->write($path, $stream, $mode)) {
            $this->updateCache($item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function file(string $path, string $mode = 'rb'): ?IStream
    {
        // No cache here
        return $this->handler->file($path, $mode);
    }

    /**
     * @inheritDoc
     */
    public function search(
        string $path,
        string $text,
        ?callable $filter = null,
        ?array $options = null,
        ?int $depth = 0,
        ?int $limit = null
    ): iterable
    {
        return $this->isSearchHandler ? $this->handler->search($path, $text, $filter, $options, $depth, $limit) : [];
    }

    /**
     * @inheritDoc
     */
    public function setContext(?Context $context): bool
    {
        return $this->isContextHandler ? $this->handler->setContext($context) : false;
    }

    /**
     * @inheritDoc
     */
    public function getContext(): ?Context
    {
        return $this->isContextHandler ? $this->handler->getContext() : null;
    }
}