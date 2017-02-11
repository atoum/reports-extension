<?php

namespace mageekguy\atoum\reports;

abstract class model
{
    protected $coverage;

    public function coverageIs($totalLines, $coveredLines, $totalBranches, $coveredBranches, $totalPaths, $coveredPaths, $totalOps, $coveredOps)
    {
        $this->coverage = [
            'totalLines' => $totalLines,
            'coveredLines' => $coveredLines,
            'lines' => $totalLines !== null ? $coveredLines / $totalLines : null,
            'totalBranches' => $totalBranches,
            'coveredBranches' => $coveredBranches,
            'branches' => $totalBranches !== null ? $coveredBranches / $totalBranches : null,
            'totalPaths' => $totalPaths,
            'coveredPaths' => $coveredPaths,
            'paths' => $totalPaths > 0 ? $coveredPaths / $totalPaths : null,
            'totalOps' => $totalOps,
            'coveredOps' => $coveredOps,
            'ops' => $totalOps > 0 ? $coveredOps / $totalOps : null
        ];

        return $this;
    }

    abstract public function renderTo(template $template, $destination);
}
