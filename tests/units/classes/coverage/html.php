<?php

namespace atoum\atoum\reports\tests\units\coverage;

use atoum\atoum;

class html extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('atoum\atoum\reports\coverage');
    }
}
