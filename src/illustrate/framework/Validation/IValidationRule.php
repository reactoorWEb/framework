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

namespace illustrate\Validation;

interface IValidationRule
{
    /**
     * Validator's name
     *
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function getError(): string;

    /**
     * @param array $arguments
     * @return array
     */
    public function getFormattedArgs(array $arguments): array;

    /**
     * @param mixed $value
     * @param array $arguments
     * @return mixed
     */
    public function prepareValue($value, array $arguments);

    /**
     * Validate
     *
     * @param mixed $value
     * @param array $arguments
     * @return bool
     */
    public function validate($value, array $arguments): bool;
}