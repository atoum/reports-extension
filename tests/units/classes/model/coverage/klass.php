<?php

namespace mageekguy\atoum\reports\tests\units\model\coverage;

use mageekguy\atoum;

class klass extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('mageekguy\atoum\reports\model');
    }

    public function testCoverageIs()
    {
        $this
            ->given(
                $class = uniqid(),
                $this->mockGenerator->shuntParentClassCalls(),
                $template = new \mock\mageekguy\atoum\reports\template(uniqid()),
                $totalLines = rand(1, PHP_INT_MAX),
                $coveredLines = rand(1, PHP_INT_MAX),
                $totalBranches = rand(1, PHP_INT_MAX),
                $coveredBranches = rand(1, PHP_INT_MAX),
                $totalPaths = rand(1, PHP_INT_MAX),
                $coveredPaths = rand(1, PHP_INT_MAX),
                $totalOps = rand(1, PHP_INT_MAX),
                $coveredOps = rand(1, PHP_INT_MAX)
            )
            ->if($this->newTestedInstance($class))
            ->then
                ->object($this->testedInstance->coverageIs($totalLines, $coveredLines, $totalBranches, $coveredBranches, $totalPaths, $coveredPaths, $totalOps, $coveredOps))->isTestedInstance
                ->object($this->testedInstance->renderTo($template, uniqid()))->isTestedInstance
                ->mock($template)
                    ->call('render')->withArguments(
                        [
                            'class' => $class,
                            'coverage' => [
                                'totalLines' => $totalLines,
                                'coveredLines' => $coveredLines,
                                'lines' => $coveredLines / $totalLines,
                                'totalBranches' => $totalBranches,
                                'coveredBranches' => $coveredBranches,
                                'branches' => $coveredBranches / $totalBranches,
                                'totalPaths' => $totalPaths,
                                'coveredPaths' => $coveredPaths,
                                'paths' => $coveredPaths / $totalPaths,
                                'totalOps' => $totalOps,
                                'coveredOps' => $coveredOps,
                                'ops' => $coveredOps / $totalOps,
                            ],
                            'methods' => [],
                            'lines' => []
                        ]
                    )->once
        ;
    }

    public function testAddMethod()
    {
        $this
            ->given(
                $name = uniqid(),
                $class = uniqid(),
                $this->mockGenerator->shuntParentClassCalls(),
                $template = new \mock\mageekguy\atoum\reports\template(uniqid())
            )
            ->if($this->newTestedInstance($class))
            ->then
                ->object($this->testedInstance->addMethod($name, [], [], []))->isTestedInstance
                ->object($this->testedInstance->renderTo($template, uniqid()))->isTestedInstance
                ->mock($template)
                    ->call('render')->withArguments(
                        [
                            'class' => $class,
                            'coverage' => null,
                            'methods' => [
                                $name => [
                                    'coverage' => [],
                                    'branches' => [],
                                    'paths' => []
                                ]
                            ],
                            'lines' => []
                        ]
                    )->once
        ;
    }

    public function testAddLine()
    {
        $this
            ->given(
                $number = rand(1, PHP_INT_MAX),
                $code = uniqid(),
                $class = uniqid(),
                $this->mockGenerator->shuntParentClassCalls(),
                $template = new \mock\mageekguy\atoum\reports\template(uniqid())
            )
            ->if($this->newTestedInstance($class))
            ->then
                ->object($this->testedInstance->addLine($number, $code, 0, null))->isTestedInstance
                ->object($this->testedInstance->renderTo($template, uniqid()))->isTestedInstance
                ->mock($template)
                    ->call('render')->withArguments(
                        [
                            'class' => $class,
                            'coverage' => null,
                            'methods' => [],
                            'lines' => [
                                $number => [
                                    'code' => $code,
                                    'number' => $number,
                                    'hit' => 0,
                                    'method' => null
                                ]
                            ]
                        ]
                    )->once
        ;
    }
}
