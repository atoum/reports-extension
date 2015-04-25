<?php

namespace mageekguy\atoum\reports\model\coverage;

use mageekguy\atoum\reports\score\coverage;

class method
{
    private $coverage;

    public function __construct()
    {
        $this->methods = array();
    }

    public function coverageIs(coverage\method $coverage)
    {
        $this->coverage = $coverage;

        return $this;
    }
}
