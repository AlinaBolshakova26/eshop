<?php

namespace Core;

class View
{
	protected $templatePath;
	protected $params;

	public function __construct($templatePath, $params = [])
	{
		$this->templatePath = $templatePath;
		$this->params = $params;
	}

	public static function make($templatePath, $params = [])
	{
		return new static($templatePath, $params);
	}

	public function render()
	{
		ob_start();
		extract($this->params);
		require_once $this->templatePath;
		return ob_get_clean();
	}

	public function __toString()
	{
		return $this->render();
	}
}