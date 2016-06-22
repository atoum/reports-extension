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
;

require_once $vendorDir . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Twig' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$twigAutoloader = new \Twig_Autoloader();
$twigAutoloader->register();
