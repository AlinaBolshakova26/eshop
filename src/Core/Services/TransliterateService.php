<?php

namespace Core\Services;

use LogicException;


class TransliterateService
{

	public static function transliterate(string $string): string
	{

		$toRu = '';

		$tlTable = 
		[
			'А' => 'F', 'Б' => ',', 'В' => 'D', 'Г' => 'U', 'Д' => 'L', 'Е' => 'T', 'Ё' => '`', 'Ж' => ';', 'З' => 'P',
			'И' => 'B', 'Й' => 'Q', 'К' => 'R', 'Л' => 'K', 'М' => 'V', 'Н' => 'Y', 'О' => 'J', 'П' => 'G', 'Р' => 'H',
			'С' => 'C', 'Т' => 'N', 'У' => 'E', 'Ф' => 'A', 'Х' => '[', 'Ц' => 'W', 'Ч' => 'X', 'Ш' => 'I', 'Щ' => 'O',
			'Ъ' => ']', 'Ы' => 'S', 'Ь' => 'M', 'Э' => '\'', 'Ю' => '.', 'Я' => 'Z', 'а' => 'f', 'в' => 'd', 'г' => 'u', 'д' => 'l',
			'е' => 't', 'з' => 'p', 'и' => 'b', 'й' => 'q', 'к' => 'r', 'л' => 'k', 'м' => 'v',
			'н' => 'y', 'о' => 'j', 'п' => 'g', 'р' => 'h', 'с' => 'c', 'т' => 'n', 'у' => 'e', 'ф' => 'a',
			'ц' => 'w', 'ч' => 'x', 'ш' => 'i', 'щ' => 'o', 'ы' => 's', 'ь' => 'm', 'я' => 'z',
		];

		$tlSpec = 
		[
			'б' => ',', 'ё' => '`', 'ж' => ';', 'х' => '[', 'ъ' => ']', 'э' => '\'', 'ю' => '.',
		];

		if (count($tlTable) !== count(array_unique($tlTable)) || count($tlSpec) !== count(array_unique($tlSpec))) 
		{
			throw new LogicException("Таблица транслитерации содержит дублирующиеся значения!");
		}

		$tlTableReversed = array_flip($tlTable);
		$tlSpecReversed = array_flip($tlSpec);

		$toRu = strtr($string, array_merge($tlTableReversed, $tlSpecReversed));

		return $toRu;

	}

}