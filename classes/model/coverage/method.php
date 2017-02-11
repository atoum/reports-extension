<?php

namespace mageekguy\atoum\reports\model\coverage;

use mageekguy\atoum\reports\model;
use mageekguy\atoum\reports\template;

class method extends model
{
    private $method;
    private $branches;
    private $paths;

    public function __construct($method, array $branches = null, array $paths = null)
    {
        $this->method = $method;
        $this->branches = $branches;
        $this->paths = $paths;
    }

    public function addToModel(klass $class)
    {
        $class->addMethod($this->method, $this->coverage, $this->branches, $this->paths);

        return $this;
    }

    public function renderTo(template $template, $destination)
    {
        $template->render(
            [
                'method' => $this->method,
                'coverage' => $this->coverage,
                'branches' => $this->branches,
                'paths' => $this->paths
            ],
            $destination
        );

        return $this;
    }
}
