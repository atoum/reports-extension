<?php

namespace mageekguy\atoum\reports\model;

class coverage
{
    private $coverage;
    private $classes;

    public function __construct()
    {
        $this->classes = array();
    }

    public function coverageIs($lines = null, $branches = null, $paths = null)
    {
        $this->coverage = array(
            'lines' => $lines,
            'branches' => $branches,
            'paths' => $paths,
        );

        return $this;
    }

    public function addClass($name, coverage\klass $model)
    {
        $this->classes[$name] = $model;

        return $this;
    }
}
