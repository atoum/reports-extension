<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';

use
    mageekguy\atoum\reports,
    mageekguy\atoum\reports\coverage
;

$runner->addExtension(new reports\extension($script));
$script->addDefaultReport();
$runner->addReport(new coverage\code());
