<?php

namespace atoum\atoum\reports;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class template
{
    private $source;
    private $twig;

    public function __construct($source)
    {
        $this->source = $source;

        $this->twig = new Environment(new FilesystemLoader([__DIR__ . '/../resources/html/templates']));
    }

    public function render(array $model, $destination)
    {
        file_put_contents($destination, $this->twig->render($this->source, $model));

        return $this;
    }
}
