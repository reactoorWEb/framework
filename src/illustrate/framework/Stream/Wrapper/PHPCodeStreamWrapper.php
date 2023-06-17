<?php
/* ============================================================================
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

namespace illustrate\Stream\Wrapper;

use Throwable;
use illustrate\Stream\{Content, IContent, IStream};

class PHPCodeStreamWrapper extends AbstractContentStreamWrapper
{
    const PROTOCOL = 'php-code';

    /**
     * @inheritDoc
     */
    final protected function content(string $path): ?IContent
    {
        $prefix = static::PROTOCOL . '://';
        if (strpos($path, $prefix) !== 0) {
            return null;
        }

        return $this->phpCodeContent(substr($path, strlen($prefix)));
    }

    /**
     * @param string $code
     * @return IContent
     */
    protected function phpCodeContent(string $code): IContent
    {
        return new Content($code, null, null, 'text/html');
    }

    /**
     * @inheritDoc
     */
    protected function streamMeta(IContent $content, string $path, string $mode, ?array $options = null): array
    {
        return [
            'wrapper_type' => static::PROTOCOL,
            'mediatype' => $content->type(),
            'mode' => $mode,
            // no point to put uri here
        ];
    }

    /**
     * @inheritDoc
     */
    public static function protocol(): string
    {
        return static::PROTOCOL;
    }

    /**
     * @param string $code
     * @return string
     */
    public static function url(string $code): string
    {
        return static::PROTOCOL . '://' . $code;
    }

    /**
     * @param string $code
     * @param array|null $vars
     * @return mixed|Throwable
     */
    public static function include(string $code, ?array $vars = null)
    {
        $alreadyRegistered = static::isRegistered();
        if (!$alreadyRegistered) {
            if (!static::register()) {
                return false;
            }
        }

        $ret = (function (string $_____CODE_____, ?array $_____VARS_____) {
            if ($_____VARS_____) {
                extract($_____VARS_____, EXTR_SKIP);
            }
            unset($_____VARS_____);

            try {
                /** @noinspection PhpIncludeInspection */
                return include($_____CODE_____);
            } catch (Throwable $e) {
                return $e;
            }
        })(static::url($code), $vars);

        if (!$alreadyRegistered) {
            static::unregister();
        }

        return $ret;
    }

    /**
     * @param string $code
     * @param array|null $vars
     * @return string
     */
    public static function template(string $code, ?array $vars = null): string
    {
        if (!ob_start()) {
            return '';
        }

        static::include($code, $vars);

        unset($code, $vars);

        return ob_get_clean();
    }

    /**
     * @param IStream $stream
     * @param string $code
     * @param array|null $vars
     * @param int $chunk
     * @return bool
     */
    public static function streamTemplate(IStream $stream, string $code, ?array $vars = null, int $chunk = 512): bool
    {
        if ($stream->isClosed() || !$stream->isWritable()) {
            return false;
        }

        $ok = ob_start(function (string $data) use ($stream): bool {
            if ($data !== '') {
                return $stream->write($data) !== null;
            }
            return true;
        }, $chunk);

        if (!$ok) {
            return false;
        }

        unset($ok, $chunk);

        static::include($code, $vars);

        unset($code, $vars);

        return ob_end_flush();
    }
}