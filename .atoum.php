<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';

use
	mageekguy\atoum\reports,
	mageekguy\atoum\reports\coverage
;

$runner->addExtension(new reports\extension($script));
$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new \mageekguy\atoum\writers\std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');
$runner->addReport($coverage);

$script->enableBranchAndPathCoverage();
$script->noCodeCoverageForClasses('mageekguy\atoum\reports\asynchronous');
$script->noCodeCoverageForClasses('mageekguy\atoum\report');
