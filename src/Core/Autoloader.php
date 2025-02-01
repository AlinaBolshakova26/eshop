<?php
namespace Core;

class Autoloader
{
	private static $paths = [];

	public static function addPath($path)
	{
		self::$paths[] = $path;
	}

	public static function register()
	{
		spl_autoload_register(function ($className) {
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $className);

			foreach (self::$paths as $path) {
				$fullPath = $path . $file . '.php';
				if (file_exists($fullPath)) {
					require $fullPath;
					return true;
				}
			}

			return false;
		});
	}
}
