<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

use Stringable;

interface LinkResultInterface extends Stringable
{
    public function getDemand(): LinkDemandInterface;
}