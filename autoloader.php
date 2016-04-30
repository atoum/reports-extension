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

atoum\autoloader::get()
	->addNamespaceAlias('atoum\reports', __NAMESPACE__)
	->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes')
	->addDirectory('Symfony\Component\Filesystem', $vendorDir . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR . 'filesystem')
	->addDirectory('GuzzleHttp', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'guzzle' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('GuzzleHttp\Promise', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'promises' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('GuzzleHttp\Psr7', $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'psr7' . DIRECTORY_SEPARATOR . 'src')
	->addDirectory('Psr\Http\Message', $vendorDir . DIRECTORY_SEPARATOR . 'psr' . DIRECTORY_SEPARATOR . 'http-message' . DIRECTORY_SEPARATOR . 'src')
;

require_once $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'guzzle' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';
require_once $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'promises' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';
require_once $vendorDir . DIRECTORY_SEPARATOR . 'guzzlehttp' . DIRECTORY_SEPARATOR . 'psr7' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'functions_include.php';

require_once $vendorDir . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Twig' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$twigAutoloader = new \Twig_Autoloader();
$twigAutoloader->register();

require_once __DIR__ . DIRECTORY_SEPARATOR .'tests' . DIRECTORY_SEPARATOR . 'autoloader.php';
