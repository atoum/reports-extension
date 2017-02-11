<?php

namespace mageekguy\atoum\reports\model\coverage;

use mageekguy\atoum\reports\model;
use mageekguy\atoum\reports\model\coverage;
use mageekguy\atoum\reports\template;

class klass extends model
{
    private $class;
    private $methods;
    private $lines;

    public function __construct($class)
    {
        $this->class = $class;
        $this->methods = [];
        $this->lines = [];
    }

    public function addMethod($name, array $coverage, array $branches, array $paths)
    {
        $this->methods[$name] = [
            'coverage' => $coverage,
            'branches' => $branches,
            'paths' => $paths,
        ];

        return $this;
    }

    public function addLine($number, $code, $hit = null, $method = null)
    {
        $this->lines[$number] = [
            'code' => $code,
            'hit' => $hit ?: 0,
            'number' => $number,
            'method' => $method
        ];

        return $this;
    }

    public function addToModel(coverage $model)
    {
        $model->addClass($this->class, $this->coverage, $this->methods, $this->lines);

        return $this;
    }

    public function renderTo(template $template, $destination)
    {
        $template->render(
            [
                'class' => $this->class,
                'coverage' => $this->coverage,
                'methods' => $this->methods,
                'lines' => $this->lines
            ],
            $destination
        );

        return $this;
    }
}
