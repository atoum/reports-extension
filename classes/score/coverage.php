<?php

namespace mageekguy\atoum\reports\score;

use mageekguy\atoum\exceptions\runtime;

class coverage
{
    private $lines;
    private $branches;
    private $paths;

    public function __construct($lines = null, $branches = null, $paths = null)
    {
        $this->lines = $lines;
        $this->branches = $branches;
        $this->paths = $paths;
    }

    public function __get($property)
    {
        switch ($property)
        {
            case 'lines':
            case 'branches':
            case 'paths':
                return $this->$property;
        }

        throw new runtime('Invalid property ' . $property);
    }

    public function __isset($property)
    {
        return in_array($property, array('lines', 'branches', 'paths'));
    }
}
