<?php

namespace atoum\atoum\reports;

use atoum\atoum;

class coverage extends asynchronous
{
    protected $score;
    protected $coverage;

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore()->getCoverage());

        return parent::handleEvent($event, $observable);
    }
}
