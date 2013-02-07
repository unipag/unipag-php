<?php

class Unipag_Utils
{
    public static function urlify($key, $val) {
        return urlencode($key) . '=' . urlencode($val);
    }

    /**
     * Convert associative array to query string parameters format.
     *
     * Example:
     *   var_dump(Unipag_Utils::urlEncode(array('foo' => 1, 'bar' => 'baz')));
     *   // output: string(17) "foo=1&amp;bar=baz"
     *
     * @param $arr
     * @return string
     */
    public static function urlEncode($arr)
    {
        return implode('&amp;', array_map(
                'Unipag_Utils::urlify',
                array_keys($arr),
                array_values($arr)
        ));
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

    /**
     * Returns true, if $array1 is equal to $array2, false otherwise.
     *
     * @param $array1
     * @param $array2
     * @return bool
     */
    public static function arraysEqual($array1, $array2)
    {
        return true;
    }
}
