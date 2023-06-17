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

namespace illustrate\FileSystem\File;

use illustrate\FileSystem\IProtocolInfo;
use illustrate\FileSystem\Traits\FullPathTrait;

class FileInfo implements IFileInfo, IProtocolInfo
{
    use FullPathTrait;

    /** @var string */
    protected $path;
    /** @var null|string */
    protected $name = null;
    /** @var Stat */
    protected $stat;
    /** @var null|string */
    protected $mime;
    /** @var null|string */
    protected $url;
    /** @var null|array */
    protected $metadata = null;

    /**
     * @param string $path
     * @param Stat $stat
     * @param null|string $mime
     * @param null|string $url
     * @param array|null $metadata
     */
    public function __construct(
        string $path,
        Stat $stat,
        ?string $mime = null,
        ?string $url = null,
        ?array $metadata = null
    )
    {
        $this->path = $path;
        $this->stat = $stat;
        $this->mime = $mime;
        $this->url = $url;
        $this->metadata = $metadata;
    }

    /**
     * @inheritDoc
     */
    public function stat(): Stat
    {
        return $this->stat;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        if ($this->name === null) {
            $this->name = explode('/', $this->path);
            $this->name = array_pop($this->name);
        }

        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function mime(): ?string
    {
        return $this->mime;
    }

    /**
     * @inheritDoc
     */
    public function url(): ?string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function metadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        $data = ['path' => $this->path, 'stat' => $this->stat];

        if ($this->mime !== null) {
            $data['mime'] = $this->mime;
        }

        if ($this->url !== null) {
            $data['url'] = $this->url;
        }

        if ($this->metadata) {
            $data['metadata'] = $this->metadata;
        }

        return serialize($data);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $serialized = unserialize($serialized);
        $this->path = $serialized['path'] ?? null;
        $this->mime = $serialized['mime'] ?? null;
        $this->url = $serialized['url'] ?? null;
        $this->metadata = $serialized['metadata'] ?? null;
        $this->stat = $serialized['stat'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'path' => $this->path,
            'name' => $this->name(),
            'mime' => $this->mime,
            'url' => $this->url,
            'metadata' => $this->metadata ?: null,
            'stat' => $this->stat,
        ];
    }
}