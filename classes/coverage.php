<?php

namespace mageekguy\atoum\reports;

use mageekguy\atoum;

class coverage extends twig
{
    protected $coverage;

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore()->getCoverage());

        return parent::handleEvent($event, $observable);
    }
} 
