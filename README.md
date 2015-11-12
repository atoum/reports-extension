# atoum reports extension [![Build Status](https://travis-ci.org/atoum/reports-extension.svg?branch=master)](https://travis-ci.org/atoum/reports-extension)

![atoum](http://downloads.atoum.org/images/logo.png)

## Install it

Install extension using [composer](https://getcomposer.org):

```json
{
    "require-dev": {
        "atoum/reports-extension": "~1.0"
    }
}

```

Enable the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use mageekguy\atoum\reports;

$runner->addExtension(new reports\extension($script));
```

## Use it

### HTML coverage report

**Check out the demo report generated with atoum's test suite: [http://atoum.github.io/reports-extension/](http://atoum.github.io/reports-extension/)**

Add the following code to you configuration file :

```php
<?php

// .atoum.php

use mageekguy\atoum\reports;
use mageekguy\atoum\reports\coverage;

$script->addDefaultReport();

$coverage = new coverage\html();
$coverage->addWriter(new \mageekguy\atoum\writers\std\out());
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
