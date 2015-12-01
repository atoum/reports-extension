<?php
namespace mageekguy\atoum\reports;

use mageekguy\atoum;

atoum\autoloader::get()
	->addNamespaceAlias('atoum\reports', __NAMESPACE__)
	->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes')
	->addDirectory('Symfony\Component\Filesystem', __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR . 'filesystem')
;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'twig' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Twig' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$twigAutoloader = new \Twig_Autoloader();
$twigAutoloader->register();

require_once __DIR__ . DIRECTORY_SEPARATOR .'tests' . DIRECTORY_SEPARATOR . 'autoloader.php';
