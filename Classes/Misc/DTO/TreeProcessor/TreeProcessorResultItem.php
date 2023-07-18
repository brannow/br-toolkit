<?php


namespace BR\Toolkit\Misc\DTO\TreeProcessor;


class TreeProcessorResultItem implements TreeProcessorResultItemInterface
{
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var TreeProcessorResultItemInterface[]
     */
    private $children = [];
    /**
     * @var null|TreeProcessorResultItemInterface
     */
    private $parent = null;

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param TreeProcessorResultItemInterface ...$children
     */
    public function addChild(TreeProcessorResultItemInterface ...$children): void
    {
        foreach ($children as $child) {
            $uid = spl_object_id($child);
            if (!isset($this->children[$uid])) {
                $this->children[$uid] = $child;
                $child->setParent($this);
            }
        }
    }

    /**
     * @return TreeProcessorResultItemInterface[]
     */
    public function getChildren(): array
    {
        return array_values($this->children);
    }

    public function getChildrenRecursive(): array
    {
        $childrenRecursive = [];
        foreach ($this->getChildren() as $child) {
            $childrenRecursive[] = $child;
            $childrenRecursive = array_merge($childrenRecursive, $child->getChildrenRecursive());
        }

        return $childrenRecursive;
    }

    /**
     * @param TreeProcessorResultItemInterface|null $parent
     */
    public function setParent(?TreeProcessorResultItemInterface $parent): void
    {
        $this->parent = $parent;
        $parent->addChild($this);
    }

    /**
     * @return TreeProcessorResultItemInterface|null
     */
    public function getParent(): ?TreeProcessorResultItemInterface
    {
        return $this->parent;
    }
}
