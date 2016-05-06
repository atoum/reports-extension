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
				$client = new \mock\GuzzleHttp\Client(),
				$this->calling($client)->request->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($client)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl)->once
			->if(
				$url = uniqid(),
				$this->testedInstance->setTelemetryUrl($url)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($client)->call('request')->withArguments('POST', $url)->once
		;
	}

	public function testSetProjectName()
	{
		$this
			->given(
				$client = new \mock\GuzzleHttp\Client(),
				$this->calling($client)->request->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($client),
				$this->function->getenv = false
			)
			->if($this->function->uniqid = 'anon/' . ($project = uniqid()))
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->if(
				$vendor = uniqid(),
				$project = uniqid(),
				$this->testedInstance->setProjectName($vendor . '/' . $project)
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->if(
				$this->function->uniqid = 'anon/' . ($project = uniqid()),
				$this->testedInstance->sendAnonymousReport()
			)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->exception(function($test) {
					$test->testedInstance->setProjectName(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Project name should match /^[a-z0-9_.-]+\/[a-z0-9_.-]+$/')
		;
	}

	public function testHandleEvent()
	{
		$this
			->given(
				$client = new \mock\GuzzleHttp\Client(),
				$this->calling($client)->request->doesNothing,
				$runner = new \mock\mageekguy\atoum\runner(),
				$telemetry = $this->newTestedInstance($client),
				$this->function->getenv = false
			)
			->if($this->function->uniqid = 'anon/' . ($project = uniqid()))
			->when(
				$this->testedInstance->handleEvent(atoum\test::runStart, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->when(
				$this->testedInstance->handleEvent(atoum\test::runStart, $runner),
				$this->testedInstance->handleEvent(atoum\test::beforeTestMethod, $runner),
				$this->testedInstance->handleEvent(atoum\runner::runStop, $runner)
			)
			->then
				->mock($client)->call('request')->withArguments('POST', testedClass::defaultUrl, ['body' => json_encode([
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
				])])->once
			->given(
				$request = new \mock\Psr\Http\Message\RequestInterface(),
				$response = new \mock\Psr\Http\Message\ResponseInterface(),
				$this->calling($response)->getStatusCode = $code = rand(400, 599),
				$exception = new \mock\GuzzleHttp\Exception\RequestException(uniqid(), $request, $response)
			)
			->if($this->calling($client)->request->throw = $exception)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($this->testedInstance)->isEqualTo('Unable to send your report to the telemetry: HTTP ' . $code . PHP_EOL)
			->if($this->calling($client)->request->doesNothing)
			->when($this->testedInstance->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($this->testedInstance)->isEqualTo('Your report has been sent to the telemetry. Thanks for sharing it with us!' . PHP_EOL)
		;
	}
}
