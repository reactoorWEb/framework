<?php
/* ============================================================================
 * Copyright 2020 Zindex Software
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

namespace illustrate\JsonSchema\Test;

use stdClass;
use illustrate\JsonSchema\Helper;
use illustrate\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use illustrate\JsonSchema\Errors\ErrorFormatter;
use illustrate\JsonSchema\Errors\ValidationError;

class ErrorFormatterTest extends TestCase
{

    protected Validator $validator;

    protected ErrorFormatter $formatter;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->validator = new Validator();
        $this->formatter = new ErrorFormatter();
    }

    /**
     * @dataProvider validationsProvider
     */
    public function testFormatter($schema, $data, $errors, $maxErrors = 1, $name = null, $skipDraft = null)
    {
        if ($skipDraft) {
            $draft = $this->validator->parser()->defaultDraftVersion();
            if (is_string($skipDraft) && $draft === $skipDraft || (is_array($skipDraft) && in_array($draft, $skipDraft))) {
                $this->assertTrue(true);
                return;
            }
        }
        $this->validator->setMaxErrors($maxErrors);

        $result = $this->validator->dataValidation($data, $schema);

        if ($result === null) {
            $this->assertNull($errors);

            return;
        }

        $this->assertInstanceOf(stdClass::class, $errors);

        if (property_exists($errors, 'nested')) {
            $this->assertEquals($errors->nested, $this->formatter->formatNested($result, [$this, 'nestedCallback']), 'Nested format - ' . $name);
        }

        if (property_exists($errors, 'flat')) {
            $this->assertEquals($errors->flat, $this->formatter->formatFlat($result, [$this, 'flatCallback']), 'Flat format - ' . $name);
        }

        if (property_exists($errors, 'keyed')) {
            $this->assertEquals($errors->keyed, (object)$this->formatter->formatKeyed($result, [$this, 'keyedCallback']), 'Keyed format - ' . $name);
        }

        if (property_exists($errors, 'output')) {
            $this->assertEquals($errors->output, Helper::convertAssocArrayToObject($this->formatter->formatOutput($result, 'detailed')), 'Output format - ' . $name);
        }

        if (property_exists($errors, 'custom')) {
            $this->assertEquals($errors->custom, (object)$this->formatter->format($result), 'Custom format - ' . $name);
        }
    }

    /**
     * @param ValidationError $error
     * @param array $subErrors
     * @return stdClass
     */
    public function nestedCallback(ValidationError $error, array $subErrors = []): stdClass
    {
        return (object)[
            'kwd' => $error->keyword(),
            'msg' => $this->formatter->formatErrorMessage($error),
            'path' => $error->data()->fullPath(),
            'args' => (object)$error->args(),
            'sub' => $subErrors,
        ];
    }

    /**
     * @param ValidationError $error
     * @return stdClass
     */
    public function flatCallback(ValidationError $error): stdClass
    {
        return (object)[
            'kwd' => $error->keyword(),
            'msg' => $this->formatter->formatErrorMessage($error),
            'path' => $error->data()->fullPath(),
            'args' => (object)$error->args(),
        ];
    }

    /**
     * @param ValidationError $error
     * @return stdClass
     */
    public function keyedCallback(ValidationError $error): stdClass
    {
        return (object)[
            'kwd' => $error->keyword(),
            'msg' => $this->formatter->formatErrorMessage($error),
            'path' => $error->data()->fullPath(),
            'args' => (object)$error->args(),
        ];
    }

    /**
     * @return array
     */
    public function validationsProvider(): array
    {
        /** @var \stdClass[] $data */
        $data = $this->getErrorsTestData();

        $list = [];

        foreach ($data as $group) {
            $maxErrors = $group->maxErrors ?? 1;
            $skipDraft = $group->skipDraft ?? null;
            foreach ($group->tests as $test) {
                $list[] = [
                    $group->schema,
                    $test->data,
                    $test->errors,
                    $test->maxErrors ?? $maxErrors,
                    $test->name ?? null,
                    $skipDraft,
                ];
            }
        }

        return $list;
    }

    protected function getErrorsTestData(): array
    {
        $dir = __DIR__ . '/errors/';

        $data = [];

        foreach (glob($dir . '*.json') as $file) {
            $content = file_get_contents($file);
            $content = json_decode($content, false);
            $data = array_merge($data, $content);
        }

        return $data;
    }
}