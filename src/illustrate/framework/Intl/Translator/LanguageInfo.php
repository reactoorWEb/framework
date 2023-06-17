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

use illustrate\Intl\{
    ILocale, IPlural, IDateTimeFormatter, INumberFormatter,
    Locale, Plural, DateTimeFormatter, NumberFormatter
};

class LanguageInfo
{

    /** @var ILocale */
    protected $locale;

    /** @var IPlural */
    protected $plural;

    /** @var IDateTimeFormatter */
    protected $datetime;

    /** @var INumberFormatter */
    protected $number;

    /** @var string[] */
    protected $fallback;

    /**
     * LanguageInfo constructor.
     * @param ILocale $locale
     * @param IPlural $plural
     * @param IDateTimeFormatter $date
     * @param INumberFormatter $number
     * @param array $fallback
     */
    public function __construct(ILocale $locale, IPlural $plural, IDateTimeFormatter $date, INumberFormatter $number, array $fallback = [])
    {
        $this->locale = $locale;
        $this->plural = $plural;
        $this->datetime = $date;
        $this->number = $number;
        $this->fallback = $fallback;
    }

    /**
     * @return ILocale
     */
    public function locale(): ILocale
    {
        return $this->locale;
    }

    /**
     * @return IPlural
     */
    public function plural(): IPlural
    {
        return $this->plural;
    }

    /**
     * @return IDateTimeFormatter
     */
    public function datetime(): IDateTimeFormatter
    {
        return $this->datetime;
    }

    /**
     * @return INumberFormatter
     */
    public function number(): INumberFormatter
    {
        return $this->number;
    }

    /**
     * @return string[]
     */
    public function fallback(): array
    {
        return $this->fallback;
    }

    /**
     * @param array $info
     * @return LanguageInfo
     */
    public static function fromArray(array $info): self
    {
        return static::create(
            $info['locale'] ?? null,
            $info['plural'] ?? null,
            $info['datetime'] ?? null,
            $info['number'] ?? null,
            $info['fallback'] ?? []
        );
    }

    /**
     * @param ILocale|array|string|null $locale
     * @param IPlural|array|null $plural
     * @param IDateTimeFormatter|array|null $datetime
     * @param INumberFormatter|array|null $number
     * @param string[] $fallback
     * @return LanguageInfo
     */
    public static function create($locale = null, $plural = null, $datetime = null, $number = null, array $fallback = []): self
    {
        $locale = static::parseLocale($locale);
        $plural = static::parsePlural($plural, $locale);
        $datetime = static::parseDateTime($datetime, $locale);
        $number = static::parseNumber($number, $locale);

        return new static($locale, $plural, $datetime, $number, $fallback);
    }

    /**
     * @param ILocale|array|string|null $locale
     * @return ILocale
     */
    public static function parseLocale($locale): ILocale
    {
        if ($locale instanceof ILocale) {
            return $locale;
        }
        if (is_array($locale)) {
            return Locale::fromArray($locale);
        }

        if (!is_string($locale)) {
            $locale = ILocale::SYSTEM_LOCALE;
        }

        return Locale::create($locale);
    }

    /**
     * @param IPlural|array|string|null $plural
     * @param ILocale|string|array|null $locale
     * @return IPlural
     */
    public static function parsePlural($plural, $locale = null): IPlural
    {
        if ($plural instanceof IPlural) {
            return $plural;
        }

        if (is_array($plural)) {
            return Plural::fromArray($plural);
        }

        $locale = static::parseLocale(is_string($plural) ? $plural : $locale);

        return Plural::create($locale->id());
    }

    /**
     * @param IDateTimeFormatter|array|null $datetime
     * @param ILocale|string|array|null $locale
     * @return IDateTimeFormatter
     */
    public static function parseDateTime($datetime, $locale = null): IDateTimeFormatter
    {
        if ($datetime instanceof IDateTimeFormatter) {
            return $datetime;
        }

        $locale = static::parseLocale(is_string($datetime) ? $datetime : $locale);

        if (is_array($datetime)) {
            return DateTimeFormatter::fromArray($datetime, $locale->id());
        }

        return DateTimeFormatter::create($locale->id());
    }

    /**
     * @param INumberFormatter|array|null $number
     * @param ILocale|string|array|null $locale
     * @return INumberFormatter
     */
    public static function parseNumber($number, $locale = null): INumberFormatter
    {
        if ($number instanceof INumberFormatter) {
            return $number;
        }

        $locale = static::parseLocale(is_string($number) ? $number : $locale);

        if (is_array($number)) {
            return NumberFormatter::fromArray($number, $locale->id());
        }

        return NumberFormatter::create($locale->id());
    }

}