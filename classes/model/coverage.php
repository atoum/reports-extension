<?php

namespace mageekguy\atoum\reports\model;

use mageekguy\atoum\exceptions\runtime;
use mageekguy\atoum\reports\model;
use mageekguy\atoum\reports\template;

class coverage extends model
{
	private $classes;

	public function __construct()
	{
		$this->classes = array();
	}

	public function addClass($name, $coverage, $methods, $lines)
	{
		$this->classes[$name] = array(
			'coverage' => $coverage,
			'methods' => $methods,
			'lines' => $lines,
		);

		return $this;
	}

	public function renderTo(template $template, $destination)
	{
		$template->render(array(
			'classes' => $this->classes,
			'coverage' => $this->coverage
		), $destination);

		return $this;
	}
}
