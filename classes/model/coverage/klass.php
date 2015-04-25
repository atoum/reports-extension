<?php

namespace mageekguy\atoum\reports\model\coverage;

use mageekguy\atoum\reports\code\line;
use mageekguy\atoum\reports\score\coverage;

class klass
{
    private $coverage;
    private $methods;
    private $lines;

    public function __construct()
    {
        $this->methods = array();
        $this->lines = array();
    }

    public function coverageIs(coverage\klass $coverage)
    {
        $this->coverage = $coverage;

        return $this;
    }

    public function addMethod($name, method $method)
    {
        $this->methods[$name] = $method;
    }

    public function addLine($name, line $line)
    {
        $this->lines[$name] = $line;
    }
}
