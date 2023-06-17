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

use RuntimeException;
use illustrate\FileSystem\File\IFileInfo;
use illustrate\FileSystem\Handler\IFileSystemHandler;

class StreamWrapperMountManager extends MountManager
{
    /** @var string */
    protected $protocol;

    /** @var IFileSystemStreamWrapper|string */
    protected $wrapperClass;

    /**
     * @param IFileSystemHandler[] $handlers
     * @param string $protocol
     * @param string $wrapper_class
     */
    public function __construct(array $handlers = [], string $protocol = 'fs', string $wrapper_class = FileSystemStreamWrapper::class)
    {
        parent::__construct($handlers);

        /** @var IFileSystemStreamWrapper $wrapper_class */
        if (!is_subclass_of($wrapper_class, IFileSystemStreamWrapper::class, true)) {
            throw new RuntimeException('Invalid wrapper class ' . $wrapper_class);
        }

        if (!$wrapper_class::register($protocol, $this)) {
            throw new RuntimeException('Cannot register wrapper class ' . $wrapper_class);
        }

        $this->protocol = $protocol;
        $this->wrapperClass = $wrapper_class;
    }

    /**
     * @return string
     */
    public function protocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function streamWrapperClass(): string
    {
        return $this->wrapperClass;
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

        $from = $this->protocol . '://' . $proto_from . '/' . $from;;
        $to = $this->protocol . '://' . $proto_to . '/' . $to;

        if (!@rename($from, $to)) {
            return null;
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

        $from = $this->protocol . '://' . $proto_from . '/' . $from;;
        $to = $this->protocol . '://' . $proto_to . '/' . $to;

        if (!@copy($from, $to)) {
            return null;
        }

        return $this->info($proto_to . '://' . $to);
    }

    /**
     * @param string $path
     * @param string $mode
     * @param array|null $context_options
     * @param array|null $context_params
     * @return resource|null
     */
    public function open(string $path, string $mode = 'rb', ?array $context_options = null, ?array $context_params = null)
    {
        $path = $this->absolutePath($path, $this->protocol);
        if ($path === null) {
            return null;
        }

        $ctx = null;
        if ($context_options || $context_params) {
            $ctx = $this->createContext($context_options ?? [], $context_params);
        }
        unset($context_options, $context_params);

        if ($ctx) {
            $resource = @fopen($path, $mode, false, $ctx);
        } else {
            $resource = @fopen($path, $mode, false);
        }

        return is_resource($resource) ? $resource : null;
    }

    /**
     * @param string $path
     * @param array|null $context_options
     * @param array|null $context_params
     * @return string|null
     */
    public function contents(string $path, ?array $context_options = null, ?array $context_params = null): ?string
    {
        $path = $this->absolutePath($path, $this->protocol);
        if ($path === null) {
            return null;
        }

        $ctx = null;
        if ($context_options || $context_params) {
            $ctx = $this->createContext($context_options ?? [], $context_params);
        }
        unset($context_options, $context_params);

        $data = file_get_contents($path, false, $ctx);

        return is_string($data) ? $data : null;
    }

    /**
     * @param array $options
     * @param array|null $params
     * @return resource
     */
    public function createContext(array $options, ?array $params = null)
    {
        return stream_context_create([$this->protocol => $options], $params);
    }
}