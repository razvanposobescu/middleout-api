<?php

namespace App\Services;

/**
 * Services Base functionality
 */
abstract class Service
{
    /**
     * Make a new instance of the class
     *
     * if used in combination with the Singleton trait this method will be overwritten by the trait
     *
     * @param mixed ...$args
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }
}
