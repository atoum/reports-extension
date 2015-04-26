<?php

namespace mageekguy\atoum\reports\score;

use mageekguy\atoum\reports\model;

class coverage
{
    private $totalLines;
    private $coveredLines;
    private $totalBranches;
    private $coveredBranches;
    private $totalPaths;
    private $coveredPaths;
    private $totalOps;
    private $coveredOps;
    private $additionalCoveredOps;

    public function branchIsAvailable(array $branch)
    {
        $this->totalBranches++;

        if ($this->totalOps === null)
        {
            $this->totalOps = 1;
        }

        if ($branch['op_end'] + 1 > $this->totalOps)
        {
            $this->totalOps = $branch['op_end'] + 1;
        }

        if ($branch['hit'] > 0)
        {
            $this->coveredBranches++;

            $this->coveredOps = array_unique(
                array_merge(
                    $this->coveredOps ?: array(),
                    range($branch['op_start'], $branch['op_end'])
                )
            );
        }

        return $this;
    }

    public function pathsAreAvailable(array $paths)
    {
        $this->totalPaths += sizeOf($paths);

        $this->coveredPaths += sizeof(array_filter($paths, function($path) { return $path['hit'] > 0; }));

        return $this;
    }

    public function linesAreAvailable(array $lines)
    {
        $this->totalLines += sizeof(array_filter($lines, function($hit) { return $hit > -2; }));
        $this->coveredLines += sizeof(array_filter($lines, function($hit) { return $hit > 0; }));

        return $this;
    }

    public function merge(self $coverage)
    {
        $merged = clone $this;

        if ($coverage->totalLines !== null)
        {
            $merged->totalLines += $coverage->totalLines;
            $merged->coveredLines += $coverage->coveredLines;
        }

        if ($coverage->totalBranches !== null)
        {
            $merged->totalBranches += $coverage->totalBranches;
            $merged->coveredBranches += $coverage->coveredBranches;
        }

        if ($coverage->totalPaths !== null)
        {
            $merged->totalPaths += $coverage->totalPaths;
            $merged->coveredPaths += $coverage->coveredPaths;
        }

        if ($coverage->totalOps !== null)
        {
            $merged->totalOps += $coverage->totalOps;
            $merged->additionalCoveredOps += sizeof($coverage->coveredOps ?: array()) + $coverage->additionalCoveredOps;
        }

        return $merged;
    }

    public function addToModel(model $model)
    {
        $model->coverageIs(
            $this->totalLines, $this->coveredLines,
            $this->totalBranches, $this->coveredBranches,
            $this->totalPaths, $this->coveredPaths,
            $this->totalOps, sizeof($this->coveredOps ?: array()) + $this->additionalCoveredOps
        );

        return $this;
    }
}
