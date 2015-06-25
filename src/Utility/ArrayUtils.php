<?php
namespace MagicMenu\Utility;

class ArrayUtils {
	
	public static function pluck($key, &$array) {
		$value = null;
		if (array_key_exists($key, $array)) {
			$value = $array[$key];
			unset($array[$key]);
		}
		return $value;
	}

}