<?php

namespace mageekguy\atoum\reports\code;

use mageekguy\atoum\exceptions\runtime;

class branch
{
    private $number;
    private $hit;

    public function __construct($number, $hit = null)
    {
        $this->number = $number;
        $this->hit = $hit ?: 0;
    }

    public function __get($property)
    {
        switch ($property)
        {
            case 'number':
            case 'hit':
                return $this->$property;
        }

        throw new runtime('Invalid property ' . $property);
    }

    public function __isset($property)
    {
        return in_array($property, array('number', 'hit'));
    }
}
