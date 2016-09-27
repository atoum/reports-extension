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

$extension = new reports\extension($script);

$extension->addToRunner($runner);
```

## Use it

### Telemetry report

The telemetry report allow us to collect metrics from your test suites. If you want to help us improve atoum, please send us your reports.

To enable the telemetry report, add the following code to your configuration file:

```php
<?php

// .atoum.php

use mageekguy\atoum\reports;
use mageekguy\atoum\reports\telemetry;
use mageekguy\atoum\writers\std;

$script->addDefaultReport();

$telemetry = new telemetry();
$telemetry->addWriter(new std\out());
$runner->addReport($telemetry);
```

Now, each time your run your test suite, atoum will collect data and send them to the telemtry. By default, **everything is
sent anonymously**: a random project name will be generated and we'll only collect metrics. 

If you want to let us know who you are, add the following lines to your configuration file:

```php
<?php 

$telemetry->readProjectNameFromComposerJson(__DIR__ . '/composer.json');

// Or

$telemetry->setProjectName('my/project');
```

_The project name **must** be composer compliant._

With this configuration, atoum will send us everything about your project: the vendor name and the project name. If 
you want to keep the latter secret so we only collect the vendor name, you can add the following line:

```php
<?php

$telemetry->sendAnonymousProjectName();
```

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

### LICENSE
```
Copyright (c) 2010-2016, Frédéric Hardy.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of Frédéric Hardy nor the names of its contributors
      may be used to endorse or promote products derived from this software
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY FRÉDÉRIC HARDY AND CONTRIBUTORS ``AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL FRÉDÉRIC HARDY AND CONTRIBUTORS BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```
