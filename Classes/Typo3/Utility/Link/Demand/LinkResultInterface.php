<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

interface LinkResultInterface
{
    public function getDemand(): LinkDemandInterface;
    public function __toString(): string;
}