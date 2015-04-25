<?php

namespace mageekguy\atoum\reports\code;

use mageekguy\atoum\exceptions\runtime;

class line
{
    private $number;
    private $code;
    private $hit;

    public function __construct($number, $code, $hit = null)
    {
        $this->number = $number;
        $this->code = $code;
        $this->hit = $hit ?: 0;
    }

    public function __get($property)
    {
        switch ($property)
        {
            case 'number':
            case 'code':
            case 'hit':
                return $this->$property;
        }

        throw new runtime('Invalid property ' . $property);
    }

    public function __isset($property)
    {
        return in_array($property, array('number', 'code', 'hit'));
    }

    public function hasBeenCovered()
    {
        $this->hit = 1;

        return $this;
    }
}
