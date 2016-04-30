<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';

use
	mageekguy\atoum\reports,
	mageekguy\atoum\reports\coverage,
	mageekguy\atoum\reports\telemetry,
	mageekguy\atoum\writers\std
;

$runner->addExtension(new reports\extension($script));
$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');
$runner->addReport($coverage);

$telemetry = new telemetry();
$telemetry->readProjectNameFromComposerJson(__DIR__ . '/composer.json');
$telemetry->addWriter(new std\out());
$runner->addReport($telemetry);

$script->enableBranchAndPathCoverage();
$script->noCodeCoverageForClasses('mageekguy\atoum\reports\asynchronous');
$script->noCodeCoverageForClasses('mageekguy\atoum\report');
