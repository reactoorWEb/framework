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

use illustrate\JsonSchema\{Helper, Keyword};
use illustrate\JsonSchema\Info\SchemaInfo;
use illustrate\JsonSchema\Keywords\{PatternKeyword, PatternDataKeyword};
use illustrate\JsonSchema\Parsers\{KeywordParser, DataKeywordTrait,
    SchemaParser};

class PatternKeywordParser extends KeywordParser
{
    use DataKeywordTrait;

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE_STRING;
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

        if ($this->isDataKeywordAllowed($parser, $this->keyword)) {
            if ($pointer = $this->getDataKeywordPointer($value)) {
                return new PatternDataKeyword($pointer);
            }
        }

        if (!is_string($value)) {
            throw $this->keywordException("{keyword} value must be a string", $info);
        }

        if (!Helper::isValidPattern($value)) {
            throw $this->keywordException("{keyword} value must be a valid regex", $info);
        }

        return new PatternKeyword($value);
    }
}