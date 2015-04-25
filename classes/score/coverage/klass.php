<?php

namespace mageekguy\atoum\reports\score\coverage;


class klass extends method
{
    private $methods;

    public function __construct($lines = null, $branches = null, $paths = null, $ops = null, $methods = null)
    {
        parent::__construct($lines, $branches, $paths, $ops);

        $this->methods = $methods;
    }

    public function __get($property)
    {
        if ($property === 'methods')
        {
            return $this->$property;
        }

        return parent::__get($property);
    }

    public function __isset($property)
    {
        return $property === 'methods' || parent::__isset($property);
    }
}
