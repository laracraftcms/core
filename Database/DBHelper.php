<?php

namespace Laracraft\Core\Database;

class DBHelper{

	const SIZE_STRING = 'string';
	const SIZE_TEXT = 'text';
	const SIZE_MEDIUMTEXT = 'mediumText';
	const SIZE_LONGTEXT = 'longText';

	const SIZE_MAX_STRING  = 255;
	const SIZE_MAX_TEXT = 65535;
	const SiZE_MAX_MEDIUMTEXT = 16777215;
	const SIZE_MAX_LONGTEXT = 4294967295;

	const TEXTUAL_COLUMN_SIZES = [
		self::SIZE_STRING => self::SIZE_MAX_STRING,
		self::SIZE_TEXT => self::SIZE_MAX_TEXT,
		self::SIZE_MEDIUMTEXT => self::SiZE_MAX_MEDIUMTEXT,
		self::SIZE_LONGTEXT => self::SIZE_MAX_LONGTEXT
	];

    public static function getTextualColumnTypeByContentLength($contentLength)
    {
		if(empty($contentLength)){
			return self::SIZE_TEXT;
		}

		foreach(self::TEXTUAL_COLUMN_SIZES as $size => $max){
			if($contentLength <= $max){
				return $size;
			}
		}

		return self::SIZE_TEXT;
    }

}