<?php

namespace MekDrop\FlatLeadsDB\Models;

use Imponeer\ToArrayInterface;

/**
 * Defines base model
 *
 * @package MekDrop\FlatLeadsDB\Models
 */
abstract class AbstractModel implements ToArrayInterface
{
    private $data = [];

    /**
     * Gets validation rules in form [field => rules]
     *
     * @return array
     */
    abstract public function getValidationRules(): array;

    /**
     * Gets ID field name
     *
     * @return string
     */
    abstract public function getIdName(): string;

    /**
     * Gets model property
     *
     * @param string $name Name of property
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->data[$name]) && $this->__isset($name)) {
            return null;
        }
        return $this->data[$name];
    }

    /**
     * Set model property
     *
     * @param string $name Name of model property
     * @param mixed $value Value of property
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Checks if property is set
     *
     * @param string $name Name of property
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->getValidationRules()[$name]);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $ret = [];
        foreach(array_keys($this->getValidationRules()) as $column) {
            $ret[$column] = $this->data[$column] ?? null;
        }
        return $ret;
    }
}