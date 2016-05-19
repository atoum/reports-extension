<?php

namespace mageekguy\atoum\reports;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use mageekguy\atoum;
use mageekguy\atoum\reports\asynchronous;

class telemetry extends asynchronous
{
	const defaultUrl = 'https://telemetry.atoum.org';

	protected $http;
	protected $score;
	protected $testClassNumber = 0;
	protected $testMethodNumber = 0;
	protected $isAnonymous = true;
	protected $isAnonymousProject = false;
	protected $projectFullName;
	protected $projectVendorName;
	protected $projectName;
	protected $telemetryUrl;
	protected $reportIsDisabled;

	public function __construct(atoum\writers\http $http = null)
	{
		parent::__construct();

		$this->setTelemetryUrl();
		$this->http = $http ?: new atoum\writers\http();
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

		return $this;
	}

	public function sendAnonymousProjectName()
	{
		$this->isAnonymousProject = true;

		return $this;
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

			if ($this->isAnonymousProject === true)
			{
				$this->setProjectName($this->projectVendorName . '/anon-' . md5($this->projectFullName));
			}

			$report = [
				'php' => $this->score->getPhpVersion(),
				'atoum' => $this->score->getAtoumVersion(),
				'os' => php_uname('s') . ' ' . php_uname('r'),
				'arch' => php_uname('m'),
				'environment' => self::getEnvironment(),
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
					'memory' => $this->score->getTotalMemoryUsage()
				]
			];

			$coverage = $this->score->getCoverage();

			if ($coverage->getValue() !== null || $coverage->getBranchesCoverageValue() !== null || $coverage->getPathsCoverageValue() !== null)
			{
				$report['metrics']['coverage'] = [];

				if ($coverage->getValue() !== null)
				{
					$report['metrics']['coverage']['lines'] = $coverage->getValue();
				}

				if ($coverage->getBranchesCoverageValue() !== null)
				{
					$report['metrics']['coverage']['branches'] = $coverage->getBranchesCoverageValue();
				}

				if ($coverage->getPathsCoverageValue() !== null)
				{
					$report['metrics']['coverage']['paths'] = $coverage->getPathsCoverageValue();
				}
			}

			try
			{
				$this->http
					->setUrl($this->telemetryUrl)
					->setMethod('POST')
					->write(json_encode($report))
				;

				$this->string = 'Your report has been sent to the telemetry. Thanks for sharing it with us!';
			}
			catch(atoum\writers\http\exception $exception)
			{
				$this->string = 'Unable to send your report to the telemetry.';
			}

			$this->string .= PHP_EOL;
		}

		return $this;
	}

	private static function getEnvironment()
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

			case (bool) getenv('CONTINUOUSPHP'):
				return 'continuousphp';

			case (bool) getenv('CI'):
				return 'ci';

			case (bool) getenv('TERM'):
				return 'cli';

			case (bool) getenv('PHPSTORM'):
				return 'phpstorm';

			case (bool) getenv('ATOM'):
				return 'atom';

			case (bool) getenv('VIM'):
				return 'vim';

			default:
				return 'unknown';
		}
	}
}
