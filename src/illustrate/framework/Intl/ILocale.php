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

namespace illustrate\Intl;

interface ILocale
{
    /** Default locale string for system */
    const SYSTEM_LOCALE = 'en__SYSTEM';

    /**
     * Get locale name
     * @return string
     */
    public function id(): string;

    /**
     * Get language code
     * @return string
     */
    public function language(): string;

    /**
     * Get script code
     * @return null|string
     */
    public function script();

    /**
     * Get region code
     * @return null|string
     */
    public function region();

    /**
     * Check if right to left
     * @return bool
     */
    public function rtl(): bool;
}