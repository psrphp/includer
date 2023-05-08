<?php

declare(strict_types=1);

namespace PsrPHP\Includer;

use LogicException;

class Wrapper
{
    private static $code;
    private static $args;
    private static $filename;

    public static function write(string $code, string $filename = null): string
    {
        if (!strlen($code)) {
            throw new LogicException('code can not be empty!');
        }

        if (!in_array('includer', stream_get_wrappers())) {
            stream_wrapper_register('includer', Stream::class);
        }

        $file = 'includer://' . $filename . '@hash=' . uniqid();
        file_put_contents($file, $code);
        return $file;
    }

    public static function load(string $code, array $args = [], string $filename = null)
    {
        self::$code = $code;
        self::$args = $args;
        self::$filename = $filename;
        return (function () {
            extract(self::$args);
            return include self::write(self::$code, self::$filename);
        })();
    }
}
