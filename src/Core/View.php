<?php

namespace Core;

use Core\Exceptions\AppException;

class View
{
	protected static string $viewsPath = ROOT . '/Views/';
	protected string $templatePath;
	protected array $params;

	public function __construct(string $templatePath, array $params = [])
	{
		$this->templatePath = static::$viewsPath . $templatePath . '.php';
		$this->params = $params;
	}

	public static function make(string $templatePath, array $params = []): self
	{
		return new static($templatePath, $params);
	}

	public function render(): string
	{
		if (!file_exists($this->templatePath)) {
			throw new AppException("View template not found: {$this->templatePath}");
		}

		ob_start();
		extract($this->params);
		require $this->templatePath;

		return ob_get_clean();
	}

	public function __toString(): string
	{
		return $this->render();
	}

}