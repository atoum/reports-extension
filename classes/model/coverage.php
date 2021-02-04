<?php

namespace atoum\atoum\reports\model;

use atoum\atoum\reports\model;
use atoum\atoum\reports\template;

class coverage extends model
{
    private $classes;

    public function __construct()
    {
        $this->classes = [];
    }

    public function addClass($name, $coverage, $methods, $lines)
    {
        $this->classes[$name] = [
            'coverage' => $coverage,
            'methods' => $methods,
            'lines' => $lines,
        ];

        return $this;
    }

    public function renderTo(template $template, $destination)
    {
        $template->render([
            'classes' => $this->classes,
            'coverage' => $this->coverage
        ], $destination);

        return $this;
    }
}
