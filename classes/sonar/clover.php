<?php

namespace mageekguy\atoum\reports\sonar;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;

class clover extends atoum\reports\asynchronous
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
        $this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

        return parent::handleEvent($event, $observable);
    }

    public function build($event)
    {
        $this->string = '';

        if ($event === atoum\runner::runStop) {
            $this->title = $this->title ?: self::defaultTitle;

            $document = new \DOMDocument('1.0', 'UTF-8');
            $document->formatOutput = true;
            $document->appendChild($root = $document->createElement('coverage'));
            $root->setAttribute('version', "1");

            $coverage = $this->score->getCoverage();

            foreach ($coverage->getClasses() as $class => $file) {
                $root->appendChild($testSuite = $document->createElement('file'));
                $testSuite->setAttribute('path', $file);

                foreach ($coverage->getCoverageForClass($class) as $lines) {
                    foreach ($lines as $line => $covered) {
                        $testCase = $document->createElement('lineToCover');
                        $testCase->setAttribute('lineNumber', $line);
                        $testCase->setAttribute('covered', ($covered === 1) ? "true" : "false");
                        $testSuite->appendChild($testCase);
                    }
                }
            }

            $this->string = $document->saveXML();
        }

        return $this;
    }
}
