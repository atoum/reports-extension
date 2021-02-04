<?php

use
	atoum\atoum\reports\coverage,
	atoum\atoum\writers\std
;

$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');
$runner->addReport($coverage);

$script->noCodeCoverageForClasses('atoum\atoum\reports\asynchronous');
$script->noCodeCoverageForClasses('atoum\atoum\report');
