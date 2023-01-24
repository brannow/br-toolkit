<?php
declare(strict_types=1);
namespace BR\Toolkit\Typo3\Utility\Link\Demand;

class LinkResult implements LinkResultInterface
{
    private string $link;
    private LinkDemandInterface $demand;

    /**
     * @param string $link
     * @return static
     */
    public function setLink(string $link): LinkResultInterface
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @param LinkDemandInterface $demand
     * @return static
     */
    public function setDemand(LinkDemandInterface $demand): LinkResultInterface
    {
        $this->demand = $demand;
        return $this;
    }

    public function getDemand(): LinkDemandInterface
    {
        return $this->demand;
    }

    public function __toString(): string
    {
        return $this->link;
    }
}