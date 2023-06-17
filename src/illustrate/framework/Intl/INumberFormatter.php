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

interface INumberFormatter
{
    /**
     * @param int|float|string $value
     * @return string
     */
    public function formatDecimal($value): string;

    /**
     * @param int|float|string $value
     * @return string
     */
    public function formatPercent($value): string;

    /**
     * @param int|float|string $value
     * @param string|null $currency
     * @return string
     */
    public function formatCurrency($value, string $currency = null): string;
}