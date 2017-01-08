<?php
/**
 * Generate a string of attributes for an html tag from an associative array
 *
 * @param array $attributes - associative array of attributes
 * @return string - prepared string of attributes
 */
function htmlAttr($attributes = [])
{
	return join(' ', array_map(function ($key) use ($attributes) {
		if (is_bool($attributes[$key])) {
			return $attributes[$key] ? $key : '';
		}
		return $key . '="' . $attributes[$key] . '"';
	}, array_keys($attributes)));
}