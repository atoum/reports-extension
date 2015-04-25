<?php
namespace mageekguy\atoum\reports;

use mageekguy\atoum;

atoum\autoloader::get()
    ->addNamespaceAlias('atoum\reports', __NAMESPACE__)
    ->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes');
;

require_once __DIR__ . '/tests/autoloader.php';
