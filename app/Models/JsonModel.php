<?php

namespace App\Models;

use ArrayAccess;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Base Model Functionality
 */
abstract class JsonModel implements JsonSerializable, Jsonable
{
    /**
     * DB Table
     *
     * @var string|null
     */
    protected static ?string $table = null;

    /**
     * DB Columns
     *
     * @var array|null
     */
    protected static ?array $columns = [];

    /**
     * Get DB Table
     *
     * @return string|null
     */
    public static function getTable(): ?string
    {
        return static::$table;
    }

    /**
     * Get Columns
     *
     * @return array|null
     */
    public static function getColumns(): ?array
    {
        return static::$columns;
    }

    /**
     * @throws EntryNotFoundException
     */
    public function __get($name)
    {
        if (property_exists($this, $name))
        {
            return $this->$name;
        }

        throw new EntryNotFoundException("Property '{$name}' not found in " . get_class($this));
    }

    /**
     * @param $name
     * @param $value
     * @throws EntryNotFoundException
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name))
        {
            $this->$name = $value;
        }
        else
        {
            throw new EntryNotFoundException("Property '{$name}' not found in " . get_class($this));
        }
    }

    /**
     * To Array
     *
     * @return array
     */
    public function toArray(): array
    {
        // todo: to be implemented
        return [];
    }

    /**
     * To Json
     *
     * @param int $options
     * @return false|string
     */
    public function toJson($options = 0): bool|string
    {
        // todo: to be implemented
        return false;
    }

    /**
     * which json data we want?
     *
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        // todo: to be implemented
        return [];
    }
}
