<?php

class Unipag_Utils
{
    public static function urlify($key, $val) {
        if (is_array($val)) {
            $params = array();
            foreach ($val as $k => $v) {
                $prefix = $key ? $key.'__' : '';
                $params[] = self::urlify($prefix.$k, $v);
            }
            return implode('&', $params);
        } else {
            return urlencode($key) . '=' . urlencode($val);
        }
    }

    /**
     * Convert associative array to query string parameters format.
     *
     * Supports nested arrays. Nested arrays will be encoded using
     * prefixes made of parent key and '__'.
     *
     * Examples:
     *
     *   var_dump(Unipag_Utils::urlEncode(array('foo' => 1, 'bar' => 'baz')));
     *   // output: string(17) "foo=1&bar=baz"
     *
     *   var_dump(Unipag_Utils::urlEncode(array(
     *       'foo' => array('bar' => 'baz', 'key' => 'val')
     *   )));
     *   // output: string(25) "foo__bar=baz&foo__key=val"
     *
     * @param $arr
     * @return string
     */
    public static function urlEncode($arr)
    {
        return self::urlify('', $arr);
    }

    /**
     * Returns true, if $haystack string starts with $needle, false otherwise.
     *
     * Case-sensitive by default.
     * Set $case to false for case-insensitive comparison.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    public static function startsWith($haystack, $needle, $case=true)
    {
        if($case)
            return strpos($haystack, $needle, 0) === 0;

        return stripos($haystack, $needle, 0) === 0;
    }

    /**
     * Returns true, if $haystack string ends with $needle, false otherwise.
     *
     * Case-sensitive by default.
     * Set $case to false for case-insensitive comparison.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    public static function endsWith($haystack, $needle, $case=true)
    {
        $expectedPosition = strlen($haystack) - strlen($needle);

        if($case)
            return strrpos($haystack, $needle, 0) === $expectedPosition;

        return strripos($haystack, $needle, 0) === $expectedPosition;
    }
}
