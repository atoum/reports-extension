<?php

namespace mageekguy\atoum\reports\tests\units\model\coverage;

use mageekguy\atoum;

class method extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends('mageekguy\atoum\reports\model');
    }

    public function testCoverageIs()
    {
        $this
            ->given(
                $method = uniqid(),
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
            ->if($this->newTestedInstance($method))
            ->then
                ->object($this->testedInstance->coverageIs($totalLines, $coveredLines, $totalBranches, $coveredBranches, $totalPaths, $coveredPaths, $totalOps, $coveredOps))->isTestedInstance
                ->object($this->testedInstance->renderTo($template, uniqid()))->isTestedInstance
                ->mock($template)
                    ->call('render')->withArguments(
                        [
                            'method' => $method,
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
                            'branches' => null,
                            'paths' => null
                        ]
                    )->once
        ;
    }
}
