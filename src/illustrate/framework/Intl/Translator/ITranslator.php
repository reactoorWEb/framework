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

namespace illustrate\Intl\Translator;

interface ITranslator
{

    /**
     * @param IDriver $driver
     * @return ITranslator
     */
    public function setDriver(IDriver $driver): self;

    /**
     * @return IDriver
     */
    public function getDriver(): IDriver;

    /**
     * @param string $language
     * @return ITranslator
     */
    public function setDefaultLanguage(string $language): self;

    /**
     * @return string
     */
    public function getDefaultLanguage(): string;

    /**
     * @param string|null $language
     * @return LanguageInfo
     */
    public function language(string $language = null): LanguageInfo;

    /**
     * @param string $ns
     * @param string $key
     * @param string|null $context
     * @param array $params
     * @param int $count
     * @param null $language
     * @return string
     */
    public function translate(string $ns, string $key, string $context = null, array $params = [], int $count = 1, $language = null): string;

}