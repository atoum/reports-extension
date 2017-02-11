<?php

namespace mageekguy\atoum\reports\tests\units\sonar;

use mageekguy\atoum;
use mageekguy\atoum\runner;

class xunit extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('mageekguy\atoum\reports\asynchronous');
    }

    public function testBuild()
    {
        $this
            ->if($adapter = new atoum\test\adapter())
            ->and($adapter->extension_loaded = true)
            ->and($adapter->get_class = $class = 'class')
            ->and($runner = new atoum\runner())
            ->and($score = new runner\score())
            ->and($report = $this->newTestedInstance($adapter))
            ->and($runner->setScore($score))
            ->and($testScore = new atoum\test\score())
            ->and($testScore->addPass())
            ->and($test = new \mock\mageekguy\atoum\test())
            ->and($test->getMockController()->getCurrentMethod[1] = $method = 'method')
            ->and($test->getMockController()->getCurrentMethod[2] = $otherMethod = 'otherMethod')
            ->and($test->getMockController()->getCurrentMethod[3] = $thirdMethod = 'thirdMethod')
            ->and($test->setScore($testScore))
            ->and($path = implode(
                DIRECTORY_SEPARATOR,
                [
                    __DIR__,
                    'resources'
                ]
            ))
            ->and($testScore->addDuration('foo', $class, $method, $duration = 1))
            ->and($testScore->addUncompletedMethod(uniqid(), $class, $otherMethod, $exitCode = 1, $output = 'output'))
            ->and($testScore->addSkippedMethod(uniqid(), $class, $thirdMethod, $line = rand(1, PHP_INT_MAX), $message = 'message'))
            ->and($report->handleEvent(atoum\test::afterTestMethod, $test))
            ->and($testScore->addPass())
            ->and($testScore->addPass())
            ->and($report->handleEvent(atoum\test::afterTestMethod, $test))
            ->and($report->handleEvent(atoum\test::afterTestMethod, $test))
            ->and($score->merge($testScore))
            ->and($report->handleEvent(atoum\runner::runStop, $runner))
            ->then
                ->castToString($report)->isEqualToContentsOfFile(implode(DIRECTORY_SEPARATOR, [$path, '1.xml']))
                ->object($dom = new \DomDocument())
                ->boolean($dom->loadXML((string) $report))
                ->isTrue()
                ->boolean($dom->schemaValidate(implode(DIRECTORY_SEPARATOR, [$path, 'xunit.xsd'])))
                ->isTrue()
        ;
    }
}
