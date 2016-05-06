<?php

namespace mageekguy\atoum\reports;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use mageekguy\atoum;
use mageekguy\atoum\reports\asynchronous;

class telemetry extends asynchronous
{
	const defaultUrl = 'https://telemetry.atoum.org';

	protected $client;
	protected $score;
	protected $testClassNumber = 0;
	protected $testMethodNumber = 0;
	protected $isAnonymous = true;
	protected $projectFullName;
	protected $projectVendorName;
	protected $projectName;
	protected $telemetryUrl;

	public function __construct(Client $client = null)
	{
		parent::__construct();

		$this->setTelemetryUrl();
		$this->client = $client ?: new Client();
	}

	public function setTelemetryUrl($url = null)
	{
		$this->telemetryUrl = $url ?: static::defaultUrl;

		return $this;
	}

	public function setProjectName($name)
	{
		if (!preg_match('/^[a-z0-9_.-]+\/[a-z0-9_.-]+$/', $name))
		{
			throw new atoum\exceptions\logic\invalidArgument('Project name should match /^[a-z0-9_.-]+\/[a-z0-9_.-]+$/');
		}

		$parts = explode('/', $name);

		$this->projectFullName = $name;
		$this->projectVendorName = $parts[0];
		$this->projectName = $parts[1];
		$this->isAnonymous = false;

		return $this;
	}

	public function readProjectNameFromComposerJson($path)
	{
		if (is_file($path) === false)
		{
			throw new atoum\exceptions\runtime($this->locale->_('File %s does not exists', realpath($path)));
		}

		$json = @file_get_contents($path);

		if ($json === false)
		{
			throw new atoum\exceptions\runtime($this->locale->_('Could not read file %s', realpath($path)));
		}

		$json = @json_decode($json, true);

		if ($json === false)
		{
			throw new atoum\exceptions\runtime($this->locale->_('Could not decode JSON from file %s', realpath($path)));
		}

		if (isset($json['name']) === false)
		{
			throw new atoum\exceptions\runtime($this->locale->_('Could not extract project name from file %s', realpath($path)));
		}

		$this->setProjectName($json['name']);

		return $this;
	}

	public function sendAnonymousReport()
	{
		$this->isAnonymous = true;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = $observable->getScore();

		if ($event === atoum\test::runStart)
		{
			$this->testClassNumber++;
		}

		if ($event === atoum\test::beforeTestMethod)
		{
			$this->testMethodNumber++;
		}

		return parent::handleEvent($event, $observable);
	}

	public function build($event)
	{
		if ($event === atoum\runner::runStop && $this->score !== null)
		{
			if (($this->isAnonymous === true && $this->projectFullName !== null) || $this->projectFullName === null)
			{
				$this->setProjectName(uniqid('anon/', true));
			}

			$report = [
				'php' => $this->score->getPhpVersion(),
				'atoum' => $this->score->getAtoumVersion(),
				'os' => php_uname('s') . ' ' . php_uname('r'),
				'arch' => php_uname('m'),
				'environment' => self::getCiEnvironment(),
				'vendor' => $this->projectVendorName,
				'project' => $this->projectName,
				'metrics' => [
					'classes' => $this->testClassNumber,
					'methods' => [
						'total' => $this->testMethodNumber,
						'void' => $this->score->getVoidMethodNumber(),
						'uncomplete' => $this->score->getUncompletedMethodNumber(),
						'skipped' => count($this->score->getSkippedMethods()),
						'failed' => count($this->score->getMethodsWithFail()),
						'errored' => count($this->score->getMethodsWithError()),
						'exception' => count($this->score->getMethodsWithException()),
					],
					'assertions' => [
						'total' => $this->score->getAssertionNumber(),
						'passed' => $this->score->getPassNumber(),
						'failed' => $this->score->getFailNumber()
					],
					'exceptions' => $this->score->getExceptionNumber(),
					'errors' => $this->score->getErrorNumber(),
					'duration' => $this->score->getTotalDuration(),
					'memory' => $this->score->getTotalMemoryUsage(),
				]
			];

			try
			{
				$this->client->request('POST', $this->telemetryUrl, ['body' => json_encode($report)]);

				$this->string = 'Your report has been sent to the telemetry. Thanks for sharing it with us!';
			}
			catch(RequestException $exception)
			{
				$this->string = 'Unable to send your report to the telemetry: HTTP ' . $exception->getCode();
			}

			$this->string .= PHP_EOL;
		}

		return $this;
	}

	private static function getCiEnvironment()
	{
		switch (true)
		{
			case (bool) getenv('TRAVIS'):
				return 'travis';

			case (bool) getenv('APPVEYOR'):
				return 'appveyor';

			case (bool) getenv('CIRCLECI'):
				return 'circleci';

			case (bool) getenv('SEMAPHORE'):
				return 'semaphore';

			case (bool) getenv('JENKINS_URL'):
				return 'jenkins';

			case (bool) getenv('GITLAB_CI'):
				return 'gitlabci';

			case (bool) getenv('CI'):
				return 'ci';

			default:
				return 'unknown';
		}
	}
}
