<?php

namespace mageekguy\atoum\reports\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\reports\telemetry as testedClass
;

class telemetry extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('mageekguy\atoum\reports\asynchronous')
		;
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultUrl)->isEqualTo('https://telemetry.atoum.org')
		;
	}

	public function testSetTelemetryUrl()
	{
		$this
			->given(
				$http = new \mock\mageekguy\atoum\writers\http(),
				$this->calling($http)->write->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($http)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)
					->call('setUrl')->withArguments(testedClass::defaultUrl)->once
					->call('setMethod')->withArguments('POST')->once
					->call('write')->once
			->if(
				$this->resetMock($http),
				$url = uniqid(),
				$this->testedInstance->setTelemetryUrl($url)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)
					->call('setUrl')->withArguments($url)->once
					->call('setMethod')->withArguments('POST')->once
					->call('write')->once
		;
	}

	public function testSetProjectName()
	{
		$this
			->given(
				$http = new \mock\mageekguy\atoum\writers\http(),
				$this->calling($http)->write->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($http),
				$this->function->getenv = false
			)
			->if($this->function->uniqid = 'anon/' . ($project = uniqid()))
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 0,
						'methods' => [
							'total' => 0,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->if(
				$vendor = uniqid(),
				$project = uniqid(),
				$this->testedInstance->setProjectName($vendor . '/' . $project)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => $vendor,
					'project' => $project,
					'metrics' => [
						'classes' => 0,
						'methods' => [
							'total' => 0,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->if(
				$this->function->uniqid = 'anon/' . ($project = uniqid()),
				$this->testedInstance->sendAnonymousReport()
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 0,
						'methods' => [
							'total' => 0,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->exception(function($test) {
					$test->testedInstance->setProjectName(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Project name should match /^[a-z0-9_.-]+\/[a-z0-9_.-]+$/')
		;
	}

	public function testSendAnonymousProjectName()
	{
		$this
			->given(
				$http = new \mock\mageekguy\atoum\writers\http(),
				$this->calling($http)->write->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($http),
				$this->function->getenv = false
			)
			->if(
				$this->testedInstance->setProjectName('atoum/reports-extension'),
				$this->testedInstance->sendAnonymousProjectName()
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'atoum',
					'project' => 'anon-' . md5('atoum/reports-extension'),
					'metrics' => [
						'classes' => 0,
						'methods' => [
							'total' => 0,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
		;
	}

	public function testHandleEvent()
	{
		$this
			->given(
				$http = new \mock\mageekguy\atoum\writers\http(),
				$this->calling($http)->write->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($http),
				$this->function->getenv = false
			)
			->if($this->function->uniqid = 'anon/' . ($project = uniqid()))
			->when(
				$this->testedInstance->handleEvent(atoum\test::runStart, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 1,
						'methods' => [
							'total' => 0,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 1,
						'methods' => [
							'total' => 1,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 1,
						'methods' => [
							'total' => 2,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::runStart, $runner),
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 2,
						'methods' => [
							'total' => 3,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0
					]
				]))->once
			->given(
				$score = new atoum\runner\score(),
				$coverage = new \mock\mageekguy\atoum\score\coverage(),
				$this->calling($coverage)->getValue = $coverageValue = rand(0, 100),
				$score->setCoverage($coverage)
			)
			->if($runner->setScore($score))
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 2,
						'methods' => [
							'total' => 3,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0,
						'coverage' => [
							'lines' => $coverageValue
						]
					]
				]))->once
			->given(
				$this->calling($coverage)->getBranchesCoverageValue = $branchesCoverageValue = rand(0, 100),
				$this->calling($coverage)->getPathsCoverageValue = $pathsCoverageValue = rand(0, 100)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($http)->call('write')->withArguments(json_encode([
					'php' => null,
					'atoum' => null,
					'os' => php_uname('s') . ' ' . php_uname('r'),
					'arch' => php_uname('m'),
					'environment' => 'unknown',
					'vendor' => 'anon',
					'project' => $project,
					'metrics' => [
						'classes' => 2,
						'methods' => [
							'total' => 3,
							'void' => 0,
							'uncomplete' => 0,
							'skipped' => 0,
							'failed' => 0,
							'errored' => 0,
							'exception' => 0,
						],
						'assertions' => [
							'total' => 0,
							'passed' => 0,
							'failed' => 0
						],
						'exceptions' => 0,
						'errors' => 0,
						'duration' => 0,
						'memory' => 0,
						'coverage' => [
							'lines' => $coverageValue,
							'branches' => $branchesCoverageValue,
							'paths' => $pathsCoverageValue
						]
					]
				]))->once
			->given($exception = new \mageekguy\atoum\writers\http\exception())
			->if($this->calling($http)->write->throw = $exception)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($this->testedInstance)->isEqualTo('Unable to send your report to the telemetry.' . PHP_EOL)
			->if($this->calling($http)->write->doesNothing)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($this->testedInstance)->isEqualTo('Your report has been sent to the telemetry. Thanks for sharing it with us!' . PHP_EOL)
		;
	}
}
