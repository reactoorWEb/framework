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

namespace Opis\Intl\Test;

use Opis\Intl\DateTimeFormatter;
use Opis\Intl\IDateTimeFormatter;

class DateTimeTest extends \PHPUnit\Framework\TestCase
{

    public function testFormat()
    {
        $d = DateTimeFormatter::create("en_US", "full", "full", null, null, "GMT");
        $this->doTests($d);
    }

    public function testOptions()
    {
        $d = DateTimeFormatter::fromArray([
            'locale' => 'en_US',
            'date' => 'full',
            'time' => 'full',
            'calendar' => 'gregorian',
            'timezone' => 'GMT',
        ]);

        $this->doTests($d);
    }

    protected function doTests(IDateTimeFormatter $d)
    {
        $this->assertContains($d->format(0), [
            'January 1, 1970, 12:00 AM', // no intl
            'Thursday, January 1, 1970 at 12:00:00 AM GMT', // different icu
            'Thursday, January 1, 1970 at 12:00:00 AM Greenwich Mean Time',
        ]);

        $this->assertContains($d->formatDate(0), [
            'January 1, 1970', // no intl
            'Thursday, January 1, 1970',
        ]);

        $this->assertContains($d->formatTime(0), [
            '12:00 AM', // no intl
            '12:00:00 AM GMT', // different icu
            '12:00:00 AM Greenwich Mean Time',
        ]);
    }
}