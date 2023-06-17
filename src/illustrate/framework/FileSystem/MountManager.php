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

namespace illustrate\FileSystem;

use illustrate\Stream\IStream;
use illustrate\FileSystem\Traits\PathTrait;
use illustrate\FileSystem\Directory\IDirectory;
use illustrate\FileSystem\File\{IFileInfo, Stat};
use illustrate\FileSystem\Handler\{IAccessHandler, IFileSystemHandler, ISearchHandler};

class MountManager implements IMountManager
{
    use PathTrait;

    /** @var iterable|IFileSystemHandler[] */
    protected $handlers;

    /**
     * @param IFileSystemHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        foreach ($handlers as $name => $handler) {
            if (is_string($name) && $handler && ($handler instanceof IFileSystemHandler)) {
                $this->mount($name, $handler);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(string $path, string $protocol): ?IFileSystemStreamPathInfo
    {
        $path = $this->parsePath($path, $protocol);
        if ($path === null) {
            return null;
        }

        if (!isset($this->handlers[$path['handler']])) {
            return null;
        }

        return new FileSystemStreamPathInfo($this->handlers[$path['handler']], $path['path']);
    }

    /**
     * @param string $name
     * @param IFileSystemHandler $handler
     * @return bool
     */
    public function mount(string $name, IFileSystemHandler $handler): bool
    {
        $this->handlers[$name] = $handler;

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function umount(string $name): bool
    {
        if (isset($this->handlers[$name])) {
            unset($this->handlers[$name]);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function handler(string $name): ?IFileSystemHandler
    {
        return $this->handlers[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function handlers(): iterable
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function mkdir(string $path, int $mode = 0777, bool $recursive = true): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$mode, $recursive]);
    }

    /**
     * @inheritDoc
     */
    public function rmdir(string $path, bool $recursive = true): bool
    {
        return $this->forward($path, __FUNCTION__, [$recursive]);
    }

    /**
     * @inheritDoc
     */
    public function unlink(string $path): bool
    {
        return $this->forward($path, __FUNCTION__, [], false);
    }

    /**
     * @inheritDoc
     */
    public function touch(string $path, int $time, ?int $atime = null): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$time, $atime], null, false, true);
    }

    /**
     * @inheritDoc
     */
    public function chmod(string $path, int $mode): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$mode], null, false, true);
    }

    /**
     * @inheritDoc
     */
    public function chown(string $path, string $owner): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$owner], null, false, true);
    }

    /**
     * @inheritDoc
     */
    public function chgrp(string $path, string $group): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$group], null, false, true);
    }

    /**
     * @inheritDoc
     */
    public function stat(string $path, bool $resolve_links = true): ?Stat
    {
        return $this->forward($path, __FUNCTION__, [$resolve_links], null);
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, IStream $stream, int $mode = 0777): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [$stream, $mode]);
    }

    /**
     * @inheritDoc
     */
    public function file(string $path, string $mode = 'rb'): ?IStream
    {
        return $this->forward($path, __FUNCTION__, [$mode], null);
    }

    /**
     * @inheritDoc
     */
    public function dir(string $path): ?IDirectory
    {
        return $this->forward($path, __FUNCTION__, [], null, true);
    }

    /**
     * @inheritDoc
     */
    public function info(string $path): ?IFileInfo
    {
        return $this->forward($path, __FUNCTION__, [], null, true);
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        return $this->stat($path) !== null;
    }

    /**
     * @inheritDoc
     */
    public function isFile(string $path): bool
    {
        if ($stat = $this->stat($path)) {
            return $stat->isFile();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDir(string $path): bool
    {
        if ($stat = $this->stat($path)) {
            return $stat->isDir();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isLink(string $path): bool
    {
        if ($stat = $this->stat($path, false)) {
            return $stat->isLink();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function rename(string $from, string $to): ?IFileInfo
    {
        if (strpos($from, '://') === false) {
            return null;
        }

        if (strpos($to, '://') === false) {
            $to = $this->mergePaths($to, $from);
            if ($to === null) {
                return null;
            }
        }

        list($proto_from, $from) = explode('://', $from, 2);
        $handler_from = $this->handler($proto_from);
        if ($handler_from === null) {
            return null;
        }
        $from = $this->normalizePath($from);

        list($proto_to, $to) = explode('://', $to, 2);
        $to = $this->normalizePath($to);

        if ($proto_from === $proto_to) {
            $info = $handler_from->rename($from, $to);
            if ($info instanceof IProtocolInfo) {
                $info->setProtocol($proto_to);
            }
            return $info;
        }

        $handler_to = $this->handler($proto_to);
        if ($handler_to === null) {
            return null;
        }

        /** @var IFileInfo $from_info */
        $from_info = $this->info($proto_from . '://' . $from);

        if ($from_info === null) {
            return null;
        }

        if (!$this->doCopy($handler_from, $from_info, $handler_to, $to, true, true)) {
            return null;
        }

        if ($from_info->stat()->isDir()) {
            $handler_from->rmdir($from_info->path(), true);
        } else {
            $handler_from->unlink($from_info->path());
        }

        return $this->info($proto_to . '://' . $to);
    }

    /**
     * @inheritDoc
     */
    public function copy(string $from, string $to, bool $overwrite = true): ?IFileInfo
    {
        if (strpos($from, '://') === false) {
            return null;
        }

        if (strpos($to, '://') === false) {
            $to = $this->mergePaths($to, $from);
            if ($to === null) {
                return null;
            }
        }

        list($proto_from, $from) = explode('://', $from, 2);
        $handler_from = $this->handler($proto_from);
        if ($handler_from === null) {
            return null;
        }
        $from = $this->normalizePath($from);

        list($proto_to, $to) = explode('://', $to, 2);
        $to = $this->normalizePath($to);

        if ($proto_from === $proto_to) {
            $info = $handler_from->copy($from, $to, $overwrite);
            if ($info instanceof IProtocolInfo) {
                $info->setProtocol($proto_to);
            }
            return $info;
        }

        $handler_to = $this->handler($proto_to);
        if ($handler_to === null) {
            return null;
        }

        /** @var IFileInfo $from_info */
        $from_info = $this->info($proto_from . '://' . $from);

        if ($from_info === null) {
            return null;
        }

        if (!$this->doCopy($handler_from, $from_info, $handler_to, $to, true, $overwrite)) {
            return null;
        }

        return $this->info($proto_to . '://' . $to);
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
        if (strpos($path, '://') === false) {
            return [];
        }

        list($protocol, $path) = explode('://', $path, 2);

        $handler = $this->handler($protocol);
        if ($handler === null || !($handler instanceof ISearchHandler)) {
            return [];
        }

        $path = $this->normalizePath($path);

        foreach ($handler->search($path, $text, $filter, $options, $depth, $limit) as $item) {
            if ($item instanceof IProtocolInfo) {
                $item->setProtocol($protocol);
            }

            yield $item;
        }
    }

    /**
     * @inheritDoc
     */
    public function copyFiltered(
        string $from,
        string $to,
        bool $recursive = true,
        bool $overwrite = true,
        ?callable $filter = null
    ): ?int
    {
        if (strpos($from, '://') === false) {
            return null;
        }

        if (strpos($to, '://') === false) {
            $to = $this->mergePaths($to, $from);
            if ($to === null) {
                return null;
            }
        }

        list($proto_from, $from) = explode('://', $from, 2);
        $handler_from = $this->handler($proto_from);
        if ($handler_from === null) {
            return null;
        }
        $from = $this->normalizePath($from);

        $from_info = $handler_from->info($from);
        if ($from_info === null) {
            return null;
        }

        list($proto_to, $to) = explode('://', $to, 2);
        $to = $this->normalizePath($to);

        if ($proto_from === $proto_to) {
            $handler_to = $handler_from;
        } else {
            $handler_to = $this->handler($proto_to);
            if ($handler_to === null) {
                return null;
            }
        }

        unset($from, $proto_from, $proto_to);

        return $this->doCopy($handler_from, $from_info, $handler_to, $to, $recursive, $overwrite, $filter);
    }

    /**
     * @inheritDoc
     */
    public function sync(string $source, string $replica, ?callable $filter = null): ?int
    {
        if (strpos($source, '://') === false) {
            return null;
        }

        if (strpos($replica, '://') === false) {
            $replica = $this->mergePaths($replica, $source);
            if ($replica === null) {
                return null;
            }
        }

        list($proto_source, $source) = explode('://', $source, 2);
        $handler_source = $this->handler($proto_source);
        if ($handler_source === null) {
            return null;
        }

        $source = $this->normalizePath($source);

        $source_info = $handler_source->info($source);
        if ($source_info === null) {
            return null;
        }

        list($proto_replica, $replica) = explode('://', $replica, 2);
        $replica = $this->normalizePath($replica);

        if ($proto_source === $proto_replica) {
            $handler_replica = $handler_source;
        } else {
            $handler_replica = $this->handler($proto_replica);
            if ($handler_replica === null) {
                return null;
            }
        }

        unset($source, $proto_source, $proto_replica);

        if (!$filter) {
            $filter = static function (IFileInfo $source, IFileInfo $replica): bool {
                $sStat = $source->stat();
                $rStat = $replica->stat();

                if ($sStat->size() !== $rStat->size()) {
                    return true;
                }

                return $sStat->mtime() > $rStat->mtime();
            };
        }

        return $this->doCopy($handler_source, $source_info, $handler_replica, $replica, true, true, null, $filter);
    }

    /**
     * @param string $path
     * @param string $protocol
     * @return string|null
     */
    public function absolutePath(string $path, string $protocol): ?string
    {
        if (strpos($path, '://') === false) {
            return null;
        }

        list($handler, $path) = explode('://', $path, 2);

        $path = $this->normalizePath($path);

        if ($path === '') {
            return $protocol . '://' . $handler;
        }

        return $protocol . '://' . $handler . '/' . $path;
    }

    /**
     * @param string $path
     * @param string|null $protocol
     * @return string|null
     */
    public function relativePath(string $path, ?string $protocol = null): ?string
    {
        $path = $this->parsePath($path, $protocol);

        if ($path === null) {
            return null;
        }

        return $path['handler'] . '://' . $path['path'];
    }

    /**
     * @param string $path
     * @param string $base
     * @param string|null $protocol
     * @return string|null
     */
    public function mergePaths(string $path, string $base, ?string $protocol = null): ?string
    {
        $absolute = $protocol !== null;

        if (strpos($path, '://') !== false) {
            return $absolute ? $this->absolutePath($path, $protocol) : $this->relativePath($path);
        }

        if (strpos($base, '://') === false) {
            return null;
        }

        list($proto, $base) = explode('://', $base, 2);

        if ($path === '') {
            return $absolute ? $this->absolutePath($base, $protocol) : $proto . '://' . $this->normalizePath($base);
        }

        if ($path[0] === '/') {
            return $absolute ? $this->absolutePath($proto . "://" . $path, $protocol) : $proto . '://' . $this->normalizePath($path);
        }

        $shouldPop = substr($base, -1, 1) !== '/';

        $base = explode('/', $this->normalizePath($base));

        if ($shouldPop) {
            array_pop($base);
        }

        foreach (explode('/', $path) as $item) {
            $item = trim($item);
            if ($item === '' || $item === '.') {
                continue;
            }

            if ($item === '..') {
                if ($base) {
                    array_pop($base);
                }
                continue;
            }

            $base[] = $item;
        }

        $base = $base ? implode('/', $base) : '';

        if ($absolute) {
            return $protocol . '://' . $proto . ($base === '' ? '' : '/' . $base);
        }

        return $proto . '://' . $base;
    }

    /**
     * @param IFileSystemHandler $from
     * @param IFileInfo $from_info
     * @param IFileSystemHandler $to
     * @param string $to_path
     * @param bool $recursive
     * @param bool $overwrite
     * @param callable|null $filter
     * @param callable|null $overwrite_filter
     * @return int
     */
    protected function doCopy(IFileSystemHandler $from, IFileInfo $from_info, IFileSystemHandler $to, string $to_path, bool $recursive, bool $overwrite, ?callable $filter = null, ?callable $overwrite_filter = null): int
    {
        if ($filter && !$filter($from_info)) {
            return 0;
        }

        $from_stat = $from_info->stat();
        $to_info = $to->info($to_path);
        $to_stat = $to_info ? $to_info->stat() : null;

        if ($from_stat->isFile()) {
            if ($to_info) {
                if (!$overwrite) {
                    return 0;
                }

                if ($overwrite_filter !== null && !$overwrite_filter($from_info, $to_info)) {
                    return 0;
                }

                if ($to_stat->isDir()) {
                    $to->rmdir($to_path, true);
                }
            }

            $file = $from->file($from_info->path());

            if ($file === null) {
                return 0;
            }

            $ok = $to->write($to_path, $file, $from_stat->mode()) !== null;

            $file->close();

            return $ok ? 1 : 0;
        }

        if (!$recursive || !$from_stat->isDir()) {
            return 0;
        }

        if ($to_info && (!$overwrite || !$to_stat->isDir())) {
            return 0;
        }

        $directory = $from->dir($from_info->path());
        if ($directory === null) {
            return 0;
        }

        unset($from_stat, $from_info, $to_info, $to_stat);

        $total = 0;

        while ($item = $directory->next()) {
            if ($filter && !$filter($item)) {
                continue;
            }
            $total += $this->doCopy($from, $item, $to, $to_path . '/' . $item->name(), $recursive, $overwrite, $filter, $overwrite_filter);
            unset($item);
        }

        return $total;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $args
     * @param bool|null|mixed $failure
     * @param bool $allowRoot
     * @param bool $access
     * @return bool|null|mixed
     */
    protected function forward(
        string $path,
        string $method,
        array $args = [],
        $failure = null,
        bool $allowRoot = false,
        bool $access = false
    )
    {
        if (strpos($path, '://') === false) {
            return $failure;
        }

        list($protocol, $path) = explode('://', $path, 2);

        $handler = $this->handler($protocol);
        if ($handler === null) {
            return $failure;
        }

        if ($access && !($handler instanceof IAccessHandler)) {
            return $failure;
        }

        $path = $this->normalizePath($path);

        if (!$allowRoot && $path === '') {
            return $failure;
        }

        array_unshift($args, $path);

        $ret = $handler->{$method}(...$args);

        if ($ret instanceof IProtocolInfo) {
            $ret->setProtocol($protocol);
        }

        return $ret;
    }
}