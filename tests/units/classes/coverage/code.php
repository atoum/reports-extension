<?php

namespace mageekguy\atoum\reports\tests\units\coverage;

use mageekguy\atoum;

class html extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('mageekguy\atoum\reports\coverage');
    }
}
