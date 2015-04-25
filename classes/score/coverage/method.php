<?php

namespace mageekguy\atoum\reports\score\coverage;

use mageekguy\atoum\reports\score\coverage;

class method extends coverage
{
    private $ops;

    public function __construct($lines = null, $branches = null, $paths = null, $ops = null)
    {
        parent::__construct($lines, $branches, $paths);

        $this->ops = $ops;
    }

    public function __get($property)
    {
        if ($property === 'ops')
        {
            return $this->$property;
        }

        return parent::__get($property);
    }

    public function __isset($property)
    {
        return $property === 'ops' || parent::__isset($property);
    }
}
