<?php

namespace mageekguy\atoum\reports;

class template
{
    private $source;
    private $twig;

    public function __construct($source)
    {
        $this->source = $source;

        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem([__DIR__ . '/../resources/html/templates']));
    }

    public function render(array $model, $destination)
    {
        file_put_contents($destination, $this->twig->render($this->source, $model));

        return $this;
    }
}
