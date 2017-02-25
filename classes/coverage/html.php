<?php

namespace mageekguy\atoum\reports\coverage;

use mageekguy\atoum;
use mageekguy\atoum\reports\coverage;
use mageekguy\atoum\reports\model;
use mageekguy\atoum\reports\score;

class html extends coverage
{
    private $indexTemplate;
    private $linesCoverageTemplate;
    private $branchesCoverageTemplate;
    private $pathsCoverageTemplate;
    private $outputDirectory;

    public function __construct()
    {
        parent::__construct();

        $this->indexTemplate = new atoum\reports\template('classes.html.twig');
        $this->linesCoverageTemplate = new atoum\reports\template('class.html.twig');
        $this->branchesCoverageTemplate = new atoum\reports\template('branch.html.twig');
        $this->pathsCoverageTemplate = new atoum\reports\template('path.html.twig');

        $this->setOutPutDirectory(getcwd() . DIRECTORY_SEPARATOR . 'coverage');
    }

    public function setOutPutDirectory($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
    }

    public function build($event)
    {
        if ($event === atoum\runner::runStop) {
            $this->render();

            $this->string .= 'Coverage report is available at file://' . realpath($this->outputDirectory) . '/index.html' . PHP_EOL;
        }

        return $this;
    }

    private function codeCoverageIsAvailable(model\coverage $coverage)
    {
        $coverage->renderTo($this->indexTemplate, $this->outputDirectory . DIRECTORY_SEPARATOR . 'index.html');

        return $this;
    }

    private function classCodeCoverageIsAvailable($class, model\coverage\klass $coverage)
    {
        $coverage
            ->renderTo($this->linesCoverageTemplate, $this->outputDirectory . DIRECTORY_SEPARATOR . str_replace('\\', '-', $class) . '.html')
            ->renderTo($this->branchesCoverageTemplate, $this->outputDirectory . DIRECTORY_SEPARATOR . str_replace('\\', '-', $class) . '-branch.html')
            ->renderTo($this->pathsCoverageTemplate, $this->outputDirectory . DIRECTORY_SEPARATOR . str_replace('\\', '-', $class) . '-path.html');

        return $this;
    }

    private function prepareOutputDIrectory()
    {
        if (is_dir($this->outputDirectory) === false) {
            @mkdir($this->outputDirectory, 0777, true);
        }

        if (is_dir($this->outputDirectory) === false) {
            throw new atoum\exceptions\runtime('Unable to create output directory for coverage report');
        }

        return $this;
    }

    private function render()
    {
        $this->prepareOutputDIrectory();

        $files = $this->score->getClasses();
        $methods = $this->score->getMethods() ?: [];
        $branches = $this->score->getBranches() ?: [];
        $paths = $this->score->getPaths() ?: [];
        $classes = array_unique(array_merge(array_keys($methods), array_keys($branches), array_keys($paths)));

        $coverageModel = new model\coverage();
        $coverageScore = new score\coverage();

        foreach ($classes as $className) {
            $classMethods = array_unique(
                array_merge(
                    array_keys(isset($methods[$className]) ? $methods[$className] : []),
                    array_keys(isset($branches[$className]) ? $branches[$className] : []),
                    array_keys(isset($paths[$className]) ? $paths[$className] : [])
                )
            );
            $classLines = file($files[$className]);
            $classModel = new model\coverage\klass($className);
            $classCoverage = new score\coverage();

            foreach ($classLines as $number => $line) {
                $classModel->addLine($number + 1, $line);
            }

            foreach ($classMethods as $methodName) {
                $methodLines = isset($methods[$className][$methodName]) ? $methods[$className][$methodName] : [];
                $methodBranches = isset($branches[$className][$methodName]) ? $branches[$className][$methodName] : [];
                $methodPaths = isset($paths[$className][$methodName]) ? $paths[$className][$methodName] : [];
                $reflectedMethod = new \reflectionMethod($className, $methodName);
                $startLineNumber = $reflectedMethod->getStartLine();
                $methodModel = new model\coverage\method($methodName, $methodBranches, $methodPaths);
                $methodCoverage = new score\coverage();

                $classModel->addLine($startLineNumber, rtrim($classLines[$startLineNumber - 1]), null, $methodName);
                $methodCoverage->linesAreAvailable($methodLines);
                $methodCoverage->pathsAreAvailable($methodPaths);

                foreach ($methodLines as $number => $hit) {
                    $classModel->addLine($number, rtrim($classLines[$number - 1]), $hit);
                }

                foreach ($methodBranches as $branch) {
                    $methodCoverage->branchIsAvailable($branch);
                }

                $methodCoverage->addToModel($methodModel);
                $methodModel->addToModel($classModel);

                $classCoverage = $classCoverage->merge($methodCoverage);
            }

            $classCoverage->addToModel($classModel);
            $classModel->addToModel($coverageModel);
            $this->classCodeCoverageIsAvailable($className, $classModel);

            $coverageScore = $coverageScore->merge($classCoverage);
        }

        $coverageScore->addToModel($coverageModel);
        $this->codeCoverageIsAvailable($coverageModel);

        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->mirror(__DIR__ . '/../../resources/html/assets', $this->outputDirectory . DIRECTORY_SEPARATOR . 'assets');
        $fs->mirror(__DIR__ . '/../../resources/html/fonts', $this->outputDirectory . DIRECTORY_SEPARATOR . 'fonts');
    }
}
