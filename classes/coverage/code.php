<?php

namespace mageekguy\atoum\reports\coverage;

use
    mageekguy\atoum,
    mageekguy\atoum\reports\coverage,
    mageekguy\atoum\reports\score
;

class code extends coverage
{
    public function build($event)
    {
        if ($event === atoum\runner::runStop)
        {
            $this->render();

            $this->string = 'report built';
        }

        return $this;
    }

    private function render()
    {
        $files = $this->score->getClasses();
        $methods = $this->score->getMethods() ?: array();
        $branches = $this->score->getBranches() ?: array();
        $paths = $this->score->getPaths() ?: array();

        $classes = array_unique(
            array_merge(
                array_keys($methods ?: array()),
                array_keys($branches ?: array()),
                array_keys($paths ?: array())
            )
        );
        $data = array(
            'coverage' => new score\coverage(
                $this->score->getValue(),
                $this->score->getBranchesCoverageValue(),
                $this->score->getPathsCoverageValue()
            ),
            'classes' => array()
        );

        foreach ($classes as $class)
        {
            $classMethods = array_unique(
                array_merge(
                    array_keys(isset($methods[$class]) ? $methods[$class] : array()),
                    array_keys(isset($branches[$class]) ? $branches[$class] : array()),
                    array_keys(isset($paths[$class]) ? $paths[$class] : array())
                )
            );

            $data['classes'][$class] = array(
                'lines' => array_map(
                    function($line) {
                        return array(
                            'code' => $line,
                            'hit' => 0
                        );
                    },
                    file($files[$class])
                ),
                'methods' => array()
            );

            $classOps = 0;
            $coveredClassOps = 0;
            $coveredClassMethods = 0;

            foreach ($classMethods as $method)
            {
                $methodLines = isset($methods[$class][$method]) ? $methods[$class][$method] : null;
                $methodBranches = isset($branches[$class][$method]) ? $branches[$class][$method] : null;
                $methodPaths = isset($paths[$class][$method]) ? $paths[$class][$method] : null;

                $reflectedMethod = new \reflectionMethod($class, $method);
                $data['classes'][$class]['lines'][$reflectedMethod->getStartLine() - 1]['method'] = $method;

                foreach ($methodLines as $number => $hit)
                {
                     $data['classes'][$class]['lines'][$number - 1]['hit'] = $hit;
                }

                $branchIndex = 0;
                $lastOp = 0;
                $coveredOps = array();

                foreach ($methodBranches as $branch)
                {
                    if ($branch['op_end'] > $lastOp)
                    {
                        $lastOp = $branch['op_end'];
                    }

                    if ($branch['hit'] > 0) {
                        $coveredOps = array_unique(
                            array_merge(
                                $coveredOps,
                                range($branch['op_start'], $branch['op_end'])
                            )
                        );
                    }

                    ++$branchIndex;
                }

                $data['classes'][$class]['methods'][$method] = array(
                    'branches' => $methodBranches,
                    'paths' => $methodPaths,
                    'coverage' => new score\coverage\method(
                        $this->score->getValueForMethod($class, $method),
                        $this->score->getBranchesCoverageValueForMethod($class, $method),
                        $this->score->getPathsCoverageValueForMethod($class, $method),
                        sizeof($coveredOps) / ($lastOp + 1)
                    )
                );

                if ((int) $this->score->getValueForMethod($class, $method) === 1) {
                    $coveredClassMethods++;
                }

                $classOps += ($lastOp + 1);
                $coveredClassOps += sizeof($coveredOps);
            }

            $data['classes'][$class]['coverage'] = new score\coverage\klass(
                $this->score->getValueForClass($class),
                $this->score->getBranchesCoverageValueForClass($class),
                $this->score->getPathsCoverageValueForClass($class),
                $coveredClassOps / $classOps,
                $coveredClassMethods / sizeof($data['classes'][$class]['methods'])
            );

            $templateData = array(
                'class' => $class,
                'data' => $data['classes'][$class]
            );

            file_put_contents(str_replace('\\', '-', $class) . '.html', $this->twig->render('class.html.twig', $templateData));
            file_put_contents(str_replace('\\', '-', $class) . '-branch.html', $this->twig->render('branch.html.twig', $templateData));
            file_put_contents(str_replace('\\', '-', $class) . '-path.html', $this->twig->render('path.html.twig', $templateData));
        }

        file_put_contents('index.html', $this->twig->render('classes.html.twig', array(
            'data' => $data,
            'coverage' => $this->score->getValue()
        )));
    }
} 
