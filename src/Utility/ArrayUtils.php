<?php
namespace MagicMenu\Utility;

class ArrayUtils
{
    /**
     * Utility function to return a value from the array
     * at the specified key, and also to remove it from the
     * array.
     *
     * @param string $key The key to remove and return
     * @param array $array The array to remove the key from
     * @return mixed
     */
    public static function consume($key, array &$array)
    {
        $value = null;
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]);
        }

        return $value;
    }
}
