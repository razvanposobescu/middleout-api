<?php

namespace App\Models;

use ArrayAccess;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionProperty;

/**
 * Base Model Functionality
 */
abstract class JsonModel implements JsonSerializable
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
     * New Json Model Instance
     *
     * @param array $fillable
     */
    public function __construct(array $fillable = [])
    {
        // fill model with data
        $this->fillModel($fillable);
    }

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
     * Returns array with Jsonable Columns aka columns that we want to expose
     *
     * @return array
     */
    protected abstract function toJson(): array;

    /**
     * Returns record as array
     *
     * @return array
     */
    abstract public function toArray(): array;

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
     * which json data we want?
     *
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        // todo: to be implemented
        return $this->toJson();
    }

    /**
     * Fill Model with data
     *
     * @param array $fillable
     * @return void
     * @throws EntryNotFoundException
     */
    private function fillModel(array $fillable): void
    {
        if (!empty($fillable))
        {
            foreach ($fillable as $property => $value)
            {
                // TODO: Implement a rudimentary contextual binding based on property type
                // $typeOf = new ReflectionProperty($this,  $property); $typeOf->getType()->getName();

                $this->__set($property, $value);
            }
        }
    }
}
