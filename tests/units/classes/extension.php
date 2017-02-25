<?php

namespace mageekguy\atoum\reports\tests\units;

use mageekguy\atoum;
use mageekguy\atoum\reports\extension as testedClass;

class extension extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->implements('mageekguy\atoum\extension')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($script = new atoum\scripts\runner(uniqid()))
            ->and($script->setArgumentsParser($parser = new \mock\mageekguy\atoum\script\arguments\parser()))
            ->and($configurator = new \mock\mageekguy\atoum\configurator($script))
            ->then
                ->object($extension = new testedClass())
            ->if($this->resetMock($parser))
            ->and($extension = new testedClass($configurator))
            ->then
                ->mock($parser)
                    ->call('addHandler')->twice()
        ;
    }

    public function testGetSetRunner()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($runner = new \mock\mageekguy\atoum\runner())
            ->then
                ->object($this->testedInstance->setRunner($runner))->isTestedInstance
                ->object($this->testedInstance->getRunner())->isIdenticalTo($runner)
        ;
    }

    public function testGetSetTest()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($test = new \mock\mageekguy\atoum\test())
            ->then
                ->object($this->testedInstance->setTest($test))->isTestedInstance
                ->object($this->testedInstance->getTest())->isIdenticalTo($test)
        ;
    }
}
