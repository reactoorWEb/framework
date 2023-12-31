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

namespace illustrate\ORM\Traits;

trait SoftDeletesTrait
{
    /** @var bool */
    protected $withSoftDeleted = false;

    /** @var bool */
    protected $onlySoftDeleted = false;

    /**
     * @param bool $value
     * @return mixed|SoftDeletesTrait
     */
    public function withSoftDeleted(bool $value = true): self
    {
        $this->withSoftDeleted = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return mixed|SoftDeletesTrait
     */
    public function onlySoftDeleted(bool $value = true): self
    {
        $this->onlySoftDeleted = $this->withSoftDeleted = $value;
        return $this;
    }

}