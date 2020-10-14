<?php


namespace BR\Toolkit\Misc\DTO\TreeProcessor;


use BR\Toolkit\Exceptions\InvalidArgumentException;

class TreeProcessorArrayData implements TreeProcessorDataInterface
{
    /**
     * @var string|int
     */
    private $primaryKey;
    /**
     * @var string|int
     */
    private $relationKey;
    /**
     * @var array[]
     */
    private $data;

    /**
     * TreeProcessorData constructor.
     * @param string|int $primaryKey
     * @param string|int $relationKey
     * @param array[] $data
     * @throws InvalidArgumentException
     */
    public function __construct($primaryKey, $relationKey, array $data)
    {
        $data = array_values($data);
        if (!$this->validateTreeData($primaryKey, $relationKey, $data)) {
            throw new InvalidArgumentException();
        }

        $this->data = $data;
        $this->primaryKey = $primaryKey;
        $this->relationKey = $relationKey;
    }

    /**
     * @param string|int $primaryKey
     * @param string|int $relationKey
     * @param array[] $data
     * @return bool
     */
    private function validateTreeData($primaryKey, $relationKey, array $data): bool
    {
        if (!is_scalar($primaryKey) || !is_scalar($relationKey) || is_array($primaryKey) || is_array($relationKey)) {
            return false;
        }
        // relation key is not mandatory
        return !(empty($data) || !array_key_exists($primaryKey, $data[0]));
    }

    /**
     * @param mixed $data
     * @return int
     */
    public function getPrimaryIdFromData($data): int
    {
        return (int)($data[$this->primaryKey]??0);
    }

    /**
     * @param mixed $data
     * @return int
     */
    public function getRelationIdFromData($data): int
    {
        return (int)($data[$this->relationKey]??0);
    }

    /**
     * @return \Iterator|array
     */
    public function getData()
    {
        return $this->data;
    }
}