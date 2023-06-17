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

namespace illustrate\JsonSchema\Parsers\Keywords;

use illustrate\JsonSchema\Keyword;
use illustrate\JsonSchema\Info\SchemaInfo;
use illustrate\JsonSchema\Keywords\PropertiesKeyword;
use illustrate\JsonSchema\Parsers\{KeywordParser, SchemaParser};

class PropertiesKeywordParser extends KeywordParser
{
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE_OBJECT;
    }

    /**
     * @inheritDoc
     */
    public function parse(SchemaInfo $info, SchemaParser $parser, object $shared): ?Keyword
    {
        $schema = $info->data();

        if (!$this->keywordExists($schema)) {
            return null;
        }

        $value = $this->keywordValue($schema);

        if (!is_object($value)) {
            throw $this->keywordException("{keyword} must be an object", $info);
        }

        $list = [];

        foreach ($value as $name => $s) {
            if (!is_bool($s) && !is_object($s)) {
                throw $this->keywordException("{keyword}[{$name}] must be a json schema (object or boolean)", $info);
            }

            $list[$name] = $s;
        }

        return $list ? new PropertiesKeyword($list) : null;
    }
}