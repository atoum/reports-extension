<?php

namespace mageekguy\atoum\reports;

use mageekguy\atoum;

class twig extends asynchronous
{
    protected $twig;
    protected $score;

    public function __construct(\Twig_Environment $twig = null)
    {
        $this->setTwig($twig);
    }

    public function setTwig(\Twig_Environment $twig = null)
    {
        $this->twig = $twig ?: new \Twig_Environment();

        $this->twig->setLoader(new \Twig_Loader_Filesystem(array(__DIR__ . '/../resources/templates')));
        $this->twig->addFilter(new \Twig_SimpleFilter('rtrim', 'rtrim'));

        return $this;
    }
} 
