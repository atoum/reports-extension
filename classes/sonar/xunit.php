<?php

namespace mageekguy\atoum\reports\sonar;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;

class xunit extends atoum\reports\asynchronous
{
    const defaultTitle = 'atoum testsuite';

    protected $score = null;
    protected $assertions = [];

    public function __construct(atoum\adapter $adapter = null)
    {
        parent::__construct();

        $this->setAdapter($adapter);

        if ($this->adapter->extension_loaded('libxml') === false) {
            throw new exceptions\runtime('libxml PHP extension is mandatory for xunit report');
        }
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->score = null;

        if ($event === atoum\test::afterTestMethod) {
            $classname = $this->adapter->get_class($observable);
            $method = $observable->getCurrentMethod();

            if (isset($this->assertions[$classname]) === false) {
                $this->assertions[$classname] = [];
            }

            $this->assertions[$classname][$method] = $observable->getScore()->getAssertionNumber() - array_sum($this->assertions[$classname]);
        }

        if ($event === atoum\runner::runStop) {
            $this->score = $observable->getScore();
        }

        return parent::handleEvent($event, $observable);
    }

    protected function getTestedClasses()
    {
        $durations = $this->score->getDurations();
        $errors = $this->score->getErrors();
        $excepts = $this->score->getExceptions();
        $fails = $this->score->getFailAssertions();
        $uncomplete = $this->score->getUncompletedMethods();
        $skipped = $this->score->getSkippedMethods();
        $assertions = $this->assertions;

        $filterClass = function ($element) use (& $clname) {
            return ($element['class'] == $clname);
        };
        $extractClasses = function ($list) use (& $clname, & $classes, & $assertions, $durations, $errors, $excepts, $fails, $uncomplete, $skipped, $filterClass) {
            foreach ($list as $entry) {
                $clname = ltrim($entry['class'], '\\');

                if (isset($classes[$clname]) === false) {
                    $classes[$clname] = [
                        'errors' => array_filter($errors, $filterClass),
                        'excepts' => array_filter($excepts, $filterClass),
                        'fails' => array_filter($fails, $filterClass),
                        'durations' => array_filter($durations, $filterClass),
                        'uncomplete' => array_filter($uncomplete, $filterClass),
                        'skipped' => array_filter($skipped, $filterClass),
                        'assertions' => isset($assertions[$clname]) ? $assertions[$clname] : []
                    ];
                }
            }
        };

        $classes = [];
        $extractClasses($durations);
        $extractClasses($errors);
        $extractClasses($excepts);
        $extractClasses($fails);
        $extractClasses($uncomplete);
        $extractClasses($skipped);

        return $classes;
    }

    public function build($event)
    {
        $this->string = '';

        if ($event === atoum\runner::runStop) {
            $this->title = $this->title ?: self::defaultTitle;

            $document = new \DOMDocument('1.0', 'UTF-8');
            $document->formatOutput = true;
            $document->appendChild($root = $document->createElement('unitTest'));
            $root->setAttribute('version', "1");
            $classes = $this->getTestedClasses();

            foreach ($classes as $name => $class) {
                $clname = $package = $name;
                $antiSlashOffset = strrpos($clname, '\\');
                if ($antiSlashOffset !== false) {
                    $clname = substr($clname, $antiSlashOffset + 1);
                    $package = substr($name, 0, $antiSlashOffset);
                }

                $root->appendChild($testSuite = $document->createElement('file'));

                $time = 0;
                foreach ($class['durations'] as $duration) {
                    $time += $duration['value'];

                    self::getTestCase($document, $testSuite, $name, $duration['method'], $duration['value'], $duration['path'], isset($class['assertions'][$duration['method']]) ? $class['assertions'][$duration['method']] : 0);
                    $path = $duration['path'];
                }

                $testSuite->setAttribute('path', $path);

                foreach ($class['errors'] as $error) {
                    $testCase = self::getTestCase($document, $testSuite, $name, $error['method'], $duration['value'], $error['file'], isset($class['assertions'][$error['method']]) ? $class['assertions'][$error['method']] : 0);
                    $testCase->appendChild($xError = $document->createElement('error'));

                    $xError->setAttribute('message', $error['type']);
                    $xError->appendChild($document->createCDATASection($error['message']));
                }

                foreach ($class['uncomplete'] as $uncomplete) {
                    $testCase = self::getTestCase($document, $testSuite, $name, $uncomplete['method'], $duration['value'], null, isset($class['assertions'][$uncomplete['method']]) ? $class['assertions'][$uncomplete['method']] : 0);
                    $testCase->appendChild($xFail = $document->createElement('error'));

                    $xFail->setAttribute('message', 'Uncomplete ' . $name);
                    $xFail->appendChild($document->createCDATASection($uncomplete['output']));
                }

                foreach ($class['fails'] as $fail) {
                    $testCase = self::getTestCase($document, $testSuite, $name, $fail['method'], $duration['value'], $fail['file'], isset($class['assertions'][$fail['method']]) ? $class['assertions'][$fail['method']] : 0);
                    $testCase->appendChild($xFail = $document->createElement('failure'));

                    $xFail->setAttribute('message', $fail['asserter']);

                    $xFail->appendChild($document->createCDATASection($fail['fail']));
                }

                foreach ($class['excepts'] as $exc) {
                    $testCase = self::getTestCase($document, $testSuite, $name, $exc['method'], $duration['value'], $exc['file'], isset($class['assertions'][$exc['method']]) ? $class['assertions'][$exc['method']] : 0);
                    $testCase->appendChild($xError = $document->createElement('error'));

                    $xError->setAttribute('message', $exc['value']);
                    $xError->appendChild($document->createCDATASection($exc['value']));
                }

                foreach ($class['skipped'] as $skipped) {
                    $testCase = self::getTestCase($document, $testSuite, $name, $skipped['method'], $duration['value'], null, isset($class['assertions'][$skipped['method']]) ? $class['assertions'][$skipped['method']] : 0);
                    $testCase->appendChild($xFail = $document->createElement('skipped'));

                    $xFail->setAttribute('message', $name);
                    $xFail->appendChild($document->createCDATASection($skipped['message']));
                }
            }

            $this->string = $document->saveXML();
        }

        return $this;
    }

    private static function getTestCase(\DOMDocument $document, \DOMElement $testSuite, $class, $method, $time, $path, $assertions)
    {
        if (($testCase = self::findTestCase($document, $class, $method)) === null) {
            $testCase = $document->createElement('testCase');
            $testCase->setAttribute('name', $method);

            set_error_handler(function () {
            }, E_WARNING);

            $testCase->setIdAttribute('name', true);

            restore_error_handler();

            $time = (int) ($time * 1000);

            $testCase->setAttribute('duration', ($time === 0) ? 1 : $time);

            $testSuite->appendChild($testCase);
        }

        return $testCase;
    }

    private static function findTestCase(\DOMDocument $document, $class, $method)
    {
        $xpath = new \DOMXPath($document);
        $query = $xpath->query("//testcase[@classname='$class' and @name='$method']");

        if ($query->length > 0) {
            return $query->item(0);
        }

        return null;
    }
}
