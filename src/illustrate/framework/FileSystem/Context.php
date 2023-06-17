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

class Context
{
    /** @var array */
    protected $options;

    /** @var bool */
    protected $blocking = false;

    /** @var int */
    protected $readTimeout = 0;

    /** @var int */
    protected $readMode = STREAM_BUFFER_NONE;

    /** @var int */
    protected $readBuffer = 8192;

    /** @var int */
    protected $writeMode = STREAM_BUFFER_NONE;

    /** @var int */
    protected $writeBuffer = 8192;

    /** @var string */
    protected $protocol;

    /** @var resource|null */
    protected $context = null;

    /**
     * Context constructor.
     * @param string $protocol
     * @param null $context
     * @param array $options
     */
    public function __construct(string $protocol, $context = null, array $options = [])
    {
        $this->protocol = $protocol;

        // Get custom options
        if ($context && is_resource($context)) {
            $this->context = $context;
            $params = @stream_context_get_options($context)[$protocol] ?? null;
            if ($params && is_array($params)) {
                $options += $params;
            }
        }

        // Get default options
        $params = @stream_context_get_default()[$protocol] ?? null;
        if ($params && is_array($params)) {
            $options += $params;
        }


        $this->options = $options;
    }

    /**
     * @return string
     */
    public function protocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return null|resource
     */
    public function resource()
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function isBlocking(): bool
    {
        return $this->blocking;
    }

    /**
     * @param bool $blocking
     * @return Context
     */
    public function setIsBlocking(bool $blocking): self
    {
        $this->blocking = $blocking;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadTimeout(): int
    {
        return $this->readTimeout;
    }

    /**
     * @param int $timeout
     * @return Context
     */
    public function setReadTimeout(int $timeout): self
    {
        $this->readTimeout = $timeout;

        return $this;
    }

    /**
     * @return int One of STREAM_BUFFER_* constants
     */
    public function getReadMode(): int
    {
        return $this->readMode;
    }

    /**
     * @param int $mode One of STREAM_BUFFER_* constants
     * @return Context
     */
    public function setReadMode(int $mode): self
    {
        $this->readMode = $mode;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadBufferSize(): int
    {
        return $this->readBuffer;
    }

    /**
     * @param int $size
     * @return Context
     */
    public function setReadBufferSize(int $size): self
    {
        $this->readBuffer = $size;

        return $this;
    }

    /**
     * @return int One of STREAM_BUFFER_* constants
     */
    public function getWriteMode(): int
    {
        return $this->writeMode;
    }

    /**
     * @param int $mode One of STREAM_BUFFER_* constants
     * @return Context
     */
    public function setWriteMode(int $mode): self
    {
        $this->writeMode = $mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getWriteBufferSize(): int
    {
        return $this->writeBuffer;
    }

    /**
     * @param int $size
     * @return Context
     */
    public function setWriteBufferSize(int $size): self
    {
        $this->writeBuffer = $size;

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return Context
     */
    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return Context
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}