# atoum/reports-extension [![Build Status](https://travis-ci.org/atoum/reports-extension.svg?branch=master)](https://travis-ci.org/atoum/reports-extension) [![StyleCI](https://styleci.io/repos/35064717/shield?branch=master)](https://styleci.io/repos/35064717)

## Install it

Install extension using [composer](https://getcomposer.org):

```
composer require --dev atoum/reports-extension
```

Enable the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use mageekguy\atoum\reports;

$extension = new reports\extension($script);

$extension->addToRunner($runner);
```

## Use it

### HTML coverage report

**Check out the demo report generated with atoum's test suite: [http://atoum.github.io/reports-extension/](http://atoum.github.io/reports-extension/)**

Add the following code to your configuration file:

```php
<?php

// .atoum.php

use mageekguy\atoum\reports;
use mageekguy\atoum\reports\coverage;
use mageekguy\atoum\writers\std;

$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');
$runner->addReport($coverage);
```

#### Branches and path coverage

If you want to generate branches and paths reports, you will have to install xDebug 2.3.0 or later:

```
wget https://github.com/FriendsOfPHP/pickle/releases/download/v0.4.0/pickle.phar
php pickle.phar install xdebug

php -v
```

Once done, just use the `-ebpc` command line flag or add the following line of code to your configuration file:

```php
<?php

// .atoum.php

$script->enableBranchAndPathCoverage();
```

### Sonar coverage report

To add generic code coverage for sonar.

```php
$xunit = new \mageekguy\atoum\reports\sonar\xunit();
$writer = new \mageekguy\atoum\writers\file('./sonar-xunit.xml');
$xunit->addWriter($writer);
$runner->addReport($xunit);

$clover = new \mageekguy\atoum\reports\sonar\clover();
$writer = new \mageekguy\atoum\writers\file('./sonar-clover.xml');
$clover->addWriter($writer);
$runner->addReport($clover);
```

and add report generate to `sonar.genericcoverage` properties

## License

reports-extension is released under the BSD-3-Clause License. See the bundled [LICENSE](LICENSE) file for details.

![atoum](http://atoum.org/images/logo/atoum.png)