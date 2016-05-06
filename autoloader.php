<?php
namespace mageekguy\atoum\reports;

use mageekguy\atoum;

$directories = [
	__DIR__ . DIRECTORY_SEPARATOR . 'vendor',
	__DIR__ . DIRECTORY_SEPARATOR . '../..'
];
$vendorDir = null;


foreach ($directories as $directory)
{
	if (is_dir($directory))
	{
		$vendorDir = $directory;

		break;
	}
}

if ($vendorDir === null)
{
	throw new \Exception('Unable to find the vendor directory');
}

$filesystemDir = $vendorDir . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR . 'filesystem';
$filesystemSuffix = 'Symfony' . DIRECTORY_SEPARATOR . 'Component' . DIRECTORY_SEPARATOR . 'Filesystem';
if (is_dir($filesystemDir . DIRECTORY_SEPARATOR . $filesystemSuffix) === true)
{
	$filesystemDir = $filesystemDir . DIRECTORY_SEPARATOR . $filesystemSuffix;
}

atoum\autoloader::get()
	->addNamespaceAlias('atoum\reports', __NAMESPACE__)
	->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes')
	->addDirectory('Symfony\Component\Filesystem', $filesystemDir)
	->addDirectory('GuzzleHttp', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'guzzle' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('GuzzleHttp\Promise', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'promises' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('GuzzleHttp\Psr7', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'psr7' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('Psr\Http\Message', $vendorDir . DIRECTORY_SEPARATOR . 'psr' . DIRECTORY_SEPARATOR . 'http-message' . DIRECTORY_SEPARATOR . 'src')
;

require_once $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'guzzle' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';

$promisesAutoloader = $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'promises' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';
if (is_file($promisesAutoloader) === false)
{
	$promisesAutoloader = $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'promises' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions.php';
}

$psr7Autoloader = $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'psr7' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';
if (is_file($psr7Autoloader) === false)
{
	$psr7Autoloader = $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'psr7' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions.php';
}

require_once $promisesAutoloader;
require_once $psr7Autoloader;

require_once $vendorDir . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Twig' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$twigAutoloader = new \Twig_Autoloader();
$twigAutoloader->register();

require_once __DIR__ . DIRECTORY_SEPARATOR .'tests' . DIRECTORY_SEPARATOR . 'autoloader.php';
