<?php

use
	mageekguy\atoum\reports\coverage,
	mageekguy\atoum\writers\std
;

$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');
$runner->addReport($coverage);

$script->noCodeCoverageForClasses('mageekguy\atoum\reports\asynchronous');
$script->noCodeCoverageForClasses('mageekguy\atoum\report');
