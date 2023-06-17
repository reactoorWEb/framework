<?php
/* ============================================================================
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

namespace illustrate\Stream;

class Content implements IContent
{
    /** @var string|callable */
    protected $data;
    /** @var int|null */
    protected $created;
    /** @var int|null */
    protected $updated;
    /** @var string|null */
    protected $type;
    /** @var bool */
    protected $callable;

    /**
     * Content constructor.
     * @param string|callable $data
     * @param int|null $created
     * @param int|null $updated
     * @param null|string $type
     */
    public function __construct($data, ?int $created = null, ?int $updated = null, ?string $type = null)
    {
        $this->callable = is_callable($data);
        $this->data = $data;
        $this->created = $created;
        $this->updated = $updated;
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function data(?array $options = null): ?string
    {
        $data = $this->callable ? ($options ? ($this->data)($options) : ($this->data)()) : $this->data;

        if (is_scalar($data) || (is_object($data) && method_exists($data, '__toString'))) {
            return (string)$data;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function created(): ?int
    {
        return $this->created;
    }

    /**
     * @inheritDoc
     */
    public function updated(): ?int
    {
        return $this->updated;
    }

    /**
     * @inheritDoc
     */
    public function type(): ?string
    {
        return $this->type;
    }
}